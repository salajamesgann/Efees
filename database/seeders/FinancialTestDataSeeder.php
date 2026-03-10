<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\FeeRecord;
use App\Models\Payment;

class FinancialTestDataSeeder extends Seeder
{
    public function run()
    {
        // Student 1: Fully Paid
        $student1 = Student::factory()->create();
        FeeRecord::factory()->create([
            'student_id' => $student1->student_id,
            'amount' => 1000,
            'balance' => 0,
            'status' => 'paid',
        ]);
        Payment::factory()->create([
            'student_id' => $student1->student_id,
            'amount_paid' => 1000,
            'status' => 'confirmed',
        ]);

        // Student 2: Partially Paid
        $student2 = Student::factory()->create();
        FeeRecord::factory()->create([
            'student_id' => $student2->student_id,
            'amount' => 2000,
            'balance' => 1500,
            'status' => 'unpaid',
        ]);
        Payment::factory()->create([
            'student_id' => $student2->student_id,
            'amount_paid' => 500,
            'status' => 'confirmed',
        ]);

        // Student 3: Unpaid
        $student3 = Student::factory()->create();
        FeeRecord::factory()->create([
            'student_id' => $student3->student_id,
            'amount' => 3000,
            'balance' => 3000,
            'status' => 'unpaid',
        ]);
    }
}
