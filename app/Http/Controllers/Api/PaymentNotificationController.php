<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;

class PaymentNotificationController
{
    public function handleNotification()
    {
        $payload = request()->all();
        $transactionId = $payload['order_id'];
        $transaction = Transaction::where('kode_pembayaran', $transactionId)->first();
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if($payload['transaction_status'] == 'settlement') {
            $transaction->update([
                'status' => 'success',
                'waktu_pembayaran' => now(),
            ]);
        } else if($payload['transaction_status'] == 'expire') {
            $transaction->update([
                'status' => 'failed',
            ]);
        } else if($payload['transaction_status'] == 'cancel') {
            $transaction->update([
                'status' => 'failed',
            ]);
        }
        return response()->json(['message' => 'Notification handled']);
    }
}