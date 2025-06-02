<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GateKeeper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GateKeeperController extends Controller
{
    public function index(Event $event)
    {
        // Pastikan event milik organizer yang login
        if ($event->organizer_id !== Auth::user()->organizer->id) {
            abort(403, 'Unauthorized');
        }

        $gatekeepers = $event->gatekeepers()->get();

        return Inertia::render('Event/GateKeeper/Index', [
            'event' => $event,
            'gatekeepers' => $gatekeepers
        ]);
    }

    public function create(Event $event)
    {
        // Pastikan event milik organizer yang login
        if ($event->organizer_id !== Auth::user()->organizer->id) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Event/GateKeeper/Create', [
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

        GateKeeper::create($validated);

        return redirect()->route('events.gatekeepers.index', $event)
            ->with('success', 'Gate Keeper berhasil ditambahkan');
    }

    public function edit(Event $event, GateKeeper $gatekeeper)
    {
        // Pastikan event milik organizer yang login dan gatekeeper milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $gatekeeper->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Event/GateKeeper/Edit', [
            'event' => $event,
            'gatekeeper' => $gatekeeper
        ]);
    }

    public function update(Request $request, Event $event, GateKeeper $gatekeeper)
    {
        // Pastikan event milik organizer yang login dan gatekeeper milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $gatekeeper->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:gate_keepers,email,' . $gatekeeper->id,
        ]);

        $gatekeeper->update($validated);

        return redirect()->route('events.gatekeepers.index', $event)
            ->with('success', 'Gate Keeper berhasil diperbarui');
    }

    public function destroy(Event $event, GateKeeper $gatekeeper)
    {
        // Pastikan event milik organizer yang login dan gatekeeper milik event ini
        if ($event->organizer_id !== Auth::user()->organizer->id || $gatekeeper->event_id !== $event->id) {
            abort(403, 'Unauthorized');
        }

        $gatekeeper->delete();

        return redirect()->route('events.gatekeepers.index', $event)
            ->with('success', 'Gate Keeper berhasil dihapus');
    }
}
