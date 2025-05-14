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
Route::post('/register/resendOtp', [AuthController::class, 'resendRegisterOtp']);
Route::post('/register/verifyOtp', [AuthController::class, 'registerVerifyOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/resendOtp', [AuthController::class, 'resendLoginOtp']);
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
    Route::patch('transactions/{id}/expired', [TransactionController::class, 'expired'])->name('transactions.expired');

    Route::apiResource('ticket-issued', TicketIssuedController::class)->only('index','show','update');
    // Get ticket issued data for checkin (by id, not kode_tiket)
    Route::get('ticket-issued/{id}/checkin', [TicketIssuedController::class, 'checkin'])->name('ticket-issued.checkin');
    
    Route::apiResource('resales', ResaleController::class)->only('index','show','update');
    
    Route::group(['prefix' => 'organizer'], function () {
        // Organizer event management
        Route::get('events', [\App\Http\Controllers\Api\OrganizerEventController::class, 'index']);
        Route::get('events/{event}', [\App\Http\Controllers\Api\OrganizerEventController::class, 'show']);
        Route::get('events/{event}/transactions', [\App\Http\Controllers\Api\OrganizerEventController::class, 'transactions']);
        Route::get('events/{event}/participants', [\App\Http\Controllers\Api\OrganizerEventController::class, 'participants']);
        Route::post('events/{event}/checkin', [\App\Http\Controllers\Api\OrganizerEventController::class, 'checkin']);
    });
});
