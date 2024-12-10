<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Event;
use App\Models\TicketIssued;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $event = Event::with('tickets')->find($request->event_id);

        $priceTotal = 0;
        $ticketCount = 0;
        $ticketIds = [];
        foreach ($request->selected_ticket as $ticket) {
            $dbTicket = $event->tickets->where('id', $ticket['id'])->first();
            $priceTotal += $dbTicket->harga * $ticket['quantity'];
            $ticketCount += $ticket['quantity'];
            // push ticket id to array based on quantity
            for($i = 0; $i < $ticket['quantity']; $i++) {
                $ticketIds[] = ['ticket_id' => $dbTicket->id];
            }
        }
        
        $transaction = DB::transaction(function () use ($request, $priceTotal, $ticketCount, $ticketIds) {
            $transaction = Transaction::create([
                'event_id' => $request->event_id,
                'user_id' => $request->user()->id,
                'jumlah_tiket' => $ticketCount,
                'total_harga' => $priceTotal,
                'batas_waktu' => now()->addMinutes(15),
                'status' => 'pending',
            ]);

            $transaction->ticketIssued()->createMany($ticketIds);

            return $transaction;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction created',
            'data' => $transaction
        ], 201);
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
