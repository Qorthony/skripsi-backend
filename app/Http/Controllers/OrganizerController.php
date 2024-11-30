<?php

namespace App\Http\Controllers;

use App\Models\Organizer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class OrganizerController extends Controller
{
    public function index()
    {
        return Inertia::render('Organizer/Index', [
            'organizer' => Auth::user()->organizer,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::user()->id;

        Organizer::create($validated);

        return redirect()->route('dashboard');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        Auth::user()->organizer->update($validated);

        return redirect()->route('organizer.index');
    }
}
