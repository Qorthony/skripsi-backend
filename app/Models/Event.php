<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $hidden = [
        'tautan_acara',
    ];

    protected function poster() : Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => $value? Storage::url($value):null,
        );
    }

    public function tickets() : HasMany 
    {
        return $this->hasMany(Ticket::class);    
    }

    public function organizer() : BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactionItems() : HasManyThrough
    {
        return $this->hasManyThrough(TransactionItem::class, Ticket::class);
    }

    public function collaborators() : HasMany
    {
        return $this->hasMany(Collaborator::class);
    }

    public function gateKeepers() : HasMany
    {
        return $this->hasMany(GateKeeper::class);
    }

    public function ticketIssued() : HasManyThrough
    {
        return $this->hasManyThrough(TicketIssued::class, TransactionItem::class, 'ticket_id', 'transaction_item_id', 'id', 'id')
                    ->join('tickets', 'transaction_items.ticket_id', '=', 'tickets.id')
                    ->where('tickets.event_id', $this->id);
    }
}
