<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pharmacy::create([
            'name'=>'Goodlife Pharmacy - Westlands',
            'location'=>'Westlands, Ring Road, Nairobi',
            'phone' => '+254712345678',
            'latitude'=> -1.2676,
            'longitude'=> 36.8080,
        ]);

        Pharmacy::create([
            'name'=>'Pharmaplus Pharmacy - CBD',
            'location'=>'Kimathi Street, Nairobi CBD',
            'phone'=>'+254722987654',
            'latitude'=> -1.2841,
            'longitude'=> 36.8248,
        ]);

        Pharmacy::create([
            'name' => 'Haltons Pharmacy - Kilimani',
            'location' => 'Argwings Kodhek Rd, Kilimani',
            'phone' => '+254733112233',
            'latitude' => -1.2918,
            'longitude' => 36.7865,
        ]);
    }
}
