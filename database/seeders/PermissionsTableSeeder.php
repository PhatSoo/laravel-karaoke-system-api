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
            ['name' => 'Manage Inventory', 'key' => 'manage_inventory', 'related_table' => 'products'],
            ['name' => 'Manage Staff', 'key' => 'manage_staff', 'related_table' => 'staffs'],
            ['name' => 'Manage Customers', 'key' => 'manage_customers', 'related_table' => 'customers'],
            ['name' => 'Manage Rooms', 'key' => 'manage_rooms', 'related_table' => 'rooms'],
            ['name' => 'Manage Revenue', 'key' => 'manage_revenue', 'related_table' => 'null'],
            ['name' => 'Manage Permissions', 'key' => 'manage_permissions', 'related_table' => 'permissions'],
        ]);
    }
}