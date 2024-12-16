<?php

namespace App\Services\Transaction;

use App\Services\Midtrans\TransactionService;

class PaymentService
{
    protected $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    public function createTransaction(string $id, int $amount,string $paymentType , array $items)
    {
        $data = [
            'transaction_details'=> [
                'order_id' => $id,
                'gross_amount' => $amount,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'aa@aa.com',
            ],
        ];

        $data = array_merge($data, $this->getPaymentType($paymentType));

        $transaction = $this->transactionService->createTransaction($data);

        return $transaction;
    }

    private function getPaymentType($paymentType)
    {
        $paymentList = [
            'bri'=>$this->getBRIData(),
            'bca'=>$this->getBCAData(),
            'permata'=>$this->getPermataData(),
            'echannel'=>$this->getMandiriData(),
            'bni'=>$this->getBNIData(),
        ];

        return $paymentList[$paymentType];
    }

    private function getBRIData()
    {
        return [
            'payment_type' => 'bank_transfer',
            'bank_transfer' => [
                'bank' => 'bri',
            ],
        ];
    }

    private function getBCAData()
    {
        return [
            'payment_type' => 'bank_transfer',
            'bank_transfer' => [
                'bank' => 'bca',
            ],
        ];
    }

    private function getPermataData()
    {
        return [
            'payment_type' => 'bank_transfer',
            'bank_transfer' => [
                'bank' => 'permata',
            ],
        ];
    }

    private function getMandiriData()
    {
        return [
            'payment_type' => 'echannel',
            'echannel' => [
                'bill_info1' => 'Payment For:',
                'bill_info2' => 'Top Up Wallet',
            ],
        ];
    }

    private function getBNIData()
    {
        return [
            'payment_type' => 'bank_transfer',
            'bank_transfer' => [
                'bank' => 'bni',
                'va_number' => '1234567890',
            ],
        ];
    }

    public function getTransaction($orderId)
    {
        $transaction = $this->transactionService->getTransaction($orderId);

        return $transaction;
    }

    public function getPaymentDetail(string $paymentType, object $res)
    {
        if (in_array($paymentType, ['bri','bni','bca','cimb'])) {
            return [
                'bank'=>$res->va_numbers[0]->bank,
                'va_number'=>$res->va_numbers[0]->va_number
            ];
        }

        if ($paymentType === 'echannel') {
            return [
                'bill_key'=>$res->bill_key,
                'biller_code'=>$res->biller_code
            ];
        }

        if ($paymentType === 'permata') {
            return [
                'permata_va_number'=>$res->permata_va_number
            ];
        }

        return [];
    }
}