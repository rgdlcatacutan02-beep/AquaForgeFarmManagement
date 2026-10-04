<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'unit',
        'minimum_quantity',
        'cost',
        'supplier',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_quantity;
    }
}
