<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use App\Models\TicketIssued;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate($role = 'participant')
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'role' => $role]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [$user, ['Authorization' => 'Bearer ' . $token]];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_get_ticket_issued_for_checkin()
    {
        [$user, $headers] = $this->authenticate();
        $event = Event::factory()->create(['status' => 'published']);
        $ticketIssued = TicketIssued::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);
        $response = $this->getJson('/api/ticket-issued/' . $ticketIssued->id . '/checkin', $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message', 'data' => ['id', 'kode_tiket', 'status']
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_checkin_non_active_ticket()
    {
        [$user, $headers] = $this->authenticate();
        $ticketIssued = TicketIssued::factory()->inactive()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->getJson('/api/ticket-issued/' . $ticketIssued->id . '/checkin', $headers);
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_checkin_nonexistent_ticket()
    {
        [$user, $headers] = $this->authenticate();
        $response = $this->getJson('/api/ticket-issued/999999/checkin', $headers);
        $response->assertStatus(404);
    }
}
