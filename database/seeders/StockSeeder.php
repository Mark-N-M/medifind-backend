<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        // --- Panadol Extra (medicine_id 1) available at ALL 3 pharmacies, different prices ---
        // This is the key "compare prices" demo scenario.
        Stock::create([
            'pharmacy_id' => 1, // Goodlife Westlands
            'medicine_id' => 1,
            'price' => 150.00,
            'in_stock' => true,
        ]);

        Stock::create([
            'pharmacy_id' => 2, // Pharmaplus CBD
            'medicine_id' => 1,
            'price' => 135.00,
            'in_stock' => true,
        ]);

        Stock::create([
            'pharmacy_id' => 3, // Haltons Kilimani
            'medicine_id' => 1,
            'price' => 160.00,
            'in_stock' => true,
        ]);

        // --- Amoxil 500mg (medicine_id 2) ---
        Stock::create([
            'pharmacy_id' => 1,
            'medicine_id' => 2,
            'price' => 450.00,
            'in_stock' => true,
        ]);

        Stock::create([
            'pharmacy_id' => 3,
            'medicine_id' => 2,
            'price' => 420.00,
            'in_stock' => true,
        ]);

        // --- Actal Fast (medicine_id 3) ---
        Stock::create([
            'pharmacy_id' => 2,
            'medicine_id' => 3,
            'price' => 90.00,
            'in_stock' => true,
        ]);

        Stock::create([
            'pharmacy_id' => 3,
            'medicine_id' => 3,
            'price' => 95.00,
            'in_stock' => false, // keeps an "out of stock" example in the data
        ]);

        // --- Piriton (medicine_id 4) ---
        Stock::create([
            'pharmacy_id' => 1,
            'medicine_id' => 4,
            'price' => 80.00,
            'in_stock' => true,
        ]);

        // --- Flagyl 400mg (medicine_id 5) ---
        Stock::create([
            'pharmacy_id' => 2,
            'medicine_id' => 5,
            'price' => 210.00,
            'in_stock' => true,
        ]);

        // --- Coartem (medicine_id 6) ---
        Stock::create([
            'pharmacy_id' => 1,
            'medicine_id' => 6,
            'price' => 320.00,
            'in_stock' => true,
        ]);

        Stock::create([
            'pharmacy_id' => 3,
            'medicine_id' => 6,
            'price' => 300.00,
            'in_stock' => true,
        ]);

        // --- ORS Sachets (medicine_id 7) ---
        Stock::create([
            'pharmacy_id' => 2,
            'medicine_id' => 7,
            'price' => 50.00,
            'in_stock' => true,
        ]);
    }
}