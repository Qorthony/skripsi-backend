<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Event/Index', [
            'events' => Event::where('organizer_id', Auth::user()->organizer->id)->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Event/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi' => 'required|string|in:online,offline',
            'kota' => 'required_if:lokasi,offline|nullable|string|max:255',
            'alamat_lengkap' => 'required_if:lokasi,offline|nullable|string|max:255',
            'tautan_acara' => 'required_if:lokasi,online|nullable|string|max:255',
            'jadwal_mulai' => 'required|date',
            'jadwal_selesai' => 'required|date',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['organizer_id'] = Auth::user()->organizer->id;
        $validated['status'] = 'draft';
        // dd($validated);

        $event = Event::create($validated);

        return redirect()->route('events.show', $event->id);
    }

    public function show(Event $event)
    {
        return Inertia::render('Event/Show', [
            'event' => $event
        ]);
    }

    public function edit(Event $event)
    {
        return Inertia::render('Event/Form', [
            'event' => $event
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lokasi' => 'required|string|in:online,offline',
            'kota' => 'required_if:lokasi,offline|nullable|string|max:255',
            'alamat_lengkap' => 'required_if:lokasi,offline|nullable|string|max:255',
            'tautan_acara' => 'required_if:lokasi,online|nullable|string|max:255',
            'jadwal_mulai' => 'required|date',
            'jadwal_selesai' => 'required|date',
            'deskripsi' => 'nullable|string',
        ]);

        $event->update($validated);

        return redirect()->route('events.show', $event->id);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index');
    }

    public function publish(Event $event)
    {
        $event->update(['status' => 'published']);

        return redirect()->route('events.show', $event->id);
    }
}
