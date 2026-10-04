<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'livestock_id',
        'offspring_batch_id',
        'food',
        'quantity',
        'fed_at',
        'notes',
    ];

    protected $casts = [
        'fed_at' => 'datetime',
    ];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function offspringBatch(): BelongsTo
    {
        return $this->belongsTo(OffspringBatch::class);
    }
}
