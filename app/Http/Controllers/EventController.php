<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
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

        return redirect()->route('events.index');
    }
}
