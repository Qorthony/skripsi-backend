<?php

namespace Database\Factories;

use App\Models\TicketIssued;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resale>
 */
class ResaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_issued_id' => TicketIssued::factory(),
            'status' => 'active',
            'harga_jual' => $this->faker->numberBetween(10000, 100000),
        ];
    }
}
