<?php

namespace App\Services\Transaction;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class StorePrimaryTransactionService
{
    public function execute($request) : Transaction
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

        return $transaction;
    }
}