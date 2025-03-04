<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OneTimePassword extends Model
{
    use HasUuids;
    
    protected $fillable = ['identifier', 'otp_code', 'expires_at'];
    public $timestamps = true;

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
