<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'phone' => ['required', 'string'], // Changed from email to phone
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Since we're using phone-based reset, we need to handle this differently
        // Laravel's Password::reset() is designed for email-based resets
        // We'll implement custom logic for phone-based password reset
        
        $reset = \DB::table('password_resets')
            ->where('phone', $request->phone)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid or expired reset token.'],
            ]);
        }

        // Check if token is expired (15 minutes)
        if (now()->diffInMinutes($reset->created_at) > 15) {
            \DB::table('password_resets')->where('phone', $request->phone)->delete();
            throw ValidationException::withMessages([
                'phone' => ['Reset token has expired.'],
            ]);
        }

        // Find user and update password
        $user = \App\Models\User::where('phone', $request->phone)->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => ['User not found.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->string('password')),
            'remember_token' => Str::random(60),
        ])->save();

        // Fire the password reset event
        event(new PasswordReset($user));

        // Clean up the reset token
        \DB::table('password_resets')->where('phone', $request->phone)->delete();

        return response()->json(['status' => 'Password has been reset successfully.']);
    }
}