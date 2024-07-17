<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Song;
use App\Models\Staff;

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
        Customer::factory()->count(10)->create();
        Song::factory()->count(10)->create();
        Booking::factory()->count(10)->create();
        Invoice::factory()->count(10)->create();
    }
}