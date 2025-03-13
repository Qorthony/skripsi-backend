<?php

namespace App\Services\Transaction;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketIssued;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class StorePrimaryTransactionService
{
    public function execute($request) : Transaction
    {
        $event = Event::with('tickets')->find($request->event_id);

        $priceTotal = 0;
        $ticketCount = 0;
        $transactionItems = [];
        // $ticketIds = [];
        
        foreach ($request->selected_ticket as $ticket) {
            // match request data with db data
            $dbTicket = $event->tickets->where('id', $ticket['id'])->first();

            // calculate price total
            $priceTotal += $dbTicket->harga * $ticket['quantity'];

            // calculate ticket count in general
            $ticketCount += $ticket['quantity'];

            // push transaction item
            $transactionItems[] = [
                'ticket_id' => $dbTicket->id,
                'nama' => $dbTicket->nama,
                'deskripsi' => $dbTicket->keterangan,
                'harga_satuan' => $dbTicket->harga,
                'jumlah' => $ticket['quantity'],
                'total_harga' => $dbTicket->harga * $ticket['quantity'],
            ];

            // push ticket id to array based on quantity
            // for($i = 0; $i < $ticket['quantity']; $i++) {
            //     $ticketIds[] = ['ticket_id' => $dbTicket->id];
            // }
        }
        
        $transaction = DB::transaction(function () use ($request, $priceTotal, $ticketCount, $transactionItems) {
            $transaction = Transaction::create([
                'event_id' => $request->event_id,
                'user_id' => $request->user()->id,
                'jumlah_tiket' => $ticketCount,
                'total_harga' => $priceTotal,
                'batas_waktu' => now()->addMinutes(15),
                'status' => 'pending',
            ]);

            $items = $transaction->transactionItems()->createMany($transactionItems);

            // create many ticket issued by using transaction items id in items variable 
            $items->each(function ($item){
                $itemIds = [];
                for($i = 0; $i < $item->jumlah; $i++) {
                    $itemIds[] = ['transaction_item_id' => $item->id];
                }

                $item->ticketIssueds()->createMany($itemIds);
            });

            return $transaction;
        });

        return $transaction;
    }
}