<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * The user instance from the query.
     */
    protected ?User $queriedUser = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->queriedUser = User::where('email', $this->request->get('email'))->first(); // Simpan user hasil query
        
        if ($this->queriedUser) {
            $role = $this->request->input('as', 'participant'); // Ambil role dari request, default 'participant'
            // kondisi user role hanya boleh participant atau organizer
            if ($role === 'participant') {
                return $this->queriedUser->role === 'participant';
            } elseif ($role === 'organizer') {
                return $this->queriedUser->role === 'organizer';
            }
        }
        return false;
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
        ];
    }

    /**
     * Get the queried user from the authorize method.
     */
    public function getQueriedUser(): ?User
    {
        return $this->queriedUser;
    }
}