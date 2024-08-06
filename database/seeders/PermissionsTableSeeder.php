<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insert([
            ['name' => 'Manage Inventory', 'key' => 'manage_inventory'],
            ['name' => 'Manage Staff', 'key' => 'manage_staff'],
            ['name' => 'Manage Customers', 'key' => 'manage_customers'],
            ['name' => 'Manage Rooms', 'key' => 'manage_rooms'],
            ['name' => 'Manage Revenue', 'key' => 'manage_revenue'],
        ]);
    }
}