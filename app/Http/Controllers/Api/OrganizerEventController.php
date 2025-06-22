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

        if ($organizer) {
            $query = Event::where('organizer_id', $organizer->id);
        } else {
            $query = Event::whereHas('gateKeepers', function ($q) use ($user) {
                $q->where('id', $user->id);
            });
        }
        
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
        $user = $request->user();
        if ($user->tokenCant('organizer:transactions')) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Unauthorized']
            , 403);
        }

        $status = $request->query('status');
        $order = $request->query('order', 'desc');
        $query = $event->transactions()->with(['user','event','transactionItems.ticket', 'transactionItems.ticketIssueds']);

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->orderBy('created_at', $order)->get();
        
        // query total penghasilan dan total transaksi berhasil
        $stats = $event->transactions()->selectRaw('sum(total_harga) as total_penghasilan, count(id) as total_tiket_terjual')
            ->where('status', 'success')
            ->first();
        
        return response()->json(
            [
                'status' => 'success', 
                'data' => [
                    'event' => $event,
                    'stats' => $stats,
                    'transactions' => $transactions,
                ]
            ]
        );
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
            ->whereIn('status', ['inactive', 'active', 'resale', 'checkin'])
            ->with([
                'transactionItem.transaction.user',
                'transactionItem.ticket',
                'checkins',
                'user',
            ]);
        
        // Jika ada parameter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                // Pencarian berdasarkan email tiket issued
                $q->where('email_penerima', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $participants = $query->get();

        // Hitung total peserta dan berapa yang sudah checkin
        $stats = TicketIssued::whereHas('transactionItem.ticket', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->whereHas('transactionItem.transaction', function ($q) {
                $q->where('status', 'success');
            })
            ->selectRaw('count(id) as total_peserta, count(case when status = "checkin" then 1 end) as total_checkin')
            ->whereNot('status', 'sold')
            ->first();
            
        return response()->json([
            'status' => 'success', 
            'data' => [
                'event' => $event,
                'stats' => $stats,
                'participants' => $participants,
            ]
        ]);
    }

    // 5. Proses checkin berdasarkan kode tiket
    public function checkin(OrganizerEventCheckinRequest $request, Event $event)
    {
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

            if ($user->role==='organizer') {
                $ticketIssued->checkins()->create([
                    'checkinable_id' => $user->id,
                    'checkinable_type' => 'App\Models\User',
                    'checked_in_at' => now()
                ]);
            }

            if ($user->kode_akses) {
                $ticketIssued->checkins()->create([
                    'checkinable_id' => $user->id,
                    'checkinable_type' => 'App\Models\GateKeeper',
                    'checked_in_at' => now()
                ]);
            }

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
