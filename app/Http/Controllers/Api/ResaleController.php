<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Resale;
use Illuminate\Http\Request;

class ResaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'index',
            'data' => $event->transactionItems()
                        ->with(['ticketIssueds', 'ticketIssueds.resale'])
                        ->whereHas('ticketIssueds.resale', function($query) {
                            $query->where('status', 'active');
                        })
                        ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Resale::create([
            'ticket_issued_id' => $request->ticket_issued_id,
            'harga' => $request->harga,
            'status' => 'active',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Resale $resale)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'show',
            'data' => $event->transactionItems()
                        ->with(['ticketIssueds.resale', 'ticket'])
                        ->whereHas('ticketIssueds.resale', function($query) use ($resale) {
                            $query->where('resales.id', $resale->id);
                        })
                        ->first(),
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
