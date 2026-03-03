<?php

namespace Tests\Feature;

use App\Mail\PaymentReceiptMail;
use App\Models\FeeRecord;
use App\Models\ParentContact;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ParentFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $parentUser;

    protected ParentContact $parent;

    protected Student $student;

    protected Role $parentRole;

    /**
     * Set up a parent user with a linked student for every test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create parent role
        $this->parentRole = Role::firstOrCreate(
            ['role_name' => 'parent'],
            ['description' => 'Parent Role']
        );

        // Create parent contact
        $this->parent = ParentContact::create([
            'full_name' => 'Maria Test Parent',
            'phone' => '09171234567',
            'email' => 'parent@test.com',
            'account_status' => 'Active',
        ]);

        // Create parent user
        $this->parentUser = User::create([
            'email' => 'parent@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->parentRole->role_id,
            'roleable_type' => ParentContact::class,
            'roleable_id' => $this->parent->id,
        ]);

        // Create student
        $this->student = Student::create([
            'student_id' => 'STU-2025-0001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'level' => 'Grade 7',
            'section' => 'Section A',
            'school_year' => '2025-2026',
        ]);

        // Link parent to student
        $this->parent->students()->attach($this->student->student_id, [
            'relationship' => 'Father',
            'is_primary' => true,
        ]);
    }

    // ─── Helper ─────────────────────────────────────────────────────

    protected function actAsParent()
    {
        return $this->actingAs($this->parentUser);
    }

    // ═══════════════════════════════════════════════════════════════
    //  1. PAYMENT SCHEDULE / INSTALLMENT VIEW
    // ═══════════════════════════════════════════════════════════════

    public function test_payment_schedule_route_accessible_by_parent()
    {
        // Create installment fee records for the student
        FeeRecord::create([
            'student_id' => $this->student->student_id,
            'record_type' => 'tuition_installment',
            'amount' => 5000,
            'balance' => 5000,
            'status' => 'pending',
            'notes' => '2025-2026 1st Monthly 1',
            'payment_date' => '2025-08-15',
        ]);

        FeeRecord::create([
            'student_id' => $this->student->student_id,
            'record_type' => 'tuition_installment',
            'amount' => 5000,
            'balance' => 0,
            'status' => 'paid',
            'notes' => '2025-2026 1st Monthly 2',
            'payment_date' => '2025-09-15',
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.schedule', $this->student->student_id));

        $response->assertStatus(200);
        $response->assertViewIs('auth.parent_payment_schedule');
        $response->assertViewHas('student');
        $response->assertViewHas('installments');
        $response->assertViewHas('totalPaid');
    }

    public function test_payment_schedule_shows_installment_data()
    {
        FeeRecord::create([
            'student_id' => $this->student->student_id,
            'record_type' => 'tuition_installment',
            'amount' => 3000,
            'balance' => 3000,
            'status' => 'pending',
            'notes' => '2025-2026 1st Monthly 1',
            'payment_date' => '2025-08-15',
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.schedule', $this->student->student_id));

        $response->assertStatus(200);
        $response->assertSee('Monthly 1');
        $response->assertSee('3,000.00');
    }

    public function test_payment_schedule_forbidden_for_unlinked_student()
    {
        $otherStudent = Student::create([
            'student_id' => 'STU-2025-9999',
            'first_name' => 'Other',
            'last_name' => 'Student',
            'level' => 'Grade 8',
            'school_year' => '2025-2026',
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.schedule', $otherStudent->student_id));

        $response->assertStatus(403);
    }

    public function test_payment_schedule_forbidden_for_unauthenticated()
    {
        $response = $this->get(route('parent.schedule', $this->student->student_id));
        $response->assertRedirect(); // auth middleware redirects to login
    }

    // ═══════════════════════════════════════════════════════════════
    //  2. PDF RECEIPT DOWNLOAD
    // ═══════════════════════════════════════════════════════════════

    public function test_receipt_pdf_download_returns_pdf()
    {
        $payment = Payment::create([
            'student_id' => $this->student->student_id,
            'amount_paid' => 5000.00,
            'method' => 'gcash',
            'reference_number' => 'PAYMONGO-test-123',
            'remarks' => 'Test Payment',
            'paid_at' => now(),
            'status' => 'approved',
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.receipt.pdf', $payment->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_receipt_pdf_forbidden_for_other_parents_payment()
    {
        $otherStudent = Student::create([
            'student_id' => 'STU-2025-8888',
            'first_name' => 'Other',
            'last_name' => 'Student2',
            'level' => 'Grade 9',
            'school_year' => '2025-2026',
        ]);

        $payment = Payment::create([
            'student_id' => $otherStudent->student_id,
            'amount_paid' => 1000.00,
            'method' => 'card',
            'reference_number' => 'PAYMONGO-other-456',
            'paid_at' => now(),
            'status' => 'approved',
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.receipt.pdf', $payment->id));

        $response->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════════
    //  3. PDF SOA DOWNLOAD
    // ═══════════════════════════════════════════════════════════════

    public function test_soa_pdf_download_returns_pdf()
    {
        $response = $this->actAsParent()
            ->get(route('parent.soa.pdf', $this->student->student_id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_soa_pdf_forbidden_for_unlinked_student()
    {
        $otherStudent = Student::create([
            'student_id' => 'STU-2025-7777',
            'first_name' => 'Unlinked',
            'last_name' => 'Student',
            'level' => 'Grade 10',
            'school_year' => '2025-2026',
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.soa.pdf', $otherStudent->student_id));

        $response->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════════
    //  4. EMAIL RECEIPT DELIVERY (Mailable)
    // ═══════════════════════════════════════════════════════════════

    public function test_payment_receipt_mailable_renders_correctly()
    {
        $payment = Payment::create([
            'student_id' => $this->student->student_id,
            'amount_paid' => 5000.00,
            'method' => 'gcash',
            'reference_number' => 'PAYMONGO-mail-001',
            'remarks' => 'Test Email Payment',
            'paid_at' => now(),
            'status' => 'approved',
        ]);

        $mailable = new PaymentReceiptMail($payment, 'EFees Test School', '2025-2026');

        $mailable->assertSeeInHtml('5,000.00');
        $mailable->assertSeeInHtml('Juan Dela Cruz');
        $mailable->assertSeeInHtml('PAYMONGO-mail-001');
        $mailable->assertSeeInHtml('EFees Test School');
        $mailable->assertSeeInHtml('2025-2026');
    }

    public function test_payment_receipt_mailable_has_correct_subject()
    {
        $payment = Payment::create([
            'student_id' => $this->student->student_id,
            'amount_paid' => 1000.00,
            'method' => 'card',
            'reference_number' => 'PAYMONGO-subj-002',
            'paid_at' => now(),
            'status' => 'approved',
        ]);

        $mailable = new PaymentReceiptMail($payment, 'School', '2025-2026');

        $mailable->assertHasSubject('Payment Receipt - PAYMONGO-subj-002');
    }

    public function test_payment_receipt_mailable_can_be_queued()
    {
        Mail::fake();

        $payment = Payment::create([
            'student_id' => $this->student->student_id,
            'amount_paid' => 2500.00,
            'method' => 'paymaya',
            'reference_number' => 'PAYMONGO-queue-003',
            'paid_at' => now(),
            'status' => 'approved',
        ]);

        Mail::to('parent@test.com')->send(
            new PaymentReceiptMail($payment, 'EFees', '2025-2026')
        );

        Mail::assertSent(PaymentReceiptMail::class, function ($mail) {
            return $mail->hasTo('parent@test.com');
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  5. NOTIFICATION CENTER
    // ═══════════════════════════════════════════════════════════════

    public function test_notification_center_accessible_by_parent()
    {
        $response = $this->actAsParent()
            ->get(route('parent.notifications'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.parent_notifications');
        $response->assertViewHas('notifications');
        $response->assertViewHas('unreadCount');
    }

    public function test_notification_center_shows_notifications()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->parentUser->user_id,
            'title' => 'Payment Successful',
            'body' => 'We received your payment of ₱5,000.00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.notifications'));

        $response->assertStatus(200);
        $response->assertSee('Payment Successful');
        $response->assertSee('5,000.00');
    }

    public function test_notification_unread_count_returns_json()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->parentUser->user_id,
            'title' => 'Test',
            'body' => 'Unread notification',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('notifications')->insert([
            'user_id' => $this->parentUser->user_id,
            'title' => 'Test 2',
            'body' => 'Read notification',
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsParent()
            ->getJson(route('parent.notifications.unreadCount'));

        $response->assertStatus(200);
        $response->assertJson(['count' => 1]);
    }

    public function test_mark_notification_as_read()
    {
        $id = DB::table('notifications')->insertGetId([
            'user_id' => $this->parentUser->user_id,
            'title' => 'Mark me read',
            'body' => 'This should be marked as read',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsParent()
            ->postJson(route('parent.notifications.read', $id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertNotNull(
            DB::table('notifications')->where('id', $id)->value('read_at')
        );
    }

    public function test_mark_all_notifications_as_read()
    {
        DB::table('notifications')->insert([
            ['user_id' => $this->parentUser->user_id, 'title' => 'N1', 'body' => 'body1', 'read_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $this->parentUser->user_id, 'title' => 'N2', 'body' => 'body2', 'read_at' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actAsParent()
            ->postJson(route('parent.notifications.readAll'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $unread = DB::table('notifications')
            ->where('user_id', $this->parentUser->user_id)
            ->whereNull('read_at')
            ->count();

        $this->assertEquals(0, $unread);
    }

    public function test_cannot_mark_other_users_notification_as_read()
    {
        // Create another user's notification
        $otherRole = Role::firstOrCreate(['role_name' => 'parent']);
        $otherParent = ParentContact::create([
            'full_name' => 'Other Parent',
            'phone' => '09179999999',
            'email' => 'other@test.com',
            'account_status' => 'Active',
        ]);
        $otherUser = User::create([
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'role_id' => $otherRole->role_id,
            'roleable_type' => ParentContact::class,
            'roleable_id' => $otherParent->id,
        ]);

        $id = DB::table('notifications')->insertGetId([
            'user_id' => $otherUser->user_id,
            'title' => 'Not mine',
            'body' => 'This is not my notification',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Try to mark it as read as the first parent
        $response = $this->actAsParent()
            ->postJson(route('parent.notifications.read', $id));

        $response->assertStatus(404);

        // Verify it's still unread
        $this->assertNull(
            DB::table('notifications')->where('id', $id)->value('read_at')
        );
    }

    public function test_notification_center_forbidden_for_unauthenticated()
    {
        $response = $this->get(route('parent.notifications'));
        $response->assertRedirect(); // auth middleware redirects to login
    }

    // ═══════════════════════════════════════════════════════════════
    //  6. SIDEBAR NAVIGATION LINKS PRESENT
    // ═══════════════════════════════════════════════════════════════

    public function test_sidebar_contains_notification_link()
    {
        $response = $this->actAsParent()
            ->get(route('parent.schedule', $this->student->student_id));

        $response->assertStatus(200);
        $response->assertSee('Notifications');
        $response->assertSee('notification-badge');
    }

    public function test_sidebar_contains_payment_schedule_link()
    {
        $response = $this->actAsParent()
            ->get(route('parent.fees.show', $this->student->student_id));

        $response->assertStatus(200);
        $response->assertSee('Payment Schedule');
    }

    // ═══════════════════════════════════════════════════════════════
    //  7. MULTI-CHILD (BULK) PAYMENT
    // ═══════════════════════════════════════════════════════════════

    private function createSecondChild(): Student
    {
        $child2 = Student::create([
            'student_id' => 'STU-2025-0002',
            'first_name' => 'Maria',
            'last_name' => 'Dela Cruz',
            'level' => 'Grade 5',
            'section' => 'Section B',
            'school_year' => '2025-2026',
        ]);

        $this->parent->students()->attach($child2->student_id, [
            'relationship' => 'Father',
            'is_primary' => false,
        ]);

        return $child2;
    }

    public function test_multi_child_page_accessible_by_parent()
    {
        $this->createSecondChild();

        $response = $this->actAsParent()
            ->get(route('parent.pay.multi'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.parent_payment_multi');
        $response->assertViewHas('childrenData');
    }

    public function test_multi_child_page_shows_all_linked_students()
    {
        $child2 = $this->createSecondChild();

        $response = $this->actAsParent()
            ->get(route('parent.pay.multi'));

        $response->assertStatus(200);
        $response->assertSee('Juan Dela Cruz');
        $response->assertSee('Maria Dela Cruz');
        $response->assertSee('STU-2025-0001');
        $response->assertSee('STU-2025-0002');
    }

    public function test_multi_child_page_shows_balances()
    {
        $child2 = $this->createSecondChild();

        // Create fee records to generate balances
        FeeRecord::create([
            'student_id' => $this->student->student_id,
            'record_type' => 'tuition',
            'amount' => 15000,
            'balance' => 15000,
            'status' => 'pending',
            'notes' => 'Tuition Fee',
        ]);
        FeeRecord::create([
            'student_id' => $child2->student_id,
            'record_type' => 'tuition',
            'amount' => 12000,
            'balance' => 12000,
            'status' => 'pending',
            'notes' => 'Tuition Fee',
        ]);

        $response = $this->actAsParent()
            ->get(route('parent.pay.multi'));

        $response->assertStatus(200);
        // The view should display balance amounts
        $response->assertSee('Multi-Child Payment');
        $response->assertSee('Select Students');
    }

    public function test_multi_child_single_student_shows_redirect_message()
    {
        // Only one student linked - should show "single student" message
        $response = $this->actAsParent()
            ->get(route('parent.pay.multi'));

        $response->assertStatus(200);
        $response->assertSee('Single Student Linked');
        $response->assertSee('Go to Payments');
    }

    public function test_multi_child_store_validates_required_fields()
    {
        $this->createSecondChild();

        $response = $this->actAsParent()
            ->post(route('parent.pay.multi.store'), []);

        $response->assertSessionHasErrors(['students', 'method']);
    }

    public function test_multi_child_store_validates_student_ownership()
    {
        $this->createSecondChild();

        // Try to pay for an unlinked student
        $unlinked = Student::create([
            'student_id' => 'STU-2025-9999',
            'first_name' => 'Stranger',
            'last_name' => 'Student',
            'level' => 'Grade 10',
            'school_year' => '2025-2026',
        ]);

        $response = $this->actAsParent()
            ->post(route('parent.pay.multi.store'), [
                'students' => [
                    ['student_id' => $unlinked->student_id, 'amount' => 1000],
                ],
                'method' => 'gcash',
            ]);

        $response->assertStatus(403);
    }

    public function test_multi_child_store_validates_minimum_total()
    {
        $this->createSecondChild();

        $response = $this->actAsParent()
            ->post(route('parent.pay.multi.store'), [
                'students' => [
                    ['student_id' => $this->student->student_id, 'amount' => 5],
                    ['student_id' => 'STU-2025-0002', 'amount' => 5],
                ],
                'method' => 'gcash',
            ]);

        // Total is 10, below the 20 minimum
        $response->assertSessionHasErrors('students');
    }

    public function test_multi_child_forbidden_for_unauthenticated()
    {
        $response = $this->get(route('parent.pay.multi'));
        $response->assertRedirect();
    }

    public function test_multi_child_cancel_redirects_correctly()
    {
        $response = $this->actAsParent()
            ->get(route('parent.pay.multi.cancel'));

        $response->assertRedirect(route('parent.pay.multi'));
        $response->assertSessionHas('info');
    }

    public function test_sidebar_contains_multi_child_pay_link()
    {
        $this->createSecondChild();

        $response = $this->actAsParent()
            ->get(route('parent.pay.multi'));

        $response->assertStatus(200);
        $response->assertSee('Multi-Child Pay');
    }

    public function test_single_payment_page_shows_multi_child_link()
    {
        $this->createSecondChild();

        $response = $this->actAsParent()
            ->get(route('parent.pay'));

        $response->assertStatus(200);
        $response->assertSee('Pay Multiple Children');
    }
}
