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
            ['name' => 'Manage Inventory', 'related_table' => 'products'],
            ['name' => 'Manage Staff', 'related_table' => 'staffs'],
            ['name' => 'Manage Customers', 'related_table' => 'customers'],
            ['name' => 'Manage Rooms', 'related_table' => 'rooms'],
            ['name' => 'Manage Revenue', 'related_table' => 'invoices'],
            ['name' => 'Manage Permissions', 'related_table' => 'permissions'],
            ['name' => 'Manage Invoices', 'related_table' => 'invoices'],
            ['name' => 'Manage Songs', 'related_table' => 'songs'],
            ['name' => 'Manage Roles', 'related_table' => 'roles'],
            ['name' => 'Manage Bookings', 'related_table' => 'bookings'],
        ]);
    }
}
