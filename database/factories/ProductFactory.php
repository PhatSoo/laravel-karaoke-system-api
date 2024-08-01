<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'inventory' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 5, 50),
            'unit' => $this->faker->randomElement(['box x24 cans', 'can', 'plate']),
            'type' => $this->faker->randomElement(['foods', 'drinks', 'other']),
        ];
    }
}