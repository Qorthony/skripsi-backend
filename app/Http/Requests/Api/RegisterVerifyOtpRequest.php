<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Services\OtpService;

class RegisterVerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'otp_code' => 'required|string',
            'device_id'=> 'nullable',
        ];
    }
    
    /**
     * Verify the OTP code
     * 
     * @param OtpService $otpService
     * @return bool
     */
    public function verifyOtp(OtpService $otpService)
    {
        return $otpService->verifyOtp(
            $this->email, 
            $this->otp_code, 
            OtpService::PURPOSE_REGISTER
        );
    }
}
