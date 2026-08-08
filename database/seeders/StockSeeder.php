<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        // Link Panadol (ID 1) to Goodlife (ID 1)
        Stock::create([
            'pharmacy_id' => 1,
            'medicine_id' => 1,
            'price' => 150.00,
            'in_stock' => true,
        ]);

        // Link Amoxil (ID 2) to Goodlife (ID 1)
        Stock::create([
            'pharmacy_id' => 1,
            'medicine_id' => 2,
            'price' => 450.00,
            'in_stock' => true,
        ]);

        // Link Panadol (ID 1) to Pharmaplus (ID 2)
        Stock::create([
            'pharmacy_id' => 2,
            'medicine_id' => 1,
            'price' => 140.00,
            'in_stock' => false,
        ]);
    }
}