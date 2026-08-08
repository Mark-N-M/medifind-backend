<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacy extends Model
{
    use HasFactory;

  // Populated $fillable array allows mass assignment for these fields
    protected $fillable = [
        'name',
        'location',
        'phone',
        'latitude',
        'longitude',
    ];

    // Converts latitude/longitude strings from DB into real PHP floating-point numbers
    protected $casts = [
        'latitude'  => 'double',
        'longitude' => 'double',
    ];

    // Relationship: A Pharmacy can have many stock entries
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
}
