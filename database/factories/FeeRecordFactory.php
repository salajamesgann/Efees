<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeRecord>
 */
class FeeRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\Student::factory(),
            'record_type' => 'tuition',
            'amount' => 1000,
            'balance' => 1000,
            'status' => 'unpaid',
        ];
    }
}
