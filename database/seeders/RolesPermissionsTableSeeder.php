<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles_permissions')->insert([
            // Admin permissions
            ['role_key' => '01_admin', 'permission_key' => 'manage_inventory'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_staff'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_customers'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_rooms'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_revenue'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_permissions'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_invoices'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_songs'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_roles'],
            ['role_key' => '01_admin', 'permission_key' => 'manage_bookings'],

            // Manager permissions
            ['role_key' => '03_manager', 'permission_key' => 'manage_inventory'],
            ['role_key' => '03_manager', 'permission_key' => 'manage_staff'],
            ['role_key' => '03_manager', 'permission_key' => 'manage_customers'],
            ['role_key' => '03_manager', 'permission_key' => 'manage_rooms'],

            // Staff permissions
            ['role_key' => '02_staff', 'permission_key' => 'manage_customers'],
            ['role_key' => '02_staff', 'permission_key' => 'manage_rooms'],
        ]);
    }
}