<?php

namespace App\Services;

use App\Models\OneTimePassword;
use Illuminate\Support\Str;

class OtpService
{
    // Constants for OTP purposes
    public const PURPOSE_LOGIN = 'login';
    public const PURPOSE_REGISTER = 'register';
    public const PURPOSE_RESET_PASSWORD = 'reset_password';

    public function generateOtp(string $identifier, string $purpose, int $length = 6, int $minutes = 2): string
    {
        $otpCode = str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes($minutes); // Masa berlaku 2 menit

        // Simpan OTP ke database dengan purpose spesifik
        OneTimePassword::updateOrCreate(
            ['identifier' => $identifier, 'purpose' => $purpose],
            [
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt
            ]
        );

        return $otpCode;
    }

    public function verifyOtp(string $identifier, string $otpCode, string $purpose): bool
    {
        $otp = OneTimePassword::where('identifier', $identifier)
            ->where('otp_code', $otpCode)
            ->where('purpose', $purpose)
            ->where('expires_at', '>=', now())
            ->first();

        if ($otp) {
            $otp->delete();
            return true;
        }

        return false;
    }

    public function invalidateOtp(string $identifier, ?string $purpose): void
    {
        $query = OneTimePassword::where('identifier', $identifier);
        
        if ($purpose) {
            $query->where('purpose', $purpose);
        }
        
        $query->delete();
    }
}