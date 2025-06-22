<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\HasApiTokens;

class GateKeeper extends Model
{
    use HasUuids, HasApiTokens;

    protected $fillable = [
        'id',
        'event_id',
        'nama',
        'email',
        'kode_akses',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function checkins(): MorphMany
    {
        return $this->morphMany(Checkin::class, 'checkinable')->chaperone();
    }
}
