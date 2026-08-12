<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope',
        'idempotency_key',
        'request_hash',
        'status',
        'response_status_code',
        'response_body',
        'locked_at',
        'completed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'response_status_code' => 'integer',
            'response_body' => 'array',
            'locked_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
