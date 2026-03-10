<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => substr(str_replace('-', '', \Illuminate\Support\Str::uuid()->toString()), 0, 20),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'enrollment_status' => 'Enrolled',
        ];
    }
}
