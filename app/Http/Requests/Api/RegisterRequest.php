<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ];
    }

    /**
     * Check if email is already registered and verified
     * 
     * @return User|null
     */
    public function getQueriedUser()
    {
        return User::where('email', $this->email)->first();
    }

    /**
     * Check if the email is already registered with a verified account
     * 
     * @return bool
     */
    public function isEmailAlreadyVerified()
    {
        $user = $this->getQueriedUser();
        return $user && $user->email_verified_at !== null;
    }
}
