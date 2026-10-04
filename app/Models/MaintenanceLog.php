<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'maintenance_type',
        'performed_at',
        'water_change_percentage',
        'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'water_change_percentage' => 'integer',
    ];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }
}
