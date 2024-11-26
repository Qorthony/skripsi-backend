<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizer>
 */
class OrganizerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama'=>fake()->company(),
            'deskripsi'=>fake()->sentence(),
            'alamat'=>fake()->address()
        ];
    }

    public function userId(string $id): Factory
    {
        return $this->state(function (array $attributes) use($id) {
            return [
                'user_id' => $id,
            ];
        });
    }
}
