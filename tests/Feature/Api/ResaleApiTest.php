<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Resale;
use App\Models\Ticket;
use App\Models\TicketIssued;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResaleApiTest extends TestCase
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
    public function can_list_resale_tickets()
    {
        [$user, $headers] = $this->authenticate();
        $organizer = Organizer::all()->first();
        $event = Event::factory()
                    ->organizerId($organizer->id)
                    ->create(['status' => 'published']);
        $ticket = Ticket::factory()
                        ->paid()
                        ->for($event, 'event')
                        ->create([
                            'event_id' => $event->id,
                        ]);
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $transactionItem = TransactionItem::factory()->create([
            'transaction_id' => $transaction->id,
            'ticket_id' => $ticket->id,
            'nama' => $ticket->nama,
            'deskripsi' => $ticket->deskripsi,
            'harga_satuan' => $ticket->harga,
            'jumlah' => 1,
            'total_harga' => $ticket->harga * 1,
        ]);
        $ticketIssued = TicketIssued::factory()->create([
            'user_id' => $user->id,
            'transaction_item_id' => $transactionItem->id,
            'status' => 'resale',
        ]);
        Resale::factory()->create(['ticket_issued_id' => $ticketIssued->id, 'status' => 'active']);
        $response = $this->getJson('/api/resales', $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message', 'data' => ['event', 'resales']
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_filter_resale_by_event()
    {
        [$user, $headers] = $this->authenticate();
        $organizer = Organizer::all()->first();
        $event = Event::factory()
                    ->organizerId($organizer->id)
                    ->create(['status' => 'published']);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $transactionItem = TransactionItem::factory()->create([
            'transaction_id' => $transaction->id,
            'ticket_id' => $ticket->id,
            'nama' => $ticket->nama,
            'deskripsi' => $ticket->deskripsi,
            'harga_satuan' => $ticket->harga,
            'jumlah' => 1,
            'total_harga' => $ticket->harga * 1,
        ]);
        $ticketIssued = TicketIssued::factory()->create([
            'user_id' => $user->id,
            'transaction_item_id' => $transactionItem->id,
            'status' => 'resale',
        ]);
        Resale::factory()->create(['ticket_issued_id' => $ticketIssued->id, 'status' => 'active']);
        $response = $this->getJson('/api/resales?event_id=' . $event->id, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message', 'data' => ['event', 'resales']
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_access_resale_with_invalid_event()
    {
        [$user, $headers] = $this->authenticate();
        $response = $this->getJson('/api/resales?event_id=999999', $headers);
        $response->assertStatus(404);
    }
}
