<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => \App\Models\Event::factory(),
            'nama'=>'gratis',
            'harga'=>0,
            'kuota'=>100,
            'waktu_tutup'=>fake()->dateTimeBetween('+2 weeks', '+1 months'),
        ];
    }

    public function scheduledOpen(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'waktu_buka' => fake()->dateTimeBetween('+1 days','+1 weeks'),
            ];
        });
    }

    public function paid(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'nama' => 'Berbayar',
                'harga'=> fake()->numberBetween(30000, 500000)
            ];
        });
    }
}
