<?php

namespace App\Services\Transaction;

use App\Models\TicketIssued;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Midtrans\TransactionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use function Illuminate\Log\log;

class StoreOwnerAndPaymentService
{
    public function handle(Transaction $transaction, string $metode_pembayaran, User $user, array $ticketIssueds=[])
    {
        $paymentService = new PaymentService();
        
        $payment = $paymentService->createTransaction(
            $transaction->id.'-'.time(), 
            $transaction->total_harga, 
            $metode_pembayaran, 
            $transaction->transactionItems->map(function ($item) use ($transaction) {
                return [
                    'id' => $item->id,
                    'price' => $item->harga_satuan,
                    'quantity' => $item->jumlah,
                    'name' => $item->nama,
                ];
            })
            ->toArray()
        );

        log('payment response', [$payment]);

        if ($payment->status_code != Response::HTTP_CREATED) {
            return null;
        }
        $paymentDetail = $paymentService->getPaymentDetail($metode_pembayaran, $payment);
        // dd($paymentDetail);


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
                'biaya_pembayaran' => 5000,
                'total_pembayaran' => (int) $transaction->total_harga+5000,
            ]);

            // jika resale_id tidak null maka lewati proses update ticket issued
            if ($transaction->resale_id) {
                return;
            }

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