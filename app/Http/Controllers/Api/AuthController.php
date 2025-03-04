<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\SendOtp;
use App\Services\OtpService;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role'=>'participant'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|string|email',
            // 'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            !$user 
            // || !Hash::check($request->password, $user->password) 
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // kondisi user role hanya boleh participant atau organizer
        if ($user->role !== 'participant' && $user->role !== 'organizer') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized role'
            ], 401);
        }

        $otp = $otpService->generateOtp($request->email);

        Notification::send($user, new SendOtp($otp));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP code has been sent to your email',
            'user' => $user,
        ], 200);
    }

    public function loginVerifyOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp_code' => 'required|string'
        ]);

        $isOtpValid = $otpService->verifyOtp($request->email, $request->otp_code);

        if (!$isOtpValid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'OTP code is valid',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully'
        ], 200);
    }
}
