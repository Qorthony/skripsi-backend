<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TransactionController extends Controller
{
    
    // Daftar 10 transaksi terbaru (admin only)
    public function index()
    {
        if (!Gate::allows('view-any-transaction')) {
            abort(403);
        }

        $transactions = Transaction::with('event', 'user')->latest()->take(10)->get();
        $stats = [
            'count' => Transaction::count(),
        ];

        return Inertia::render('Admin/TransactionIndex', [
            'transactions' => $transactions,
            'stats' => $stats,
        ]);
    }

    // Daftar transaksi berdasarkan event (admin & organizer)
    public function byEvent(Request $request, Event $event)
    {
        $response = Gate::inspect('view-event-transaction', $event);
        if( !$request->access_code) {
            if ($response->denied()) {
                abort(403);
            }
        }

        $transactions = Transaction::with('user', 'event')
            ->where('event_id', $event->id)
            ->latest()->get();

        $stats = [
            'count' => $transactions->count(),
            'total_income' => $transactions->sum('total_harga'),
            'tickets_by_category' => $event->tickets()->withCount('transactionItems')->get()->map(function($ticket) {
                return [
                    'category' => $ticket->nama,
                    'price' => $ticket->harga,
                    'count' => $ticket->transaction_items_count,
                ];
            }),
        ];

        return Inertia::render('Event/TransactionList', [
            'event' => $event,
            'transactions' => $transactions,
            'stats' => $stats,
        ]);
    }

    // Detail transaksi (admin & organizer)
    public function show(Request $request, Event $event ,Transaction $transaction)
    {
        $response = Gate::inspect('view-transaction', $transaction);
        if( !$request->access_code) {
            if ($response->denied()) {
                abort(403);
            }
        }

        $transaction->load('user', 'event.tickets.transactionItems');
        
        return Inertia::render('Transaction/Show', [
            'event' => $event,
            'transaction' => $transaction,
        ]);
    }
}
