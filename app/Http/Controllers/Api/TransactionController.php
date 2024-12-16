<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Event;
use App\Models\TicketIssued;
use App\Models\Transaction;
use App\Services\Transaction\PaymentService;
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
    public function update(Transaction $transaction, UpdateTransactionRequest $request)
    {
        // $ticketIssueds = [];
        // foreach ($request->ticket_issueds as $ticket) {
        //     $ticketIssueds[] = [
        //     'id' => $ticket['id'],
        //     'transaction_id'=>$transaction->id,
        //     'user_id' => isset($ticket['pemesan']) && $ticket['pemesan'] ? $request->user()->id : null,
        //     'email_penerima' => $ticket['email_penerima'],
        //     'aktif' => isset($ticket['pemesan']) && $ticket['pemesan'] ? true : false,
        //     'waktu_penerbitan' => isset($ticket['pemesan']) && $ticket['pemesan'] ? now() : null,
        //     ];
        // }
        // return $ticketIssueds;
        if ($transaction->status === 'pending') {
            $paymentService = new PaymentService();
        
            $payment = $paymentService->createTransaction(
                $transaction->id.'-'.time(), 
                $transaction->total_harga, 
                $request->metode_pembayaran, 
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
            $paymentDetail = $paymentService->getPaymentDetail($request->metode_pembayaran, $payment);
            // dd($paymentDetail);
            // return $payment;


            // // update payment and ticket owner
            DB::transaction(function () use ($request, $transaction, $payment ,$paymentDetail) {
                $transaction->update([
                    'status' => 'payment',
                    'kode_pembayaran' => $payment->order_id,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'detail_pembayaran'=>$paymentDetail,
                    'batas_waktu' => now()->addMinutes(15),
                    'total_pembayaran' => (int) $transaction->total_harga+5000,
                ]);

                foreach ($request->ticket_issueds as $ticket) {
                    TicketIssued::find($ticket['id'])->update([
                        'user_id' => isset($ticket['pemesan']) && $ticket['pemesan'] ? $request->user()->id : null,
                        'email_penerima' => $ticket['email_penerima'],
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction updated',
                'data' => $transaction
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Transaction already paid',
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
