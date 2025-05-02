<?php

namespace App\Actions;

use App\Models\Ticket;
use App\Models\TransactionItem;

class CheckIfTicketAvailable
{
    public function handle($event, $selectedTickets)
    {
        // Check if the selected tickets are available (stock not empty, between waktu buka and waktu tutup)
        $availableTickets = $event->tickets()->whereIn('id', array_column($selectedTickets, 'id'))->get();

        $availableTickets->each(function ($ticket) use ($selectedTickets) {
            $selectedTicket = collect($selectedTickets)->firstWhere('id', $ticket->id);
            if ($selectedTicket && $this->checkTicketStock($ticket, $selectedTicket)) {
                return [
                    'status' => 'error',
                    'message' => "Ticket {$ticket->name} is not available in the requested quantity.",
                ];
            }

            if ($selectedTicket && !$this->checkTicketTime($ticket, $selectedTicket)) {
                return [
                    'status' => 'error',
                    'message' => "Ticket {$ticket->name} is not available at this time.",
                ];
            }
        });

        return [
            'status' => 'success',
            'message' => 'All selected tickets are available.',
        ];
    }

    private function checkTicketStock($ticket, $selectedTicket)
    {
        $bookedTicket = $this->countBookedTicket($ticket);
        $ticketStock = $ticket->kuota - $bookedTicket;
        if ($ticketStock < $selectedTicket['quantity']) {
            return false;
        }
        return true;
    }

    private function countBookedTicket(Ticket $ticket)
    {
        return TransactionItem::where('ticket_id', $ticket->id)
            ->whereHas('transaction', function ($query) {
                $query->whereIn('status', ['pending', 'payment', 'success']);
            })
            ->count();
    }

    private function checkTicketTime($ticket)
    {
        $openingTime = $ticket->waktu_buka;
        $closingTime = $ticket->waktu_tutup;
        if ($openingTime) 
        {
            if (now()->isBetween($openingTime, $closingTime)) {
                return true;
            }
            return false;
        } else 
        {
            if (now()->isBefore($closingTime)) {
                return true;
            }
            return false;
        }
    }
}