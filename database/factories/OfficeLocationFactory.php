<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfficeLocation>
 */
class OfficeLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Office',
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-10, 5), // Indonesia latitude range
            'longitude' => fake()->longitude(95, 141), // Indonesia longitude range
            'radius_meters' => fake()->numberBetween(50, 200),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the office location is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}