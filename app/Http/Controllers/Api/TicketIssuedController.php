<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resale;
use App\Models\TicketIssued;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketIssuedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'My Ticket Issued',
            'data' => auth()->user()->ticketIssued()->with('ticket')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketIssued $ticketIssued)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Detail Ticket Issued',
            'data' => $ticketIssued
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // activate ticket issued
        $ticketIssued = TicketIssued::find($id);

        if ($ticketIssued->user_id != auth()->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        if ($ticketIssued->transaction->status != 'success') {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction is not success'
            ], 400);
        }

        if ($ticketIssued->aktif) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket already activated'
            ], 400);
        }

        if ($request->input('action') == 'resale') {
            DB::transaction(function () use ($ticketIssued, $request) {
                Resale::updateOrCreate(
                    ['ticket_issued_id' => $ticketIssued->id],
                    [
                        'harga_jual' => $request->input('harga_jual'),
                        'status' => 'active'
                    ]
                );

                $ticketIssued->update([
                    'status' => 'resale'
                ]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket on resale',
                'data' => $ticketIssued->load(['resale','ticket'])
            ]);
        }

        // $ticketIssued->kode_tiket = (string) Str::uuid();
        // $ticketIssued->status = 'active';
        // $ticketIssued->waktu_penerbitan = now();
        // $ticketIssued->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket activated',
            // 'data' => $ticketIssued
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
