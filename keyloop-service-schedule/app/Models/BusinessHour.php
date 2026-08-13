<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BusinessHour extends Model
{
    protected $fillable = ['dealership_id', 'weekday', 'opens_at', 'closes_at'];

    public function dealership(): BelongsTo
    {
        return $this->belongsTo(Dealership::class);
    }
}
