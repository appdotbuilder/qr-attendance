<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\QrCode;
use App\Models\OfficeLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInTime = fake()->time('H:i:s');
        $checkOutTime = fake()->optional(0.8)->time('H:i:s');
        
        return [
            'employee_id' => Employee::factory(),
            'qr_code_id' => QrCode::factory(),
            'office_location_id' => OfficeLocation::factory(),
            'date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'check_in_latitude' => fake()->latitude(-10, 5),
            'check_in_longitude' => fake()->longitude(95, 141),
            'check_out_latitude' => $checkOutTime ? fake()->latitude(-10, 5) : null,
            'check_out_longitude' => $checkOutTime ? fake()->longitude(95, 141) : null,
            'status' => fake()->randomElement(['present', 'late', 'absent']),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the attendance is for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the employee was late.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'late',
            'check_in_time' => fake()->time('09:00:00', '11:59:59'),
        ]);
    }

    /**
     * Indicate that the employee was present on time.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
            'check_in_time' => fake()->time('07:00:00', '08:59:59'),
        ]);
    }
}