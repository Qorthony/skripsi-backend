<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketIssued;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request, Event $event)
    {
        // Pastikan user adalah organizer dari event ini
        if (!$request->access_code){
            if (Auth::user()->organizer?->id !== $event->organizer_id) {
                abort(403, 'Unauthorized');
            }
        }

        // Ambil semua tiket yang sudah diterbitkan untuk event ini
        $ticketsIssued = TicketIssued::whereHas('transactionItem.ticket', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })
        ->with([
            'user:id,name,email',
            'transactionItem.ticket:id,nama,event_id',
            'checkins' => function ($query) {
                $query->orderBy('checked_in_at', 'desc');
            }
        ])
        ->orderBy('waktu_penerbitan', 'desc')
        ->get();

        // Transform data untuk menambahkan informasi kehadiran
        $attendanceData = $ticketsIssued->map(function ($ticket) {
            $latestCheckin = $ticket->checkins->first();
            
            return [
                'id' => $ticket->id,
                'kode_tiket' => $ticket->kode_tiket,
                'email_penerima' => $ticket->email_penerima,
                'waktu_penerbitan' => $ticket->waktu_penerbitan,
                'status' => $ticket->status,
                'user' => $ticket->user,
                'ticket_name' => $ticket->transactionItem->ticket->nama,
                'is_checked_in' => $latestCheckin && $latestCheckin->checked_in_at && !$latestCheckin->checked_out_at,
                'checked_in_at' => $latestCheckin?->checked_in_at,
                'checked_out_at' => $latestCheckin?->checked_out_at,
                // 'total_checkins' => $ticket->checkins->count(),
            ];
        });

        return Inertia::render('Event/Attendance/Index', [
            'event' => $event,
            'attendanceData' => $attendanceData,
            'stats' => [
                'total_tickets' => $ticketsIssued->count(),
                'checked_in' => $attendanceData->where('is_checked_in', true)->count(),
                'never_checked_in' => $attendanceData->where('total_checkins', 0)->count(),
            ]
        ]);
    }

    // public function checkin(Event $event, TicketIssued $ticketIssued)
    // {
    //     // Pastikan user adalah organizer dari event ini
    //     if (Auth::user()->organizer?->id !== $event->organizer_id) {
    //         abort(403, 'Unauthorized');
    //     }

    //     // Pastikan tiket ini milik event ini
    //     if (!$ticketIssued->transactionItem->ticket->event_id === $event->id) {
    //         abort(404, 'Ticket not found for this event');
    //     }

    //     // Cek apakah sudah check-in dan belum check-out
    //     $latestCheckin = $ticketIssued->checkins()
    //         ->whereNotNull('checked_in_at')
    //         ->whereNull('checked_out_at')
    //         ->first();

    //     if ($latestCheckin) {
    //         return back()->withErrors(['message' => 'Peserta sudah melakukan check-in.']);
    //     }

    //     // Buat checkin baru
    //     Checkin::create([
    //         'ticket_issued_id' => $ticketIssued->id,
    //         'user_id' => $ticketIssued->user_id,
    //         'checked_in_at' => now(),
    //     ]);

    //     return back()->with('success', 'Peserta berhasil melakukan check-in.');
    // }

    // public function checkout(Event $event, TicketIssued $ticketIssued)
    // {
    //     // Pastikan user adalah organizer dari event ini
    //     if (Auth::user()->organizer?->id !== $event->organizer_id) {
    //         abort(403, 'Unauthorized');
    //     }

    //     // Pastikan tiket ini milik event ini
    //     if (!$ticketIssued->transactionItem->ticket->event_id === $event->id) {
    //         abort(404, 'Ticket not found for this event');
    //     }

    //     // Cari checkin yang aktif (sudah check-in tapi belum check-out)
    //     $activeCheckin = $ticketIssued->checkins()
    //         ->whereNotNull('checked_in_at')
    //         ->whereNull('checked_out_at')
    //         ->first();

    //     if (!$activeCheckin) {
    //         return back()->withErrors(['message' => 'Peserta belum melakukan check-in atau sudah check-out.']);
    //     }

    //     // Update checkin dengan waktu check-out
    //     $activeCheckin->update([
    //         'checked_out_at' => now(),
    //     ]);

    //     return back()->with('success', 'Peserta berhasil melakukan check-out.');
    // }

    // public function history(Event $event, TicketIssued $ticketIssued)
    // {
    //     // Pastikan user adalah organizer dari event ini
    //     if (Auth::user()->organizer?->id !== $event->organizer_id) {
    //         abort(403, 'Unauthorized');
    //     }

    //     // Pastikan tiket ini milik event ini
    //     if (!$ticketIssued->transactionItem->ticket->event_id === $event->id) {
    //         abort(404, 'Ticket not found for this event');
    //     }

    //     $checkins = $ticketIssued->checkins()
    //         ->orderBy('checked_in_at', 'desc')
    //         ->get();

    //     return Inertia::render('Event/Attendance/History', [
    //         'event' => $event,
    //         'ticketIssued' => $ticketIssued->load(['user', 'transactionItem.ticket']),
    //         'checkins' => $checkins,
    //     ]);
    // }
}
