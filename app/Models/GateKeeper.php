<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GateKeeper extends Model
{
    use HasUuids;

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
}
