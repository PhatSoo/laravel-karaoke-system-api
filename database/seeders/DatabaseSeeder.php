<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Room;
use App\Models\Song;
use App\Models\Staff;
use App\Models\User;
use App\Models\Role;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Room::factory()->count(10)->create();
        Staff::factory()->count(10)->create();
        Customer::insert([
            'name' => 'Unknown Guest',
            'phone' => 'null',
            'email' => 'null',
        ]);
        Customer::factory()->count(9)->create();
        Song::factory()->count(10)->create();
        Booking::factory()->count(10)->create();
        Invoice::factory()->count(10)->create();
        Product::factory()->count(10)->create();
        User::insert([
            'username' => 'username',
            'password' => Hash::make('password')
        ]);

        // version 2
        Role::insert([
            ['name' => 'Admin', 'key' => '01_admin'],
            ['name' => 'Staff', 'key' => '02_staff'],
        ]);

        DB::insert('insert into users_roles (user_id, role_key) values (?, ?)', [1, '01_admin']);
        DB::insert('insert into users_roles (user_id, role_key) values (?, ?)', [1, '02_staff']);

        DB::insert('insert into invoices_products (invoice_id, product_id, quantity) values (?, ?, ?)', [1, 3, 10]);
        DB::insert('insert into invoices_products (invoice_id, product_id, quantity) values (?, ?, ?)', [1, 6, 15]);
    }
}
