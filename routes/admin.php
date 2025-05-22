<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventSubmissionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['prefix' => 'admin'], function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'store'])->name('admin.login.store');
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.index');

        Route::get('/event-submission', [EventSubmissionController::class, 'index'])->name('admin.event-submission.index');
        Route::get('/event-submission/{event}', [EventSubmissionController::class, 'show'])->name('admin.event-submission.show');
        Route::post('/event-submission/{event}/approve', [EventSubmissionController::class, 'approve'])->name('admin.event-submission.approve');
        Route::post('/event-submission/{event}/reject', [EventSubmissionController::class, 'reject'])->name('admin.event-submission.reject');
        Route::get('/transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('admin.transactions.index');
    });
});
