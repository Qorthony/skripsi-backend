<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventSubmissionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/EventSubmission/Index', [
            'eventSubmissions' => Event::whereIn('status', [
                EventStatus::InReview, 
                EventStatus::Published, 
                EventStatus::Rejected
            ])->latest()->get(),
        ]);
    }

    public function show(Event $event)
    {
        return Inertia::render('Admin/EventSubmission/Detail', [
            'event' => $event,
            'tickets' => $event->tickets,
        ]);
    }

    public function approve(Event $event)
    {
        $event->update(['status' => EventStatus::Published, 'alasan_penolakan' => null]);

        return redirect()->route('admin.event-submission.index')->with('success', 'Event approved successfully');
    }

    public function reject(Event $event, Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $event->update(['status' => EventStatus::Rejected, 'alasan_penolakan' => $validated['reason']]);

        return redirect()->route('admin.event-submission.index')->with('success', 'Event rejected successfully');
    }
}
