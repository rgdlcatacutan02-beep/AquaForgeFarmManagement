<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

class OffspringBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_code',
        'species_id',
        'breeding_event_id',
        'variety',
        'birth_or_hatch_date',
        'initial_count',
        'current_count',
        'death_count',
        'cull_count',
        'available_count',
        'grade',
        'tank_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'birth_or_hatch_date' => 'date',
        'initial_count' => 'integer',
        'current_count' => 'integer',
        'death_count' => 'integer',
        'cull_count' => 'integer',
        'available_count' => 'integer',
    ];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function breedingEvent(): BelongsTo
    {
        return $this->belongsTo(BreedingEvent::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function batchLogs(): HasMany
    {
        return $this->hasMany(BatchLog::class);
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(FeedingLog::class);
    }

    public function recordMortality(int $quantity, string $date, ?string $reason = null, ?string $notes = null): BatchLog
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Mortality quantity must be greater than zero.');
        }

        if ($quantity > $this->current_count) {
            throw new InvalidArgumentException('Mortality quantity cannot exceed current population count.');
        }

        $this->current_count -= $quantity;
        $this->death_count += $quantity;
        $this->available_count = max(0, $this->available_count - $quantity);
        $this->save();

        return $this->batchLogs()->create([
            'type' => 'MORTALITY',
            'quantity' => $quantity,
            'log_date' => $date,
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }

    public function recordCulling(int $quantity, string $date, ?string $reason = null, ?string $notes = null): BatchLog
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Culling quantity must be greater than zero.');
        }

        if ($quantity > $this->current_count) {
            throw new InvalidArgumentException('Culling quantity cannot exceed current population count.');
        }

        $this->current_count -= $quantity;
        $this->cull_count += $quantity;
        $this->available_count = max(0, $this->available_count - $quantity);
        $this->save();

        return $this->batchLogs()->create([
            'type' => 'CULLING',
            'quantity' => $quantity,
            'log_date' => $date,
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }
}
