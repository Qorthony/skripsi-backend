<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resale extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_issued_id',
        'harga_jual',
        'status'
    ];

    public function ticketIssued()
    {
        return $this->belongsTo(TicketIssued::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
