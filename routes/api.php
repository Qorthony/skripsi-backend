<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TransactionController;
use App\Services\Midtrans\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello World']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/midtrans/transaction/{orderId}', function ($orderId) {
    $transactionService = new TransactionService();
    $transaction = $transactionService->getTransaction($orderId);
    return response()->json($transaction);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::apiResource('events', EventController::class)->only('index','show');
    Route::apiResource('transactions', TransactionController::class)->only('store','show','update');
});
