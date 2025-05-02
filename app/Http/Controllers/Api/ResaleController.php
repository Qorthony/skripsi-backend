<?php

namespace App\Http\Controllers\Api;

use App\Actions\CancelResaleTicket;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Resale;
use App\Models\TicketIssued;
use Illuminate\Http\Request;

class ResaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'index',
            'data' => [
                'event' => $event,
                'resales' => TicketIssued::with(['resale', 'transactionItem'])
                    ->whereHas('transactionItem.ticket', function($query) use ($event) {
                        $query->where('event_id', $event->id);
                    })
                    ->whereHas('resale', function($query) {
                        $query->where('status', 'active');
                    })
                    ->get(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Resale::create([
            'ticket_issued_id' => $request->ticket_issued_id,
            'harga' => $request->harga,
            'status' => 'active',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Resale $resale)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'show',
            'data' => $event->transactionItems()
                        ->with(['ticketIssueds.resale', 'ticket'])
                        ->whereHas('ticketIssueds.resale', function($query) use ($resale) {
                            $query->where('resales.id', $resale->id);
                        })
                        ->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $resale = Resale::find($id);

        if (!$resale) {
            return response()->json([
                'status' => 'error',
                'message' => 'Resale not found',
            ], 404);
        }

        $action = $request->query('action');

        if ($action === 'cancel'){

            // Cancel the resale ticket
            $this->cancel($resale);

        } elseif ($action === 'update-price') {

            // Update the resale price
            $this->updatePrice($resale, $request);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid action',
            ], 400);
        }
    }

    private function cancel(Resale $resale)
    {
        // Ensure the resale status is active
        if ($resale->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Resale can only be cancelled if it is active',
            ], 400);
        }

        // Cancel the resale
        $cancelResaleTicket = new CancelResaleTicket();
        $cancelResaleTicket->handle($resale);

        return response()->json([
            'status' => 'success',
            'message' => 'Resale cancelled successfully',
        ]);
    }

    private function updatePrice(Resale $resale, Request $request)
    {
        // Validate the request data
        $request->validate([
            'harga' => 'required|numeric|min:0',
        ]);

        // Update the resale record
        $resale->update([
            'harga' => $request->harga,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Resale price updated successfully',
            'data' => $resale,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
