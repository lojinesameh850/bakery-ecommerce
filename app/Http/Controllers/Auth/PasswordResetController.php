<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Twilio\Rest\Client;

class PasswordResetSMSController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'exists:users,phone'],
        ]);

        // Rate limiting - only allow 1 request per minute per phone
        $key = 'password_reset_' . $request->input('phone');
        if (Cache::has($key)) {
            return response()->json([
                'message' => 'Please wait before requesting another code.'
            ], 429);
        }

        $user = User::where('phone', $request->input('phone'))->first();

        // Generate cryptographically secure code
        $resetCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store with expiration
        DB::table('password_resets')->updateOrInsert(
            ['phone' => $user->phone],
            [
                'token' => Hash::make($resetCode),
                'created_at' => now(),
            ]
        );

        // Rate limit for 1 minute
        Cache::put($key, true, now()->addMinutes(1));

        // Send SMS
        if (app()->environment('local')) {
            // Development - just log, don't send SMS
            \Log::info("SMS to {$user->phone}: Your password reset code is: $resetCode");
            
            return response()->json([
                'status' => __('Password reset code sent via SMS.'),
                'expires_in' => 15 // minutes
            ]);
        } else {
            // Production - send real SMS
            try {
                $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
                
                $twilio->messages->create(
                    $user->phone, // to
                    [
                        'from' => config('services.twilio.from'),
                        'body' => "Your password reset code is: $resetCode"
                    ]
                );
                
                return response()->json([
                    'status' => __('Password reset code sent via SMS.'),
                    'expires_in' => 15 // minutes
                ]);
            } catch (\Exception $e) {
                \Log::error("Twilio SMS error: " . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to send SMS. Please try again.'
                ], 500);
            }
        }
    }

    /**
     * Verify the reset code and update password.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $reset = DB::table('password_resets')
            ->where('phone', $request->phone)
            ->first();

        if (!$reset || !Hash::check($request->code, $reset->token)) {
            throw ValidationException::withMessages([
                'code' => ['Invalid or expired reset code.']
            ]);
        }

        // Check if code is expired (15 minutes)
        if (now()->diffInMinutes($reset->created_at) > 15) {
            DB::table('password_resets')->where('phone', $request->phone)->delete();
            throw ValidationException::withMessages([
                'code' => ['Reset code has expired.']
            ]);
        }

        // Update password
        $user = User::where('phone', $request->phone)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Clean up
        DB::table('password_resets')->where('phone', $request->phone)->delete();

        return response()->json(['status' => __('Password reset successfully.')]);
    }
}