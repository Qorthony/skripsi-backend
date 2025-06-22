<?php

namespace App\Http\Requests\Api;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class OrganizerEventDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $event = $this->route('event');
        // Pastikan user adalah organizer dan event milik organizer tsb
        if(
            $user 
                && $user->role === 'organizer' 
                && $event 
                && $event->organizer_id === $user->organizer->id
        )
        {
            return true;
        }

        if(
            $user 
            && $user->kode_akses
            && $user->event_id === $event->id
        )
        {
            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [];
    }
}
