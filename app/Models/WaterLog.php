<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'recorded_at',
        'temperature',
        'ph',
        'ammonia',
        'nitrite',
        'nitrate',
        'tds',
        'status',
        'notes',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'decimal:2',
        'ph' => 'decimal:2',
        'ammonia' => 'decimal:2',
        'nitrite' => 'decimal:2',
        'nitrate' => 'decimal:2',
        'tds' => 'decimal:2',
    ];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public static function calculateStatus(?float $ph, ?float $ammonia, ?float $nitrite, ?float $nitrate): string
    {
        if (($ammonia !== null && $ammonia > 0.5) || ($nitrite !== null && $nitrite > 0.5) || ($nitrate !== null && $nitrate > 80)) {
            return 'CHECK';
        }

        if (($ammonia !== null && $ammonia > 0.25) || ($nitrite !== null && $nitrite > 0.25) || ($nitrate !== null && $nitrate > 40) || ($ph !== null && ($ph < 6.0 || $ph > 8.5))) {
            return 'WARNING';
        }

        return 'GOOD';
    }
}
