<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function authenticate($role = 'participant')
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'role' => $role]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [$user, ['Authorization' => 'Bearer ' . $token]];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function participant_can_list_own_transactions()
    {
        [$user, $headers] = $this->authenticate();
        $organizer = Organizer::all()->first();
        $event = Event::factory()
                    ->organizerId($organizer->id)
                    ->create(['status' => 'published']);
        Transaction::factory()->count(2)->create(['user_id' => $user->id, 'event_id' => $event->id]);
        $response = $this->getJson('/api/transactions', $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message', 'data' => [['id', 'event_id', 'user_id']]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function organizer_must_provide_event_id_to_list_transactions()
    {
        [$user, $headers] = $this->authenticate('organizer');
        $response = $this->getJson('/api/transactions', $headers);
        $response->assertStatus(400)
            ->assertJson(['status' => 'error']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function organizer_can_list_transactions_for_event()
    {
        [$user, $headers] = $this->authenticate('organizer');
        $organizer = Organizer::all()->first();
        $event = Event::factory()
                        ->organizerId($organizer->id)
                        ->create(['status' => 'published']);
        Transaction::factory()->count(2)->create(['event_id' => $event->id]);
        $response = $this->getJson('/api/transactions?event_id=' . $event->id, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message', 'data' => [['id', 'event_id']]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_access_transactions_without_auth()
    {
        $response = $this->getJson('/api/transactions');
        $response->assertStatus(401);
    }
}
