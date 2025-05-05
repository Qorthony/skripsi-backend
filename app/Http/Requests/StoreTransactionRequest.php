<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'participant';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->request->has('ticket_source') && $this->request->get('ticket_source') === 'secondary') {
            return [
                'resale_id' => 'required|exists:resales,id',
            ];
        } else {
            return [
                'event_id' => 'required|exists:events,id',
                'selected_ticket' => 'required|array',
                'selected_ticket.*.id' => 'required|exists:tickets,id',
                'selected_ticket.*.quantity' => 'required|numeric|min:1',
            ];
        }
    }
}
