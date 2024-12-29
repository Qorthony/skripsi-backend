<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TicketIssued;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Checkin list',
            'data' => TicketIssued::with(['user','ticket'])->where('status', 'checkin')->get(),
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticketIssued = TicketIssued::find($id);

        if (!$ticketIssued) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found',
            ], 404);
        }

        // kondisi role user yang login harus organizer dan merupakan pembuat event
        if (auth()->user()->role !== 'organizer' || auth()->id() !== $ticketIssued->transaction->event->organizer->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        // buat kondisi error atau tiket tidak valid jika status tiket bukan active atau checkin
        if ($ticketIssued->status !== 'active' && $ticketIssued->status !== 'checkin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not valid',
            ], 400);
        }

        // kondisi jika tiket sudah checkin dan di tabel checkin data terakhir bukan checkout, maka response error
        if ($ticketIssued->status === 'checkin' && $ticketIssued->checkins->last()->checked_out_at === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket already checkin',
            ], 400);
        }


        $ticketIssued->update([
            'status' => 'checkin',
        ]);

        $ticketIssued->checkins()->create([
            'user_id' => auth()->id(),
            'checked_in_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Checkin success',
            'data' => $ticketIssued,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
