<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceLog>
 */
class AttendanceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'type' => fake()->randomElement(['check_in', 'check_out']),
            'logged_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'latitude' => fake()->latitude(-10, 5),
            'longitude' => fake()->longitude(95, 141),
            'distance_meters' => fake()->numberBetween(0, 150),
            'device_info' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate that this is a check-in log.
     */
    public function checkIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'check_in',
        ]);
    }

    /**
     * Indicate that this is a check-out log.
     */
    public function checkOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'check_out',
        ]);
    }
}