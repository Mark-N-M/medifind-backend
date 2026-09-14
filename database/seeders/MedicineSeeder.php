<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        Medicine::create([
            'name' => 'Panadol Extra',
            'generic_name' => 'Paracetamol / Caffeine',
            'category' => 'Pain Relief',
        ]);

        Medicine::create([
            'name' => 'Amoxil 500mg',
            'generic_name' => 'Amoxicillin',
            'category' => 'Antibiotics',
        ]);

        Medicine::create([
            'name' => 'Actal Fast',
            'generic_name' => 'Antacid',
            'category' => 'Digestive Health',
        ]);

        Medicine::create([
            'name' => 'Piriton',
            'generic_name' => 'Chlorphenamine Maleate',
            'category' => 'Allergy',
        ]);

        Medicine::create([
            'name' => 'Flagyl 400mg',
            'generic_name' => 'Metronidazole',
            'category' => 'Antibiotics',
        ]);

        Medicine::create([
            'name' => 'Coartem',
            'generic_name' => 'Artemether / Lumefantrine',
            'category' => 'Antimalarial',
        ]);

        Medicine::create([
            'name' => 'ORS Sachets',
            'generic_name' => 'Oral Rehydration Salts',
            'category' => 'Digestive Health',
        ]);
    }
}