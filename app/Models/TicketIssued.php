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
        'transaction_item_id',
        'user_id',
        'kode_tiket',
        'email_penerima',
        'waktu_penerbitan',
        'status'
    ];

    protected $casts = [
        'waktu_penerbitan' => 'datetime'
    ];

    protected $hidden = [
        'kode_tiket'
    ];

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resale()
    {
        return $this->hasOne(Resale::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
}
