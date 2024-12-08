<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'jumlah_tiket' => fake()->numberBetween(1, 5),
            'total_harga' => fake()->numberBetween(100000, 500000),
            'batas_waktu' => fake()->dateTimeBetween('now', '+15 minutes'),
            'status' => fake()->randomElement(['pending', 'success', 'failed']),
        ];
    }
}
