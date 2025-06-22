<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Checkin extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_issued_id',
        'checkinable_type',
        'checkinable_id',
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

    public function checkinable(): MorphTo
    {
        return $this->morphTo();
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
