<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;

class CancelResaleTicket
{
    public function handle($resale)
    {
        DB::beginTransaction();
        try {
            // Update the ticket_issued status to inactive
            $this->updateTicketIssuedStatus($resale);

            // Update the resale status to cancelled
            $this->updateResaleStatus($resale);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function updateTicketIssuedStatus($resale)
    {
        $ticketIssued = $resale->ticketIssued;
        $ticketIssued->update([
            'status' => $ticketIssued->waktu_penerbitan ? 'active' : 'inactive'
        ]);
    }

    private function updateResaleStatus($resale)
    {
        $resale->update(['status' => 'cancelled']);
    }
}