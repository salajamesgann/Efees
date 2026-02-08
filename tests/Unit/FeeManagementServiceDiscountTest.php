<?php

namespace Tests\Unit;

use App\Models\AdditionalCharge;
use App\Models\Discount;
use App\Models\ParentContact;
use App\Models\Student;
use App\Models\TuitionFee;
use App\Services\FeeManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeManagementServiceDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_stackable_high_priority_applies_exclusively(): void
    {
        $grade = 'Grade 11';

        TuitionFee::create([
            'grade_level' => $grade,
            'amount' => 1000.00,
            'school_year' => 'SY 2025-2026',
            'semester' => 'N/A',
            'is_active' => true,
        ]);

        AdditionalCharge::create([
            'charge_name' => 'Lab',
            'description' => null,
            'amount' => 500.00,
            'applicable_grades' => [$grade],
            'is_active' => true,
            'is_mandatory' => true,
        ]);

        Discount::create([
            'discount_name' => 'Non-Stackable Charges Only ₱300',
            'type' => 'fixed',
            'value' => 300.00,
            'eligibility_rules' => ['apply_scope' => 'charges_only', 'is_stackable' => false],
            'applicable_grades' => [$grade],
            'is_active' => true,
            'is_automatic' => true,
            'priority' => 20,
        ]);

        Discount::create([
            'discount_name' => 'Stackable Total 10%',
            'type' => 'percentage',
            'value' => 10.0,
            'eligibility_rules' => ['apply_scope' => 'total', 'is_stackable' => true],
            'applicable_grades' => [$grade],
            'is_active' => true,
            'is_automatic' => true,
            'priority' => 10,
        ]);

        $student = Student::create([
            'student_id' => 'STU_X_1',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'level' => $grade,
            'section' => 'A',
            'school_year' => 'SY 2025-2026',
            'enrollment_status' => 'Active',
        ]);

        $svc = app(FeeManagementService::class);
        $totals = $svc->computeTotalsForStudent($student);

        $this->assertSame(1000.0, (float) $totals['baseTuition']);
        $this->assertSame(500.0, (float) $totals['chargesTotal']);
        $this->assertSame(300.0, (float) $totals['discountsTotal']);
        $this->assertSame(1200.0, (float) $totals['totalAmount']);
    }

    public function test_stackable_then_non_stackable_applies_until_exclusive_hit(): void
    {
        $grade = 'Grade 11';

        TuitionFee::create([
            'grade_level' => $grade,
            'amount' => 1000.00,
            'school_year' => 'SY 2025-2026',
            'semester' => 'N/A',
            'is_active' => true,
        ]);

        AdditionalCharge::create([
            'charge_name' => 'Lab',
            'description' => null,
            'amount' => 500.00,
            'applicable_grades' => [$grade],
            'is_active' => true,
            'is_mandatory' => true,
        ]);

        Discount::create([
            'discount_name' => 'Stackable Total 10%',
            'type' => 'percentage',
            'value' => 10.0,
            'eligibility_rules' => ['apply_scope' => 'total', 'is_stackable' => true],
            'applicable_grades' => [$grade],
            'is_active' => true,
            'is_automatic' => true,
            'priority' => 20,
        ]);

        Discount::create([
            'discount_name' => 'Non-Stackable Charges Only ₱300',
            'type' => 'fixed',
            'value' => 300.00,
            'eligibility_rules' => ['apply_scope' => 'charges_only', 'is_stackable' => false],
            'applicable_grades' => [$grade],
            'is_active' => true,
            'is_automatic' => true,
            'priority' => 10,
        ]);

        $student = Student::create([
            'student_id' => 'STU_X_2',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'level' => $grade,
            'section' => 'A',
            'school_year' => 'SY 2025-2026',
            'enrollment_status' => 'Active',
        ]);

        $svc = app(FeeManagementService::class);
        $totals = $svc->computeTotalsForStudent($student);

        $this->assertSame(1000.0, (float) $totals['baseTuition']);
        $this->assertSame(500.0, (float) $totals['chargesTotal']);
        $this->assertSame(450.0, (float) $totals['discountsTotal']);
        $this->assertSame(1050.0, (float) $totals['totalAmount']);
    }

    public function test_sibling_discount_applies_when_two_children_share_parent(): void
    {
        $gradeA = 'Grade 7';
        $gradeB = 'Grade 9';

        TuitionFee::create([
            'grade_level' => $gradeA,
            'amount' => 1000.00,
            'school_year' => 'SY 2025-2026',
            'semester' => 'N/A',
            'is_active' => true,
        ]);

        TuitionFee::create([
            'grade_level' => $gradeB,
            'amount' => 1000.00,
            'school_year' => 'SY 2025-2026',
            'semester' => 'N/A',
            'is_active' => true,
        ]);

        $parent = ParentContact::create([
            'full_name' => 'Parent Paz',
            'phone' => '09123456789',
            'account_status' => 'Active',
        ]);

        $child1 = Student::create([
            'student_id' => 'STU_SIB_1',
            'first_name' => 'Child',
            'last_name' => 'One',
            'level' => $gradeA,
            'section' => 'A',
            'school_year' => 'SY 2025-2026',
            'enrollment_status' => 'Active',
        ]);

        $child2 = Student::create([
            'student_id' => 'STU_SIB_2',
            'first_name' => 'Child',
            'last_name' => 'Two',
            'level' => $gradeB,
            'section' => 'B',
            'school_year' => 'SY 2025-2026',
            'enrollment_status' => 'Active',
        ]);

        $parent->students()->attach($child1->student_id, ['relationship' => 'Parent', 'is_primary' => true]);
        $parent->students()->attach($child2->student_id, ['relationship' => 'Parent', 'is_primary' => false]);

        Discount::ensureSiblingDefaults();

        $svc = app(FeeManagementService::class);

        $totalsChild1 = $svc->computeTotalsForStudent($child1); // Eldest → no sibling discount
        $totalsChild2 = $svc->computeTotalsForStudent($child2); // Second child → sibling discount

        $this->assertSame(0.0, (float) $totalsChild1['discountsTotal']);
        $this->assertGreaterThan(0.0, (float) $totalsChild2['discountsTotal']);
    }

    public function test_sibling_discount_does_not_apply_for_siblings_in_different_school_years(): void
    {
        $grade = 'Grade 7';

        TuitionFee::create([
            'grade_level' => $grade,
            'amount' => 1000.00,
            'school_year' => 'SY 2025-2026',
            'semester' => 'N/A',
            'is_active' => true,
        ]);

        $parent = ParentContact::create([
            'full_name' => 'Parent Cruz',
            'phone' => '09998887777',
            'account_status' => 'Active',
        ]);

        // Old Sibling (Active but different SY)
        $childOld = Student::create([
            'student_id' => 'STU_OLD_1',
            'first_name' => 'Child',
            'last_name' => 'Old',
            'level' => 'Grade 10',
            'section' => 'A',
            'school_year' => 'SY 2024-2025',
            'enrollment_status' => 'Active',
        ]);

        // New Student (Current SY)
        $childNew = Student::create([
            'student_id' => 'STU_NEW_1',
            'first_name' => 'Child',
            'last_name' => 'New',
            'level' => $grade,
            'section' => 'A',
            'school_year' => 'SY 2025-2026',
            'enrollment_status' => 'Active',
        ]);

        $parent->students()->attach($childOld->student_id, ['relationship' => 'Parent', 'is_primary' => true]);
        $parent->students()->attach($childNew->student_id, ['relationship' => 'Parent', 'is_primary' => true]);

        Discount::ensureSiblingDefaults();

        $svc = app(FeeManagementService::class);

        $totalsChildNew = $svc->computeTotalsForStudent($childNew);

        // Should be treated as first child in this SY, so no discount
        $this->assertSame(0.0, (float) $totalsChildNew['discountsTotal']);
    }
}
