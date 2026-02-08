<?php

namespace Tests\Feature;

use App\Models\Discount;
use App\Models\FeeAssignment;
use App\Models\ParentContact;
use App\Models\Student;
use App\Models\TuitionFee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiblingDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_applies_5_percent_tuition_only_for_two_siblings(): void
    {
        Discount::ensureSiblingDefaults();

        $parent = ParentContact::create([
            'full_name' => 'Parent One',
            'phone' => '09999999999',
            'account_status' => 'Active',
        ]);

        $tuition = TuitionFee::create([
            'grade_level' => 'Grade 10',
            'amount' => 10000.00,
            'school_year' => '2026-2027',
            'semester' => '1st',
            'is_active' => true,
        ]);

        $s1 = Student::create([
            'student_id' => 'S-1001',
            'first_name' => 'A',
            'last_name' => 'Alpha',
            'level' => 'Grade 10',
            'school_year' => '2026-2027',
            'enrollment_status' => 'Active',
        ]);
        $s2 = Student::create([
            'student_id' => 'S-1002',
            'first_name' => 'B',
            'last_name' => 'Beta',
            'level' => 'Grade 10',
            'school_year' => '2026-2027',
            'enrollment_status' => 'Active',
        ]);

        $parent->students()->syncWithoutDetaching([
            $s1->student_id => ['relationship' => 'Parent', 'is_primary' => true],
            $s2->student_id => ['relationship' => 'Parent', 'is_primary' => false],
        ]);

        $fa1 = FeeAssignment::assignForStudent($s1->student_id, '2026-2027', '1st');
        $fa2 = FeeAssignment::assignForStudent($s2->student_id, '2026-2027', '1st');

        $this->assertNotNull($fa1);
        $this->assertNotNull($fa2);

        $fa1->refresh();
        $fa2->refresh();

        $this->assertEquals(10000.00, (float) $fa1->base_tuition);
        $this->assertEquals(500.00, (float) $fa1->discounts_total);
        $this->assertEquals(9500.00, (float) $fa1->total_amount);

        $this->assertEquals(10000.00, (float) $fa2->base_tuition);
        $this->assertEquals(500.00, (float) $fa2->discounts_total);
        $this->assertEquals(9500.00, (float) $fa2->total_amount);
    }

    public function test_discount_does_not_exceed_5_percent_with_many_siblings(): void
    {
        Discount::ensureSiblingDefaults();

        $parent = ParentContact::create([
            'full_name' => 'Parent Two',
            'phone' => '09888888888',
            'account_status' => 'Active',
        ]);

        TuitionFee::create([
            'grade_level' => 'Grade 9',
            'amount' => 20000.00,
            'school_year' => '2026-2027',
            'semester' => '1st',
            'is_active' => true,
        ]);

        $students = [];
        for ($i = 0; $i < 5; $i++) {
            $students[$i] = Student::create([
                'student_id' => 'S-200'.($i + 1),
                'first_name' => 'Child'.($i + 1),
                'last_name' => 'Gamma',
                'level' => 'Grade 9',
                'school_year' => '2026-2027',
                'enrollment_status' => 'Active',
            ]);
            $parent->students()->syncWithoutDetaching([
                $students[$i]->student_id => ['relationship' => 'Parent', 'is_primary' => $i === 0],
            ]);
        }

        $fa = FeeAssignment::assignForStudent($students[4]->student_id, '2026-2027', '1st');
        $fa->refresh();

        $this->assertEquals(1000.00, (float) $fa->discounts_total);
        $this->assertEquals(19000.00, (float) $fa->total_amount);
    }

    public function test_discount_removed_when_sibling_unlinked(): void
    {
        Discount::ensureSiblingDefaults();

        $parent = ParentContact::create([
            'full_name' => 'Parent Three',
            'phone' => '09777777777',
            'account_status' => 'Active',
        ]);

        $tuition = TuitionFee::create([
            'grade_level' => 'Grade 8',
            'amount' => 15000.00,
            'school_year' => '2026-2027',
            'semester' => '1st',
            'is_active' => true,
        ]);

        $s1 = Student::create([
            'student_id' => 'S-3001',
            'first_name' => 'C',
            'last_name' => 'Chi',
            'level' => 'Grade 8',
            'school_year' => '2026-2027',
            'enrollment_status' => 'Active',
        ]);
        $s2 = Student::create([
            'student_id' => 'S-3002',
            'first_name' => 'D',
            'last_name' => 'Delta',
            'level' => 'Grade 8',
            'school_year' => '2026-2027',
            'enrollment_status' => 'Active',
        ]);

        $parent->students()->syncWithoutDetaching([
            $s1->student_id => ['relationship' => 'Parent', 'is_primary' => true],
            $s2->student_id => ['relationship' => 'Parent', 'is_primary' => false],
        ]);

        $fa1 = FeeAssignment::assignForStudent($s1->student_id, '2026-2027', '1st');
        $fa1->refresh();
        $this->assertEquals(750.00, (float) $fa1->discounts_total);
        $this->assertEquals(14250.00, (float) $fa1->total_amount);

        $parent->students()->detach($s2->student_id);

        $fa1->discounts()->detach();
        $fa1->calculateTotal();
        $fa1->refresh();

        $this->assertEquals(0.00, (float) $fa1->discounts_total);
        $this->assertEquals(15000.00, (float) $fa1->total_amount);
    }
}
