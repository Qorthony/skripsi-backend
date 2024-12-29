<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_issued_id',
        'user_id',
        'checked_in_at',
        'checked_out_at',
    ];

    protected $dates = [
        'checked_in_at',
        'checked_out_at',
    ];

    public function ticketIssued()
    {
        return $this->belongsTo(TicketIssued::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCheckedIn($query)
    {
        return $query->whereNotNull('checked_in_at');
    }

    public function scopeCheckedOut($query)
    {
        return $query->whereNotNull('checked_out_at');
    }
}
