<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\TransactionItem;
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
            'transaction_item_id' => TransactionItem::factory(),
            'user_id' => User::factory(),
            'email_penerima' => fake()->email(),
        ];
    }

    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => fake()->randomElement(['inactive', 'resale', 'sold', 'checkin']),
            ];
        });
    }
}
