<?php

namespace App\Services\Transaction;

use App\Models\Resale;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class StoreSecondaryTransactionService
{
    public function execute($request, Resale $resale): Transaction
    {
        $transaction = DB::transaction(function () use ($resale, $request) {
            $resale->update([
                'status' => 'booked',
            ]);

            $transaction = Transaction::create([
                'event_id' => $resale->ticketIssued->ticket->event_id,
                'user_id' => $request->user()->id,
                'jumlah_tiket' => 1,
                'total_harga' => $resale->harga_jual,
                'batas_waktu' => now()->addMinutes(15),
                'status' => 'pending',
                'resale_id' => $resale->id,
            ]);

            $transaction->ticketIssued()->create([
                'ticket_id' => $resale->ticketIssued->ticket_id,
                'user_id' => $request->user()->id,
                'email_penerima' => $request->user()->email,
            ]);

            return $transaction;
        });

        return $transaction;
    }
}