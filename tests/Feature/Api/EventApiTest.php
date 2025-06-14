<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\OrganizerSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed= true;

    protected function authenticate()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_list_events()
    {
        $organizer = Organizer::all()->first();
        Event::factory()
            ->organizerId($organizer->id)
            ->has(
                Ticket::factory()
            )
            ->count(3)->create(['status' => 'published']);
        $headers = $this->authenticate();
        $response = $this->getJson('/api/events', $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message', 'data' => [['id', 'nama', 'status', 'tickets']]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_filter_ongoing_events()
    {
        $organizer = Organizer::all()->first();
        Event::factory()
            ->organizerId($organizer->id)
            ->create(['status' => 'published', 'jadwal_mulai' => now()->addDay()]);
        Event::factory()
            ->organizerId($organizer->id)
            ->create(['status' => 'published', 'jadwal_mulai' => now()->subDay()]);
        $headers = $this->authenticate();
        $response = $this->getJson('/api/events?ongoing=1', $headers);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);
        $this->assertTrue(
            collect($response->json('data'))->every(fn($e) => $e['jadwal_mulai'] >= now()->toDateTimeString())
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_show_event_detail()
    {
        $organizer = Organizer::all()->first();
        $event = Event::factory()
                    ->organizerId($organizer->id)
                    ->create(['status' => 'published']);
        $headers = $this->authenticate();
        $response = $this->getJson('/api/events/' . $event->id, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message', 'data' => ['id', 'nama', 'status', 'tickets']
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_access_unpublished_event()
    {
        $organizer = Organizer::all()->first();
        $event = Event::factory()
                    ->organizerId($organizer->id)
                    ->create(['status' => 'draft']);
        $headers = $this->authenticate();
        $response = $this->getJson('/api/events/' . $event->id, $headers);
        $response->assertStatus(404);
    }
}
