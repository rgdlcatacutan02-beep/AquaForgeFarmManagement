<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreedingEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'breeding_code',
        'species_id',
        'tank_id',
        'male_livestock_id',
        'female_livestock_id',
        'start_date',
        'expected_date',
        'actual_birth_or_hatch_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_date' => 'date',
        'actual_birth_or_hatch_date' => 'date',
    ];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function maleLivestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'male_livestock_id');
    }

    public function femaleLivestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'female_livestock_id');
    }

    public function offspringBatches(): HasMany
    {
        return $this->hasMany(OffspringBatch::class);
    }
}
