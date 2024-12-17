<?php

namespace App\Services\Midtrans;

use Illuminate\Support\Facades\Http;

class TransactionService
{
    protected $serverKey;
    protected $clientKey;
    protected $endpoint;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $isProduction = config('services.midtrans.is_production');
        $this->endpoint = $isProduction ? config('services.midtrans.production_endpoint') : config('services.midtrans.sandbox_endpoint');
    }

    public function getServerKey()
    {
        return $this->serverKey;
    }

    public function getClientKey()
    {
        return $this->clientKey;
    }

    public function createTransaction($data)
    {
        try {
            $transaction = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Override-Notification' => config('services.midtrans.notification_url'),
            ])->post($this->endpoint . 'charge', $data);
    
            return json_decode($transaction);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getTransaction($orderId)
    {
        $transaction = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get($this->endpoint . $orderId . '/status');

        return json_decode($transaction);
    }
}
