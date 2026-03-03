<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ParentContact;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentLinkRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LinkApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $parentUser;

    protected ParentContact $parent;

    protected Student $student;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        $parentRole = Role::firstOrCreate(
            ['role_name' => 'parent'],
            ['description' => 'Parent Role']
        );
        $adminRole = Role::firstOrCreate(
            ['role_name' => 'admin'],
            ['description' => 'Admin Role']
        );

        // Parent
        $this->parent = ParentContact::create([
            'full_name' => 'Maria Test Parent',
            'phone' => '09171234567',
            'email' => 'parent@test.com',
            'account_status' => 'Active',
        ]);

        $this->parentUser = User::create([
            'email' => 'parent@test.com',
            'password' => bcrypt('password'),
            'role_id' => $parentRole->role_id,
            'roleable_type' => ParentContact::class,
            'roleable_id' => $this->parent->id,
        ]);

        // Student
        $this->student = Student::create([
            'student_id' => 'STU-2025-0001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'level' => 'Grade 7',
            'section' => 'Section A',
            'school_year' => '2025-2026',
        ]);

        // Admin
        $admin = Admin::create([
            'admin_id' => 'ADM-001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'contact_number' => '09170000000',
        ]);
        $this->adminUser = User::create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->role_id,
            'roleable_type' => Admin::class,
            'roleable_id' => $admin->admin_id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    //  PARENT: LINK REQUEST
    // ═══════════════════════════════════════════════════════════════

    public function test_parent_can_submit_link_request()
    {
        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.link_student'), [
                'student_id' => $this->student->student_id,
                'relationship' => 'Father',
                'reason' => 'Enrolling my child',
            ]);

        $response->assertRedirect(route('parent.dashboard'));
        $response->assertSessionHas('success');

        // A pending request was created
        $this->assertDatabaseHas('student_link_requests', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'pending',
            'relationship' => 'Father',
        ]);

        // Student is NOT actually linked yet
        $this->assertDatabaseMissing('parent_student', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
        ]);
    }

    public function test_parent_cannot_submit_duplicate_pending_link_request()
    {
        StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.link_student'), [
                'student_id' => $this->student->student_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, StudentLinkRequest::where('parent_id', $this->parent->id)
            ->where('type', 'link')
            ->where('status', 'pending')
            ->count());
    }

    public function test_parent_cannot_link_already_linked_student()
    {
        // Directly attach (simulate already approved link)
        $this->parent->students()->attach($this->student->student_id, [
            'relationship' => 'Father',
            'is_primary' => true,
        ]);

        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.link_student'), [
                'student_id' => $this->student->student_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, StudentLinkRequest::count());
    }

    public function test_link_request_validates_student_exists()
    {
        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.link_student'), [
                'student_id' => 'NON-EXISTENT',
            ]);

        $response->assertSessionHasErrors('student_id');
    }

    // ═══════════════════════════════════════════════════════════════
    //  PARENT: UNLINK REQUEST
    // ═══════════════════════════════════════════════════════════════

    public function test_parent_can_submit_unlink_request()
    {
        // Must be currently linked
        $this->parent->students()->attach($this->student->student_id, [
            'relationship' => 'Father',
            'is_primary' => true,
        ]);

        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.unlink_student'), [
                'student_id' => $this->student->student_id,
                'reason' => 'Student transferred',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('student_link_requests', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'unlink',
            'status' => 'pending',
        ]);

        // Student is still linked (not removed yet)
        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
        ]);
    }

    public function test_parent_cannot_unlink_student_not_linked()
    {
        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.unlink_student'), [
                'student_id' => $this->student->student_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, StudentLinkRequest::count());
    }

    public function test_parent_cannot_submit_duplicate_pending_unlink_request()
    {
        $this->parent->students()->attach($this->student->student_id, [
            'relationship' => 'Father',
            'is_primary' => true,
        ]);

        StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'unlink',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.unlink_student'), [
                'student_id' => $this->student->student_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, StudentLinkRequest::where('type', 'unlink')->where('status', 'pending')->count());
    }

    // ═══════════════════════════════════════════════════════════════
    //  ADMIN: INDEX
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_can_view_link_approvals_page()
    {
        StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.link_approvals.index'));

        $response->assertStatus(200);
        $response->assertSee('Student Link Approvals');
        $response->assertSee($this->student->student_id);
    }

    public function test_non_admin_cannot_access_link_approvals()
    {
        $response = $this->actingAs($this->parentUser)
            ->get(route('admin.link_approvals.index'));

        $response->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════════
    //  ADMIN: APPROVE
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_approve_link_request_attaches_student()
    {
        $linkRequest = StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'pending',
            'relationship' => 'Mother',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.link_approvals.approve', $linkRequest->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $linkRequest->refresh();
        $this->assertEquals('approved', $linkRequest->status);
        $this->assertEquals($this->adminUser->user_id, $linkRequest->reviewed_by);
        $this->assertNotNull($linkRequest->reviewed_at);

        // Student is now actually linked
        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'relationship' => 'Mother',
        ]);
    }

    public function test_admin_approve_unlink_request_detaches_student()
    {
        // First link the student
        $this->parent->students()->attach($this->student->student_id, [
            'relationship' => 'Father',
            'is_primary' => true,
        ]);

        $linkRequest = StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'unlink',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.link_approvals.approve', $linkRequest->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $linkRequest->refresh();
        $this->assertEquals('approved', $linkRequest->status);

        // Student is now actually unlinked
        $this->assertDatabaseMissing('parent_student', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
        ]);
    }

    public function test_admin_cannot_approve_already_processed_request()
    {
        $linkRequest = StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'rejected',
            'reviewed_by' => $this->adminUser->user_id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.link_approvals.approve', $linkRequest->id));

        $response->assertSessionHas('error', 'This request has already been processed.');

        // Student remains unlinked
        $this->assertDatabaseMissing('parent_student', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    //  ADMIN: REJECT
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_reject_request_with_remarks()
    {
        $linkRequest = StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.link_approvals.reject', $linkRequest->id), [
                'admin_remarks' => 'Unable to verify relationship',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $linkRequest->refresh();
        $this->assertEquals('rejected', $linkRequest->status);
        $this->assertEquals('Unable to verify relationship', $linkRequest->admin_remarks);
        $this->assertEquals($this->adminUser->user_id, $linkRequest->reviewed_by);

        // Student remains unlinked
        $this->assertDatabaseMissing('parent_student', [
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
        ]);
    }

    public function test_admin_cannot_reject_already_processed_request()
    {
        $linkRequest = StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'approved',
            'reviewed_by' => $this->adminUser->user_id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.link_approvals.reject', $linkRequest->id), [
                'admin_remarks' => 'Nope',
            ]);

        $response->assertSessionHas('error', 'This request has already been processed.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  NOTIFICATIONS
    // ═══════════════════════════════════════════════════════════════

    public function test_admin_notified_on_link_request()
    {
        $this->actingAs($this->parentUser)
            ->post(route('parent.link_student'), [
                'student_id' => $this->student->student_id,
            ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->adminUser->user_id,
            'title' => 'Student Link Request',
        ]);
    }

    public function test_parent_notified_on_approval()
    {
        $linkRequest = StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'pending',
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('admin.link_approvals.approve', $linkRequest->id));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->parentUser->user_id,
            'title' => 'Link Request Approved',
        ]);
    }

    public function test_parent_notified_on_rejection()
    {
        $linkRequest = StudentLinkRequest::create([
            'parent_id' => $this->parent->id,
            'student_id' => $this->student->student_id,
            'type' => 'link',
            'status' => 'pending',
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('admin.link_approvals.reject', $linkRequest->id), [
                'admin_remarks' => 'Cannot verify',
            ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->parentUser->user_id,
            'title' => 'Link Request Rejected',
        ]);
    }
}
