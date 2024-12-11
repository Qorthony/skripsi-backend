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
        return $this->request->all();
        return [
            'metode_pembayaran' => 'required|string',
            'ticket_issued' => 'required|array',
            'ticket_issued.*.id' => 'required|exists:ticket_issueds,id',
            'ticket_issued.*.email_penerima' => 'required|email',
        ];
    }
}
