<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Species extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'scientific_name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function livestock(): HasMany
    {
        return $this->hasMany(Livestock::class);
    }

    public function breedingEvents(): HasMany
    {
        return $this->hasMany(BreedingEvent::class);
    }

    public function offspringBatches(): HasMany
    {
        return $this->hasMany(OffspringBatch::class);
    }
}
