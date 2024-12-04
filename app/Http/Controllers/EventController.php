<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Event/Index', [
            'events' => Event::where('organizer_id', Auth::user()->organizer->id)->latest()->get()
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

        $event = Event::create($validated);

        return redirect()->route('events.edit', $event->id);
    }

    public function show(Event $event)
    {
        return Inertia::render('Event/Show', [
            'event' => $event,
            'tickets' => $event->tickets
        ]);
    }

    public function edit(Event $event)
    {
        return Inertia::render('Event/Form', [
            'event' => $event,
            'tickets' => $event->tickets
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
        $event->update(['status' => 'in_review', 'alasan_penolakan' => null]);

        return redirect()->route('events.show', $event->id);
    }

    public function cancelPublish(Event $event)
    {
        $event->update(['status' => 'draft', 'alasan_penolakan' => null]);

        return redirect()->route('events.show', $event->id);
    }

    public function storeTicket(Request $request, Event $event)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
            'harga' => 'nullable|integer|min:1',
            'waktu_buka' => 'required|date',
            'waktu_tutup' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        if ($validated['harga'] == null) {
            $validated['harga'] = 0;
        }

        $event->tickets()->create($validated);

        return redirect()->back();
    }

    public function updateTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
            'harga' => 'nullable|integer|min:1',
            'waktu_buka' => 'required|date',
            'waktu_tutup' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $ticket->update($validated);

        return redirect()->back();
    }

    public function destroyTicket(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->back();
    }
}
