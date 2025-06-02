<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'id',
        'event_id',
        'nama',
        'email',
        'kode_akses',
    ];

    protected $appends = ['access_link'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    protected function accessLink(): Attribute
    {
        return new Attribute(
            get: fn () => route('events.show.collaborator', [
                'event' => $this->event_id,
                'access_code' => $this->kode_akses,
            ])
        );
    }
}
