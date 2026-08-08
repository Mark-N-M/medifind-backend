<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    // Populated $fillable array (includes foreign keys and stock info)
    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'price',
        'in_stock',
    ];

    // Converts price to 2 decimal places and in_stock to a true/false boolean
    protected $casts = [
        'price'    => 'decimal:2',
        'in_stock' => 'boolean',
    ];

    // Relationship: Stock entry belongs to a single Pharmacy
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    // Relationship: Stock entry belongs to a single Medicine
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}