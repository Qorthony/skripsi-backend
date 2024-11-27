<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\EventSubmissionFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    public function event() : BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
