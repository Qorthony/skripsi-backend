<?php

namespace App\Services\Transaction;

use App\Models\TicketIssued;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Midtrans\TransactionService;
use Illuminate\Support\Facades\DB;

class StoreOwnerAndPaymentService
{
    public function handle(Transaction $transaction, string $metode_pembayaran, User $user, array $ticketIssueds)
    {
        $paymentService = new PaymentService();
        
        $payment = $paymentService->createTransaction(
            $transaction->id.'-'.time(), 
            $transaction->total_harga, 
            $metode_pembayaran, 
            $transaction->ticketIssued->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'price' => $ticket->ticket->harga,
                    'quantity' => 1,
                    'name' => $ticket->ticket->nama,
                ];
            })
            ->toArray()
        );
        $paymentDetail = $paymentService->getPaymentDetail($metode_pembayaran, $payment);
        // dd($paymentDetail);
        // return $payment;


        // // update payment and ticket owner
        DB::transaction(function () 
        use ($metode_pembayaran, $transaction, $payment ,$paymentDetail, $ticketIssueds, $user) 
        {
            $transaction->update([
                'status' => 'payment',
                'kode_pembayaran' => $payment->order_id,
                'metode_pembayaran' => $metode_pembayaran,
                'detail_pembayaran'=>$paymentDetail,
                'batas_waktu' => now()->addMinutes(15),
                'total_pembayaran' => (int) $transaction->total_harga+5000,
            ]);

            foreach ($ticketIssueds as $ticket) {
                TicketIssued::find($ticket['id'])->update([
                    'user_id' => isset($ticket['pemesan']) && $ticket['pemesan'] ? $user->id : null,
                    'email_penerima' => $ticket['email_penerima'],
                ]);
            }
        });

        return $transaction;
    }
}