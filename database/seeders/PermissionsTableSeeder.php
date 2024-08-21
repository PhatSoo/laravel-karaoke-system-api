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
            ['name' => 'Manage Inventory'],
            ['name' => 'Manage Staff'],
            ['name' => 'Manage Customers'],
            ['name' => 'Manage Rooms'],
            ['name' => 'Manage Revenue'],
            ['name' => 'Manage Permissions'],
            ['name' => 'Manage Invoices'],
            ['name' => 'Manage Songs'],
            ['name' => 'Manage Roles'],
            ['name' => 'Manage Bookings'],
        ]);
    }
}
