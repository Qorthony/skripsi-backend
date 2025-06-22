<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\LoginVerifyOtpRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\RegisterVerifyOtpRequest;
use App\Models\GateKeeper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\SendOtp;
use App\Services\OtpService;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, OtpService $otpService)
    {
        if ($request->isEmailAlreadyVerified()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already registered'
            ], 409);
        }

        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'role' => 'participant'
            ]
        );

        $otp = $otpService->generateOtp($request->email, OtpService::PURPOSE_REGISTER);

        Notification::send($user, new SendOtp($otp));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP code has been sent to your email',
            'user' => $user,
        ], 201);
    }

    public function resendRegisterOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not registered'
            ], 404);
        }
        if ($user->email_verified_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already verified'
            ], 409);
        }

        $otp = $otpService->generateOtp($request->email, OtpService::PURPOSE_REGISTER);

        Notification::send($user, new SendOtp($otp));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP code has been resent to your email',
            'user' => $user,
        ], 200);
    }

    public function registerVerifyOtp(RegisterVerifyOtpRequest $request, OtpService $otpService)
    {
        $isOtpValid = $request->verifyOtp($otpService);

        if (!$isOtpValid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $user->email_verified_at = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'OTP code is valid',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function login(LoginRequest $request, OtpService $otpService)
    {
        // Ambil user dari FormRequest yang sudah diquery sebelumnya
        $user = $request->getQueriedUser();
        
        // Karena auth sudah dilakukan di FormRequest, kita bisa langsung lanjut ke generate OTP
        $otp = $otpService->generateOtp($request->email, OtpService::PURPOSE_LOGIN);

        Notification::send($user, new SendOtp($otp));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP code has been sent to your email',
            'user' => $user,
        ], 200);
    }

    public function resendLoginOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not registered'
            ], 404);
        }

        $otp = $otpService->generateOtp($request->email, OtpService::PURPOSE_LOGIN);

        Notification::send($user, new SendOtp($otp));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP code has been resent to your email',
            'user' => $user,
        ], 200);
    }

    public function loginVerifyOtp(LoginVerifyOtpRequest $request, OtpService $otpService)
    {
        $isOtpValid = $otpService->verifyOtp($request->email, $request->otp_code, OtpService::PURPOSE_LOGIN);

        if (!$isOtpValid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        if ($user->role == 'organizer') {
            $user->load('organizer');
        }

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

    public function gateKeeperAccess(Request $request)
    {
        $request->validate([
            'event_id' => 'required|uuid',
            'kode_akses' => 'required|uuid'
        ]);

        $gateKeeper = GateKeeper::where('event_id', $request->event_id)
            ->where('kode_akses', $request->kode_akses)
            ->first();

        if (!$gateKeeper) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid access code or event not found'
            ], 404);
        }

        $token = $gateKeeper->createToken('gate_keeper_token', [
            'organizer:events',
            'organizer:checkin',
            'organizer:attendance',
        ])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Access granted',
            'gate_keeper' => $gateKeeper,
            'token' => $token
        ], 200);
    }
}
