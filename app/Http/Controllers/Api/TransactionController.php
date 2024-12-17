<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Event;
use App\Models\TicketIssued;
use App\Models\Transaction;
use App\Services\Transaction\PaymentService;
use App\Services\Transaction\StoreOwnerAndPaymentService;
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
    public function show(Transaction $transaction)
    {
        $paymentService = new PaymentService();

        $paymentData = $paymentService->getTransaction($transaction->kode_pembayaran);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction detail',
            'data' => $paymentData
        ], 200);
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
            $service = new StoreOwnerAndPaymentService();

            $transaction = $service->handle(
                $transaction, 
                $request->metode_pembayaran, 
                $request->user(), 
                $request->ticket_issueds
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction updated',
                'data' => $transaction
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Transaction already updated',
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
