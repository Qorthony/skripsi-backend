<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckinController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PaymentNotificationController;
use App\Http\Controllers\Api\ResaleController;
use App\Http\Controllers\Api\TicketIssuedController;
use App\Http\Controllers\Api\TransactionController;
use App\Services\Midtrans\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello World']);
});

Route::post('/midtrans/notification', [PaymentNotificationController::class, 'handleNotification']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/verifyOtp', [AuthController::class, 'registerVerifyOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/verifyOtp', [AuthController::class, 'loginVerifyOtp']);


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
    Route::apiResource('transactions', TransactionController::class)->only('index','store','show','update');
    Route::apiResource('ticket-issued', TicketIssuedController::class)->only('index','show','update');
    Route::apiResource('events.resales', ResaleController::class)->only('index','show','destroy');
    Route::apiResource('checkins', CheckinController::class)->only('index','update');
});
