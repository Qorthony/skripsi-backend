<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        Collaborator::create($validated);

        return redirect()->route('events.collaborators.index', $event)
            ->with('success', 'Collaborator berhasil ditambahkan');
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
}
