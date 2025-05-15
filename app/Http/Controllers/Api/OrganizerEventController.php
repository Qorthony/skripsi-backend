<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrganizerEventDetailRequest;
use App\Http\Requests\Api\OrganizerEventCheckinRequest;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\TicketIssued;
use App\Models\Organizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizerEventController extends Controller
{
    // 1. List events milik organizer
    public function index(Request $request)
    {
        $user = $request->user();
        $ongoing = $request->query('ongoing');
        $organizer = $user->organizer;

        
        $query = Event::where('organizer_id', $organizer->id);
        
        $order = 'desc';

        if ($ongoing) {
            $query->where('jadwal_mulai', '>=', now());
            $order = 'asc';
        }
        
        $events = $query->with('tickets')->orderBy('jadwal_mulai', $order)->get();
        
        return response()->json(['status' => 'success', 'data' => $events]);
    }

    // 2. Detail event milik organizer
    public function show(OrganizerEventDetailRequest $request, Event $event)
    {
        return response()->json(['status' => 'success', 'data' => $event->load('tickets')]);
    }

    // 3. Daftar transaksi event
    public function transactions(OrganizerEventDetailRequest $request, Event $event)
    {
        $status = $request->query('status');
        $order = $request->query('order', 'desc');
        $query = $event->transactions()->with('user');

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->orderBy('created_at', $order)->get();
        
        return response()->json(['status' => 'success', 'data' => $transactions]);
    }

    // 4. Daftar peserta event (berdasarkan ticket issued yang sudah selesai) beserta data checkin
    public function participants(OrganizerEventDetailRequest $request, Event $event)
    {
        $search = $request->query('search');
        $query = TicketIssued::whereHas('transactionItem.ticket', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->whereHas('transactionItem.transaction', function ($q) {
                $q->where('status', 'success');
            })
            ->whereIn('status', ['inactive', 'active', 'resale'])
            ->with([
                'transactionItem.transaction.user',
                'transactionItem.ticket',
                'checkins',
                'user',
            ]);
        
        // Jika ada parameter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                // Pencarian berdasarkan data user (jika ada)
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                // Pencarian berdasarkan email tiket issued
                ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $participants = $query->get();
            
        return response()->json([
            'status' => 'success', 
            'data' => [
                'event' => $event,
                'participants' => $participants,
            ]
        ]);
    }

    // 5. Proses checkin berdasarkan kode tiket
    public function checkin(OrganizerEventCheckinRequest $request, Event $event)
    {
        // Validasi user harus organizer dan pembuat event
        $user = request()->user();

        $kodeTiket = $request['kode_tiket'];
        
        $ticketIssued = TicketIssued::whereHas('transactionItem.ticket', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->where('kode_tiket', $kodeTiket)
            ->first();

        if (!$ticketIssued) {
            return response()->json(['status' => 'error', 'message' => 'Kode tiket tidak ditemukan'], 404);
        }

        // Validasi status tiket harus active atau checkin
        if (!in_array($ticketIssued->status, ['active', 'checkin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not valid',
            ], 400);
        }

        // Jika tiket sudah checkin dan checkin terakhir belum checkout, error
        $lastCheckin = $ticketIssued->checkins->last();
        if ($ticketIssued->status === 'checkin' && $lastCheckin && $lastCheckin->checked_out_at === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket already checkin',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $ticketIssued->update([
                'status' => 'checkin',
            ]);

            $ticketIssued->checkins()->create([
                'user_id' => $user->id,
                'checked_in_at' => now()
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Checkin gagal',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Checkin berhasil'
        ]);
    }
}
