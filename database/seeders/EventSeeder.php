<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizers = Organizer::all();

        foreach ($organizers as $key => $org) {
            $this->generateEvent($org->id);   
        }
    }

    private function generateEvent(string $organizerId)
    {
        // event offline gratis
        Event::factory()
            ->organizerId($organizerId)
            ->has(
                Ticket::factory()
            )
            ->create();
        
        // event offline berbayar
        Event::factory()
        ->organizerId($organizerId)
        ->has(
            Ticket::factory()
            ->paid()
            ->count(2)
        )
        ->create();

        // event online gratis
        Event::factory()
            ->organizerId($organizerId)
            ->has(
                Ticket::factory()
            )
            ->online()
            ->oneDay()
            ->create();
        
        // event online berbayar
        Event::factory()
        ->organizerId($organizerId)
        ->has(
            Ticket::factory()
            ->paid()
            ->count(2)
        )
        ->online()
        ->oneDay()
        ->create();
    }
}
