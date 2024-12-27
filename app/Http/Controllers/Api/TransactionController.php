<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Event;
use App\Models\Resale;
use App\Models\TicketIssued;
use App\Models\Transaction;
use App\Services\Transaction\PaymentService;
use App\Services\Transaction\StoreOwnerAndPaymentService;
use App\Services\Transaction\StorePrimaryTransactionService;
use App\Services\Transaction\StoreSecondaryTransactionService;
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
        // secondary ticket transaction
        if ($request->has('ticket_source') && $request->get('ticket_source') === 'secondary') {
            $resale = Resale::find($request->resale_id);
            // tambahkan pembatasan akses bahwa user pembeli tidak sama dengan user yang menjual
            if ($resale->ticketIssued->user_id === $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            // tambahkan pembatasan akses jika ada transaksi yang sudah dibuat menggunakan resale tersebut maka tidak bisa membuat transaksi baru kecuali user login sama dengan user yang di transaksi tersebut
            if ($resale->transaction) {
                if ($resale->transaction->user_id !== $request->user()->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaction already created',
                    ], 400);
                }

                if ($resale->transaction->user_id === $request->user()->id) {
                    $transaction = $resale->transaction;
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaction already created',
                        'data' => $transaction
                    ], 200);
                }
            }

            $secondaryTransactionService = new StoreSecondaryTransactionService();
            $transaction = $secondaryTransactionService->execute($request, $resale);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction created',
                'data' => $transaction
            ], 201);
        }

        // primary ticket transaction
        $primaryTransactionService = new StorePrimaryTransactionService();
        $transaction = $primaryTransactionService->execute($request);

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
        return response()->json([
            'status' => 'success',
            'message' => 'Transaction detail',
            'data' => $transaction
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Transaction $transaction, UpdateTransactionRequest $request)
    {
        if ($transaction->status === 'pending') {
            $service = new StoreOwnerAndPaymentService();

            $transaction = $service->handle(
                $transaction, 
                $request->metode_pembayaran, 
                $request->user(), 
                $request->has('ticket_issueds') ? $request->ticket_issueds : []
            );

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create payment',
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction updated',
                'data' => $transaction
            ], 200);
        }

        if ($transaction->status === 'payment') {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction already in payment process',
            ], 400);
        }

        if ($transaction->status === 'success') {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction already success',
            ], 400);
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
