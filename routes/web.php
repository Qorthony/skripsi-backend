<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        // Tambahan data untuk konteks project event organizer
        'appDescription' => 'Platform manajemen event untuk penyelenggara dan peserta. Buat, kelola, dan promosikan event Anda dengan mudah.',
        'features' => [
            'Buat dan kelola event secara online',
            'Manajemen tiket dan peserta',
            'Publikasi event dan sistem verifikasi',
            'Dashboard khusus penyelenggara',
        ],
    ]);
})->name('home');

Route::group([
    'middleware' => ['auth', 'verified'],
    'prefix' => 'dashboard',
], function () {

    Route::get('/organizer', [OrganizerController::class, 'index'])->name('organizer.index');

    Route::post('/organizer', [OrganizerController::class, 'store'])->name('organizer.store');

    Route::middleware('hasOrganizer')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::put('/organizer', [OrganizerController::class, 'update'])->name('organizer.update');

        Route::get('/events', [EventController::class, 'index'])->name('events.index');

        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');

        Route::post('/events', [EventController::class, 'store'])->name('events.store');

        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');

        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        
        Route::post('/events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');

        Route::post('/events/{event}/publish/cancel', [EventController::class, 'cancelPublish'])->name('events.cancelPublish');

        Route::post('/events/{event}/ticket', [EventController::class, 'storeTicket'])->name('events.ticket.store');

        Route::put('/events/ticket/{ticket}', [EventController::class, 'updateTicket'])->name('events.ticket.update');

        Route::delete('/events/ticket/{ticket}', [EventController::class, 'destroyTicket'])->name('events.ticket.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin & Organizer: daftar transaksi per event & detail transaksi
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/events/{event}/transactions', [TransactionController::class, 'byEvent'])->name('events.transactions.index');
    Route::get('/dashboard/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
});

require __DIR__.'/auth.php';

require __DIR__.'/admin.php';
