<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['name' => 'Admin', 'key' => '01_admin'],
            ['name' => 'Staff', 'key' => '02_staff'],
            ['name' => 'Manager', 'key' => '03_manager'],
            ['name' => 'Guest', 'key' => '04_guest'],
        ]);
    }
}