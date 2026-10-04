<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Livestock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'livestock';

    protected $fillable = [
        'livestock_code',
        'species_id',
        'variety',
        'sex',
        'date_of_birth',
        'date_acquired',
        'source',
        'purchase_price',
        'status',
        'tank_id',
        'grade',
        'grading_scores',
        'notes',
        'photo_path',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_acquired' => 'date',
        'purchase_price' => 'decimal:2',
        'grading_scores' => 'array',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return null;
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function maleBreedingEvents(): HasMany
    {
        return $this->hasMany(BreedingEvent::class, 'male_livestock_id');
    }

    public function femaleBreedingEvents(): HasMany
    {
        return $this->hasMany(BreedingEvent::class, 'female_livestock_id');
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(FeedingLog::class);
    }

    public function tankPhotos(): HasMany
    {
        return $this->hasMany(TankPhoto::class)->latest();
    }
}
