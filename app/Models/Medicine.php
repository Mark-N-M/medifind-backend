<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'generic_name',
        'category',
    ];
    //Relationship: A medicine can exist in many pharmacy stocks eg amoxicilin 

    public function stocks(): HasMany{
        return $this->hasMany(Stock::class); //this is another way of doing the complex sql queries
    }
}
