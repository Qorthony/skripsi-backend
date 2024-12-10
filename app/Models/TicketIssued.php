<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class TicketIssued extends Model
{
    /** @use HasFactory<\Database\Factories\TicketIssuedFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'transaction_id',
        'ticket_id',
        'user_id',
        'email_penerima',
        'waktu_penerbitan',
        'aktif'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
