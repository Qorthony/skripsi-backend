<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama'=>fake()->sentence(3),
            'lokasi'=>'offline',
            'kota'=>fake()->city(),
            'alamat_lengkap'=>fake()->address(),
            'jadwal_mulai'=>fake()->dateTimeBetween('+32 days','+33 days'),
            'jadwal_selesai' => fake()->dateTimeBetween('+34 days','+35 days'),
            'deskripsi' => fake()->paragraph(),
            'status'=> EventStatus::Published,
        ];
    }

    public function organizerId(string $id): Factory
    {
        return $this->state(function (array $attributes) use($id) {
            return [
                'organizer_id' => $id,
            ];
        });
    }

    public function online(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'lokasi' => 'online',
                'tautan_acara' => fake()->url(),
            ];
        });
    }

    public function oneDay(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'jadwal_mulai' => now()->addDays(35),
                'jadwal_selesai' => now()->addDays(35),
            ];
        });
    }
}
