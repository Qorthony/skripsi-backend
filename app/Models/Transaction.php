<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id',
        'user_id',
        'jumlah_tiket',
        'total_harga',
        'batas_waktu',
        'status',
        'metode_pembayaran',
        'kode_pembayaran',
        'detail_pembayaran',
        'waktu_pembayaran',
        'total_pembayaran'
    ];

    protected $casts = [
        'batas_waktu' => 'datetime',
        'waktu_pembayaran' => 'datetime',
        'detail_pembayaran' => 'array'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketIssued()
    {
        return $this->hasMany(TicketIssued::class);
    }
}
