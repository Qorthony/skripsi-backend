<?php

namespace App\Services;

use App\Models\OneTimePassword;
use Illuminate\Support\Str;

class OtpService
{
    public function generateOtp(string $identifier, int $length = 6, int $minutes = 2): string
    {
        $otpCode = str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes($minutes); // Masa berlaku 5 menit

        // Simpan OTP ke database
        OneTimePassword::updateOrCreate(
            ['identifier' => $identifier],
            [
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt
            ]
        );

        return $otpCode;
    }

    public function verifyOtp(string $identifier, string $otpCode): bool
    {
        $otp = OneTimePassword::where('identifier', $identifier)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>=', now())
            ->first();

        if ($otp) {
            $otp->delete();
            return true;
        }

        return false;
    }

    public function invalidateOtp(string $identifier): void
    {
        OneTimePassword::where('identifier', $identifier)->delete();
    }
}