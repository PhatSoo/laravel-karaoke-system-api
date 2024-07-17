<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Staff;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $bookingId = Booking::inRandomOrder()->first()->id;
        $staffId = Staff::inRandomOrder()->first()->id;

        return [
            'booking_id' => $bookingId,
            'staff_id' => $staffId,
            'total_amount' => $this->faker->randomFloat(2, 10, 50),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
        ];
    }
}