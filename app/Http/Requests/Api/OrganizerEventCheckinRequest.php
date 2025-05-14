<?php

namespace App\Http\Requests\Api;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class OrganizerEventCheckinRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $event = $this->route('event');
        // Validasi user harus organizer dan pembuat event
        return $user && $user->role === 'organizer' && $user->organizer && $event && $user->organizer->id === $event->organizer_id;
    }

    public function rules(): array
    {
        return [
            'kode_tiket' => 'required|string',
        ];
    }
}
