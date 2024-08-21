<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomId = Room::inRandomOrder()->first()->id;
        $customerId = Customer::inRandomOrder()->first()->id;

        return [
            'room_id' => $roomId,
            'customer_id' => $customerId,
            'start_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_time' => $this->faker->dateTimeBetween('+1 hour', '+2 week'),
            'status' => $this->faker->randomElement(['booked', 'completed', 'cancelled']),
        ];
    }
}