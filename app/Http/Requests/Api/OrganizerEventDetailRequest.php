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
        return $user 
                && $user->role === 'organizer' 
                && $event 
                && $event->organizer_id === $user->organizer->id;
    }

    public function rules(): array
    {
        return [];
    }
}
