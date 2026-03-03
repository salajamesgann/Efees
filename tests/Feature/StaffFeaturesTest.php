<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use App\Models\ParentContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $staffUser;
    protected Staff $staff;
    protected Role $staffRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staffRole = Role::firstOrCreate(
            ['role_name' => 'staff'],
            ['description' => 'Staff Role']
        );

        // Use DB facade to avoid timestamp column issues with SQLite migration
        DB::table('staff')->insert([
            'staff_id' => 'STF-TEST-001',
            'first_name' => 'Test',
            'last_name' => 'Staff',
            'contact_number' => '09171234567',
        ]);
        $this->staff = Staff::find('STF-TEST-001');

        $this->staffUser = User::create([
            'email' => 'staff@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->staffRole->role_id,
            'roleable_type' => Staff::class,
            'roleable_id' => $this->staff->staff_id,
        ]);
    }

    protected function actAsStaff()
    {
        return $this->actingAs($this->staffUser);
    }

    /**
     * Create a parent user for testing non-staff access.
     */
    protected function createParentUser(): User
    {
        $parentRole = Role::firstOrCreate(
            ['role_name' => 'parent'],
            ['description' => 'Parent Role']
        );

        $parentContact = ParentContact::create([
            'full_name' => 'Test Parent',
            'phone' => '09179876543',
            'email' => 'parent@test.com',
            'account_status' => 'Active',
        ]);

        return User::create([
            'email' => 'parent@test.com',
            'password' => Hash::make('password'),
            'role_id' => $parentRole->role_id,
            'roleable_type' => ParentContact::class,
            'roleable_id' => $parentContact->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    //  1. NOTIFICATION CENTER
    // ═══════════════════════════════════════════════════════════════

    public function test_notification_center_accessible_by_staff()
    {
        $response = $this->actAsStaff()->get(route('staff.notifications'));
        $response->assertStatus(200);
        $response->assertSee('Notifications');
    }

    public function test_notification_center_shows_notifications()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Payment Confirmed for STU-001',
            'body' => 'A payment of PHP 5,000 has been confirmed.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsStaff()->get(route('staff.notifications'));
        $response->assertStatus(200);
        $response->assertSee('Payment Confirmed for STU-001');
        $response->assertSee('A payment of PHP 5,000 has been confirmed.');
    }

    public function test_notification_mark_as_read()
    {
        $notifId = DB::table('notifications')->insertGetId([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Test Notification',
            'body' => 'Unread notification body.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsStaff()->postJson(route('staff.notifications.read', $notifId));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertNotNull(
            DB::table('notifications')->where('id', $notifId)->value('read_at')
        );
    }

    public function test_notification_mark_all_as_read()
    {
        DB::table('notifications')->insert([
            ['user_id' => $this->staffUser->user_id, 'title' => 'Notif 1', 'body' => 'Body 1', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $this->staffUser->user_id, 'title' => 'Notif 2', 'body' => 'Body 2', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actAsStaff()->postJson(route('staff.notifications.readAll'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $unread = DB::table('notifications')
            ->where('user_id', $this->staffUser->user_id)
            ->whereNull('read_at')
            ->count();

        $this->assertEquals(0, $unread);
    }

    public function test_notification_unread_count_json()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id, 'title' => 'N1', 'body' => 'B1', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id, 'title' => 'N2', 'body' => 'B2', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id, 'title' => 'N3', 'body' => 'B3', 'read_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        $response = $this->actAsStaff()->getJson(route('staff.notifications.unreadCount'));
        $response->assertStatus(200);
        $response->assertJson(['count' => 2]);
    }

    public function test_notification_center_blocked_for_non_staff()
    {
        $parentUser = $this->createParentUser();

        $response = $this->actingAs($parentUser)->get(route('staff.notifications'));
        $response->assertStatus(403);
    }

    public function test_cannot_mark_other_users_notification_as_read()
    {
        $parentUser = $this->createParentUser();

        $notifId = DB::table('notifications')->insertGetId([
            'user_id' => $parentUser->user_id,
            'title' => 'Parent Notif',
            'body' => 'Belongs to parent.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Staff cannot mark parent's notification as read
        $response = $this->actAsStaff()->postJson(route('staff.notifications.read', $notifId));
        $response->assertStatus(404);

        $this->assertNull(
            DB::table('notifications')->where('id', $notifId)->value('read_at')
        );
    }

    public function test_notification_pagination_exists()
    {
        // Insert 25 notifications to trigger pagination (page size is 20)
        for ($i = 1; $i <= 25; $i++) {
            DB::table('notifications')->insert([
                'user_id' => $this->staffUser->user_id,
                'title' => "Notification $i",
                'body' => "Body $i",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->actAsStaff()->get(route('staff.notifications'));
        $response->assertStatus(200);
        // Should see pagination (page 2 link)
        $response->assertSee('page=2', false);
    }

    // ═══════════════════════════════════════════════════════════════
    //  2. AUDIT TRAIL / ACTIVITY LOG
    // ═══════════════════════════════════════════════════════════════

    public function test_audit_trail_accessible_by_staff()
    {
        $response = $this->actAsStaff()->get(route('staff.audit_trail'));
        $response->assertStatus(200);
        $response->assertSee('Activity Log');
    }

    public function test_audit_trail_shows_own_logs()
    {
        AuditLog::create([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'update_fee',
            'model_type' => 'FeeRecord',
            'model_id' => 'FR-001',
            'details' => 'Updated tuition fee for STU-001',
            'old_values' => ['amount' => 5000],
            'new_values' => ['amount' => 6000],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actAsStaff()->get(route('staff.audit_trail'));
        $response->assertStatus(200);
        $response->assertSee('Updated tuition fee for STU-001');
        $response->assertSee('update_fee');
    }

    public function test_audit_trail_does_not_show_other_users_logs()
    {
        $parentUser = $this->createParentUser();

        AuditLog::create([
            'user_id' => $parentUser->user_id,
            'user_role' => 'parent',
            'action' => 'view_payment',
            'model_type' => 'Payment',
            'model_id' => 'PAY-999',
            'details' => 'Parent viewed payment details',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actAsStaff()->get(route('staff.audit_trail'));
        $response->assertStatus(200);
        $response->assertDontSee('Parent viewed payment details');
    }

    public function test_audit_trail_filter_by_action_type()
    {
        AuditLog::create([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'create_payment',
            'model_type' => 'Payment',
            'model_id' => 'PAY-001',
            'details' => 'Created payment for STU-001',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'send_sms',
            'model_type' => 'SmsLog',
            'model_id' => 'SMS-001',
            'details' => 'Sent SMS reminder',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actAsStaff()->get(route('staff.audit_trail', ['action' => 'create_payment']));
        $response->assertStatus(200);
        $response->assertSee('Created payment for STU-001');
        $response->assertDontSee('Sent SMS reminder');
    }

    public function test_audit_trail_filter_by_date_range()
    {
        // Use DB facade to set exact timestamps (Eloquent would override created_at)
        DB::table('audit_logs')->insert([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'update_fee',
            'model_type' => 'FeeRecord',
            'model_id' => 'FR-001',
            'details' => 'Old fee update',
            'ip_address' => '127.0.0.1',
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00',
        ]);

        DB::table('audit_logs')->insert([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'update_fee',
            'model_type' => 'FeeRecord',
            'model_id' => 'FR-002',
            'details' => 'Recent fee update',
            'ip_address' => '127.0.0.1',
            'created_at' => '2025-06-15 10:00:00',
            'updated_at' => '2025-06-15 10:00:00',
        ]);

        $response = $this->actAsStaff()->get(route('staff.audit_trail', ['from' => '2025-06-01', 'to' => '2025-06-30']));
        $response->assertStatus(200);
        $response->assertSee('Recent fee update');
        $response->assertDontSee('Old fee update');
    }

    public function test_audit_trail_search()
    {
        AuditLog::create([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'create_payment',
            'model_type' => 'Payment',
            'model_id' => 'PAY-001',
            'details' => 'Processed cash payment for Juan Dela Cruz',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'send_sms',
            'model_type' => 'SmsLog',
            'model_id' => 'SMS-001',
            'details' => 'Sent reminder to Maria Santos',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actAsStaff()->get(route('staff.audit_trail', ['search' => 'Juan Dela Cruz']));
        $response->assertStatus(200);
        $response->assertSee('Processed cash payment for Juan Dela Cruz');
        $response->assertDontSee('Sent reminder to Maria Santos');
    }

    public function test_audit_trail_blocked_for_non_staff()
    {
        $parentUser = $this->createParentUser();

        $response = $this->actingAs($parentUser)->get(route('staff.audit_trail'));
        $response->assertStatus(403);
    }

    public function test_audit_trail_shows_change_details()
    {
        AuditLog::create([
            'user_id' => $this->staffUser->user_id,
            'user_role' => 'staff',
            'action' => 'update_fee',
            'model_type' => 'FeeRecord',
            'model_id' => 'FR-001',
            'details' => 'Updated fee record amount',
            'old_values' => ['amount' => 5000, 'status' => 'pending'],
            'new_values' => ['amount' => 6000, 'status' => 'updated'],
            'ip_address' => '192.168.1.1',
        ]);

        $response = $this->actAsStaff()->get(route('staff.audit_trail'));
        $response->assertStatus(200);
        $response->assertSee('Updated fee record amount');
        $response->assertSee('192.168.1.1');
    }

    // ═══════════════════════════════════════════════════════════════
    //  3. SIDEBAR NAVIGATION
    // ═══════════════════════════════════════════════════════════════

    public function test_sidebar_includes_notification_link()
    {
        $response = $this->actAsStaff()->get(route('staff.notifications'));
        $response->assertStatus(200);
        $response->assertSee('Notifications');
        $response->assertSee('Activity Log');
    }

    public function test_sidebar_shows_notification_badge_count()
    {
        DB::table('notifications')->insert([
            ['user_id' => $this->staffUser->user_id, 'title' => 'N1', 'body' => 'B1', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $this->staffUser->user_id, 'title' => 'N2', 'body' => 'B2', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actAsStaff()->get(route('staff.notifications'));
        $response->assertStatus(200);
        $response->assertSee('notification-badge', false);
    }

    // ── Notification Filter & Search Tests ──────────────────────

    public function test_notification_filter_by_status_unread()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Unread Notice',
            'body' => 'Still unread.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Already Read',
            'body' => 'Was read.',
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsStaff()->get(route('staff.notifications', ['status' => 'unread']));
        $response->assertStatus(200);
        $response->assertSee('Unread Notice');
        $response->assertDontSee('Already Read');
    }

    public function test_notification_filter_by_status_read()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Unread Notice',
            'body' => 'Still unread.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Already Read',
            'body' => 'Was read.',
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsStaff()->get(route('staff.notifications', ['status' => 'read']));
        $response->assertStatus(200);
        $response->assertSee('Already Read');
        $response->assertDontSee('Unread Notice');
    }

    public function test_notification_filter_by_type()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Payment Received from Student',
            'body' => 'A payment was made.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'System Alert: Maintenance',
            'body' => 'Server maintenance tonight.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsStaff()->get(route('staff.notifications', ['type' => 'payment']));
        $response->assertStatus(200);
        $response->assertSee('Payment Received from Student');
        $response->assertDontSee('System Alert: Maintenance');
    }

    public function test_notification_search()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Fee Update for Grade 10',
            'body' => 'Tuition increased.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Link Request Approved',
            'body' => 'Parent linked successfully.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actAsStaff()->get(route('staff.notifications', ['search' => 'tuition']));
        $response->assertStatus(200);
        $response->assertSee('Fee Update for Grade 10');
        $response->assertDontSee('Link Request Approved');
    }

    public function test_notification_combined_filters()
    {
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Payment Confirmed',
            'body' => 'Payment body.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id' => $this->staffUser->user_id,
            'title' => 'Payment Old',
            'body' => 'Old payment.',
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Filter: type=payment + status=unread → only "Payment Confirmed"
        $response = $this->actAsStaff()->get(route('staff.notifications', [
            'type' => 'payment',
            'status' => 'unread',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Payment Confirmed');
        $response->assertDontSee('Payment Old');
    }
}
