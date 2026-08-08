<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Super Administrator', 'slug' => 'super-admin']);
        Role::create(['name' => 'Pharmacy Manager',   'slug' => 'mini-admin']);
        Role::create(['name' => 'Normal User',        'slug' => 'user']);
    }
}
