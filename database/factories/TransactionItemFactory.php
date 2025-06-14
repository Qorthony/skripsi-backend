<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionItem>
 */
class TransactionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ticket = Ticket::factory()->create();
        return [
            'transaction_id' => \App\Models\Transaction::factory(),
            'ticket_id' =>  $ticket->id,
            'nama'=>  $ticket->nama,
            'deskripsi' => $ticket->deskripsi,
            'harga_satuan' => $ticket->harga,
            'jumlah' => 1,
            'total_harga' => $ticket->harga * 1,
        ];
    }
}
