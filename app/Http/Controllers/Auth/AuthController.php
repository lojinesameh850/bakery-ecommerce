<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
    {
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'min:13',
                'max:13',
                'unique:users,phone',
                'regex:/^\+201\d{9}$/'
            ],
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        // Prevent registering if already in DB
        if (User::where('phone', $request->phone)->exists()) {
            return response()->json([
                'message' => 'Phone number already registered',
            ], 422);
        }

        // Prevent re-sending within 15 min
        if (Cache::has('pending_registration_' . $request->phone)) {
            return response()->json([
                'message' => 'Verification code already sent. Please verify or wait.',
            ], 422);
        }

        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Temporarily store user data
        Cache::put('pending_registration_' . $request->phone, [
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => $request->password,
            'verification_code' => $verificationCode,
        ], now()->addMinutes(15));

        // TODO: Send SMS

        return response()->json([
            'message' => 'Verification code sent to your phone',
            'phone' => $request->phone,
            'verification_code' => $verificationCode // Remove in production
        ], 200);
    }

    public function verifyAndCompleteRegistration(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'verification_code' => 'required|string|size:6',
        ]);

        $pendingData = Cache::get('pending_registration_' . $request->phone);

        if (!$pendingData) {
            return response()->json([
                'message' => 'No pending registration found or verification code expired',
            ], 404);
        }

        if ($pendingData['verification_code'] !== $request->verification_code) {
            return response()->json([
                'message' => 'Invalid verification code',
            ], 400);
        }

        $user = User::create([
            'name' => $pendingData['name'],
            'phone' => $pendingData['phone'],
            'password' => Hash::make($pendingData['password']),
        ]);

        Cache::forget('pending_registration_' . $request->phone);

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('phone', 'password'))) {
            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user()
            ]);
        }

        throw ValidationException::withMessages([
            'phone' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $pendingData = Cache::get('pending_registration_' . $request->phone);

        if (!$pendingData) {
            return response()->json([
                'message' => 'No pending registration found',
            ], 404);
        }

        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pendingData['verification_code'] = $verificationCode;

        // Update cache with new code
        Cache::put('pending_registration_' . $request->phone, $pendingData, now()->addMinutes(15));

        // TODO: Send SMS with new verification code

        return response()->json([
            'message' => 'New verification code sent',
            'verification_code' => $verificationCode, // Remove this in production
        ], 200);
    }
}