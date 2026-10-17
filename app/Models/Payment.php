<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'fee_id',
        'amount',
        'method',
        'transaction_reference',
        'provider',
        'provider_transaction_id',
        'phone_number',
        'status',
        'paid_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }
}
