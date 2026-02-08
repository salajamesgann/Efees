<?php

namespace Tests\Feature;

use App\Models\FeeAssignment;
use App\Models\FeeRecord;
use App\Models\Student;
use App\Models\TuitionFee;
use App\Services\PaymentScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_generates_monthly_schedule()
    {
        $service = new PaymentScheduleService;
        $schedule = $service->buildSchedule(9000, 'monthly', '2025-08-15');

        $this->assertTrue($schedule['installment_allowed']);
        $this->assertEquals('monthly', $schedule['plan']);
        $this->assertCount(9, $schedule['items']);

        $first = $schedule['items'][0];
        $this->assertEquals(1000, $first['amount']);
        $this->assertEquals('2025-08-15', $first['due_date']);

        $second = $schedule['items'][1];
        $this->assertEquals('2025-09-15', $second['due_date']);
    }

    public function test_service_generates_quarterly_schedule()
    {
        $service = new PaymentScheduleService;
        $schedule = $service->buildSchedule(10000, 'quarterly', '2025-08-01');

        $this->assertCount(4, $schedule['items']);
        $this->assertEquals(2500, $schedule['items'][0]['amount']);
        // 3 months gap
        $this->assertEquals('2025-08-01', $schedule['items'][0]['due_date']);
        $this->assertEquals('2025-11-01', $schedule['items'][1]['due_date']);
    }

    public function test_fee_assignment_creates_installment_records()
    {
        // Manually create TuitionFee to bypass missing factory
        $tuition = new TuitionFee;
        // $tuition->fee_name = 'Test Tuition'; // Column does not exist, uses notes or implied
        $tuition->grade_level = 'Grade 1';
        $tuition->school_year = '2025-2026';
        $tuition->semester = '1st';
        $tuition->amount = 5000;
        $tuition->payment_schedule = [
            'installment_allowed' => true,
            'plan' => 'semester',
            'items' => [
                ['label' => '1st Sem', 'amount' => 2500, 'due_date' => '2025-08-15'],
                ['label' => '2nd Sem', 'amount' => 2500, 'due_date' => '2026-01-15'],
            ],
        ];
        $tuition->save();

        $student = new Student;
        $student->student_id = 'S-2025-0001';
        $student->first_name = 'Test';
        $student->last_name = 'Student';
        $student->level = $tuition->grade_level;
        $student->school_year = $tuition->school_year;
        $student->save();

        // In test, we called FeeAssignment::create() directly which creates the record in DB
        // BUT it does NOT trigger the static method assignForStudent() which contains the schedule creation logic!
        // The standard Eloquent create() does not invoke arbitrary static methods unless triggered by Observer/Event.
        // Looking at FeeAssignment.php, the schedule creation is inside assignForStudent().

        // So we must call assignForStudent() instead of create() to test the logic.

        $assignment = FeeAssignment::assignForStudent(
            $student->student_id,
            $tuition->school_year,
            $tuition->semester
        );

        // Manual sync call usually done in observer/service
        // Let's call the FeeManagementService recompute which usually handles sync?
        // Or if FeeAssignment::boot creates it.
        // Assuming FeeAssignment logic I added in previous turn works on 'created' or similar.
        // Wait, I updated FeeAssignment.php but didn't verify WHERE the logic is.
        // If it is in `booted` -> `created`, it should run automatically.
        // If it is NOT, then I need to find where it is or move it.
        // Let's assume it runs. If test fails, I'll investigate FeeAssignment.php.

        $this->assertDatabaseHas('fee_records', [
            'student_id' => $student->student_id,
            'record_type' => 'tuition_installment',
            'amount' => 2500,
        ]);

        $this->assertEquals(2, FeeRecord::where('student_id', $student->student_id)->where('record_type', 'tuition_installment')->count());
    }
}
