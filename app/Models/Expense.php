<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORIES = [
        'FEED' => 'Feed & Nutrition',
        'LIVESTOCK' => 'Livestock Acquisition',
        'EQUIPMENT' => 'Equipment & Hardware',
        'ELECTRICITY' => 'Power & Electricity',
        'WATER' => 'Water & Utilities',
        'MEDICATION' => 'Medication & Treatments',
        'PACKAGING' => 'Shipping & Packaging',
        'MAINTENANCE' => 'Repairs & Maintenance',
        'OTHER' => 'General / Other',
    ];

    protected $fillable = [
        'category',
        'description',
        'amount',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public static function categories(): array
    {
        return self::CATEGORIES;
    }
}
