<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
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
        if ($this->transaction->status === 'pending') {
            return [
                'metode_pembayaran' => 'required|string|max:50',
                'ticket_issueds' => 'required|array|',
                'ticket_issueds.*.id' => 'required|exists:ticket_issueds,id',
                'ticket_issueds.*.email_penerima' => 'required|email|max:255',
                'ticket_issueds.*.pemesan'=>'sometimes|boolean'
            ];
        }

        return [];
    }
}
