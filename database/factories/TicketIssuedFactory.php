<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketIssued>
 */
class TicketIssuedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'email_penerima' => fake()->email(),
        ];
    }
}
