<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'offspring_batch_id',
        'type',
        'quantity',
        'log_date',
        'reason',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'quantity' => 'integer',
    ];

    public function offspringBatch(): BelongsTo
    {
        return $this->belongsTo(OffspringBatch::class);
    }
}
