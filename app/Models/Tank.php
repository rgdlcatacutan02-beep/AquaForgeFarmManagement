<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tank_code',
        'name',
        'tank_type',
        'length',
        'width',
        'height',
        'volume_liters',
        'location',
        'purpose',
        'status',
        'notes',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'volume_liters' => 'decimal:2',
    ];

    public function livestock(): HasMany
    {
        return $this->hasMany(Livestock::class);
    }

    public function offspringBatches(): HasMany
    {
        return $this->hasMany(OffspringBatch::class);
    }

    public function breedingEvents(): HasMany
    {
        return $this->hasMany(BreedingEvent::class);
    }

    public function waterLogs(): HasMany
    {
        return $this->hasMany(WaterLog::class);
    }

    public function latestWaterLog(): HasOne
    {
        return $this->hasOne(WaterLog::class)->latestOfMany('recorded_at');
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(FeedingLog::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function lastWaterChange(): HasOne
    {
        return $this->hasOne(MaintenanceLog::class)
            ->where('maintenance_type', 'WATER_CHANGE')
            ->latestOfMany('performed_at');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(TankPhoto::class)->latest();
    }
}
