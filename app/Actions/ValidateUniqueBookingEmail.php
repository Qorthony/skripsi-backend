<?php

namespace App\Actions;

class ValidateUniqueBookingEmail
{
    public function handle(array $ticketIssueds, string $bookingEmail): bool
    {
        // Check if the booking email is included in the ticket issueds array
        $emailCount = 0;
        foreach ($ticketIssueds as $ticketIssued) {
            if ($ticketIssued['email_penerima'] === $bookingEmail && $ticketIssued['pemesan'] === true) {
                $emailCount++;
            }
        }

        // If the email count is 1 return true, otherwise false
        return $emailCount === 1;
    }
}