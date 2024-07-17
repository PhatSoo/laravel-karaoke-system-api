<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_name' => $this->faker->word,
            'capacity' => $this->faker->numberBetween(10, 20),
            'price_per_hour' => $this->faker->randomFloat(2, 10, 50),
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance']),
        ];
    }
}