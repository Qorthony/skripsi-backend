<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GateKeeperController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Event;
use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/.health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');

Route::get('/.well-known/assetlinks.json', function () {
    return response()->json([
        [
            "relation" => ["delegate_permission/common.handle_all_urls"],
            "target" => [
                "namespace" => "android_app",
                "package_name" => "com.qorthony.skripsi",
                "sha256_cert_fingerprints" => [
                    "E5:D7:C9:0C:B5:6B:33:72:97:87:FC:9C:8B:5F:8A:FC:2F:0C:C0:FD:63:75:C6:B9:86:16:91:87:59:93:B6:25"
                ]
            ]
        ],
        [
            "relation" => ["delegate_permission/common.handle_all_urls"],
            "target" => [
                "namespace" => "android_app",
                "package_name" => "com.qorthony.skripsi.preview",
                "sha256_cert_fingerprints" => [
                    "66:93:DC:A2:73:AE:62:71:59:65:9F:AA:3E:8D:56:2F:47:DF:86:C9:59:F8:20:D5:DF:8D:42:D9:82:A7:DB:8A"
                ]
            ]
        ],
        [
            "relation" => ["delegate_permission/common.handle_all_urls"],
            "target" => [
                "namespace" => "android_app",
                "package_name" => "com.qorthony.skripsi.dev",
                "sha256_cert_fingerprints" => [
                    "AC:FF:AD:63:C6:9E:A5:3F:F8:B2:8F:EA:72:DA:60:3B:B5:D1:3A:0A:1D:1F:50:35:F8:B1:EF:8F:F3:7E:E1:B7"
                ]
            ]
        ]
    ]);
})->withoutMiddleware([HandleInertiaRequests::class,AddLinkHeadersForPreloadedAssets::class])
  ->name('assetlinks');


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

Route::get('/link', function () {
    return Inertia::render('MobileLink',[
        'intentLink'=>'intent://#Intent;scheme=skripsi;package=com.qorthony.skripsi.preview;end',
        'downloadLink'=>'https://expo.dev/accounts/qorthony/projects/skripsi/builds/77d96a6b-a71e-4786-afc2-0e3ddf67aa14'
    ]);
})->name('mobilelink');

Route::get('/link/events/{event}', function ($event) {
    return Inertia::render('MobileLink',[
        'intentLink'=>'intent://events/'.$event.'#Intent;scheme=skripsi;package=com.qorthony.skripsi.preview;end',
        'downloadLink'=>'https://expo.dev/accounts/qorthony/projects/skripsi/builds/77d96a6b-a71e-4786-afc2-0e3ddf67aa14'
    ]);
})->name('mobilelink.event');

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

        // Collaborator routes
        Route::get('/events/{event}/collaborators', [CollaboratorController::class, 'index'])->name('events.collaborators.index');
        Route::get('/events/{event}/collaborators/create', [CollaboratorController::class, 'create'])->name('events.collaborators.create');
        Route::post('/events/{event}/collaborators', [CollaboratorController::class, 'store'])->name('events.collaborators.store');
        Route::get('/events/{event}/collaborators/{collaborator}/edit', [CollaboratorController::class, 'edit'])->name('events.collaborators.edit');
        Route::put('/events/{event}/collaborators/{collaborator}', [CollaboratorController::class, 'update'])->name('events.collaborators.update');
        Route::delete('/events/{event}/collaborators/{collaborator}', [CollaboratorController::class, 'destroy'])->name('events.collaborators.destroy');
        Route::post('/events/{event}/collaborators/{collaborator}/resend-invitation', [CollaboratorController::class, 'resendInvitation'])->name('events.collaborators.resend-invitation');

        // GateKeeper routes
        Route::get('/events/{event}/gatekeepers', [GateKeeperController::class, 'index'])->name('events.gatekeepers.index');
        Route::get('/events/{event}/gatekeepers/create', [GateKeeperController::class, 'create'])->name('events.gatekeepers.create');
        Route::post('/events/{event}/gatekeepers', [GateKeeperController::class, 'store'])->name('events.gatekeepers.store');
        Route::get('/events/{event}/gatekeepers/{gatekeeper}/edit', [GateKeeperController::class, 'edit'])->name('events.gatekeepers.edit');
        Route::put('/events/{event}/gatekeepers/{gatekeeper}', [GateKeeperController::class, 'update'])->name('events.gatekeepers.update');
        Route::delete('/events/{event}/gatekeepers/{gatekeeper}', [GateKeeperController::class, 'destroy'])->name('events.gatekeepers.destroy');

        // Attendance routes
        Route::get('/events/{event}/attendance', [AttendanceController::class, 'index'])->name('events.attendance.index');
        // Route::post('/events/{event}/attendance/{ticketIssued}/checkin', [AttendanceController::class, 'checkin'])->name('events.attendance.checkin');
        // Route::post('/events/{event}/attendance/{ticketIssued}/checkout', [AttendanceController::class, 'checkout'])->name('events.attendance.checkout');
        // Route::get('/events/{event}/attendance/{ticketIssued}/history', [AttendanceController::class, 'history'])->name('events.attendance.history');
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

Route::middleware(['collaborator'])
    ->prefix('collaborator/events/{event}')
    ->group(function () {
        Route::get('/', [EventController::class, 'show'])->name('events.show.collaborator');
        Route::get('/transactions', [TransactionController::class, 'byEvent'])->name('events.transactions.index.collaborator');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show.collaborator');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('events.attendance.index.collaborator');
    });

require __DIR__.'/auth.php';

require __DIR__.'/admin.php';
