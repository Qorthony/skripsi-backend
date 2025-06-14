<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ongoing = $request->query('ongoing', false);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Event list',
            'data' => Event::with('tickets')
                        ->when($ongoing, function ($query) {
                            return $query->where('jadwal_mulai', '>=', now());
                        })
                        ->where('status', 'published')
                        ->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        if ($event->status !== 'published') {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found or not published'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Event detail',
            'data' => $event->load('tickets')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
