<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Event;
use App\Notifications\CollaboratorInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CollaboratorController extends Controller
{
    public function index(Event $event)
    {
        // Pastikan event milik organizer yang login
        if ($event->organizer_id !== Auth::user()->organizer->id) {
            abort(403, 'Unauthorized');
        }

        $collaborators = $event->collaborators;

        return Inertia::render('Event/Collaborator/Index', [
            'event' => $event,
            'collaborators' => $collaborators
        ]);
    }

    public function create(Event $event)
    {
        // Pastikan event milik organizer yang login
        if ($event->organizer_id !== Auth::user()->organizer->id) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Event/Collaborator/Create', [
            'event' => $event
        ]);
    }

    public function store(Request $request, Event $event)
    {
        // Pastikan event milik organizer yang login
        if ($event->organizer_id !== Auth::user()->organizer->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);       
        $validated['event_id'] = $event->id;
        $validated['kode_akses'] = Str::uuid();        
        $collaborator = Collaborator::create($validated);
        
        // Load relasi event untuk access_link
        $collaborator->load('event');

        // Kirim email undangan ke collaborator
        Notification::route('mail', $collaborator->email)
            ->notify(new CollaboratorInvitation($collaborator));

        return redirect()->route('events.collaborators.index', $event)
            ->with('success', 'Collaborator berhasil ditambahkan dan email undangan telah dikirim');
    }

    public function show(Event $event, Collaborator $collaborator)
    {
        // Pastikan event milik organizer yang login dan collaborator milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $collaborator->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Event/Collaborator/Show', [
            'event' => $event,
            'collaborator' => $collaborator
        ]);
    }

    public function edit(Event $event, Collaborator $collaborator)
    {
        // Pastikan event milik organizer yang login dan collaborator milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $collaborator->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Event/Collaborator/Edit', [
            'event' => $event,
            'collaborator' => $collaborator
        ]);
    }

    public function update(Request $request, Event $event, Collaborator $collaborator)
    {
        // Pastikan event milik organizer yang login dan collaborator milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $collaborator->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $collaborator->update($validated);

        return redirect()->route('events.collaborators.index', $event)
            ->with('success', 'Collaborator berhasil diperbarui');
    }

    public function destroy(Event $event, Collaborator $collaborator)
    {
        // Pastikan event milik organizer yang login dan collaborator milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $collaborator->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        $collaborator->delete();        
        
        return redirect()->route('events.collaborators.index', $event)
            ->with('success', 'Collaborator berhasil dihapus');
    }

    public function resendInvitation(Event $event, Collaborator $collaborator)
    {
        // Pastikan event milik organizer yang login dan collaborator milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $collaborator->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        // Load relasi event untuk access_link
        $collaborator->load('event');

        // Kirim ulang email undangan
        Notification::route('mail', $collaborator->email)
            ->notify(new CollaboratorInvitation($collaborator));

        return redirect()->route('events.collaborators.index', $event)
            ->with('success', 'Email undangan berhasil dikirim ulang ke ' . $collaborator->email);
    }
}
