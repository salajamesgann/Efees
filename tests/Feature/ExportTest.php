<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_reports_csv()
    {
        $admin = new User;
        $admin->email = 'admin@example.com';
        $admin->password = bcrypt('password');

        $role = \App\Models\Role::firstOrCreate(['role_name' => 'admin']);
        $admin->role_id = $role->role_id;

        $staff = new \App\Models\Staff;
        $staff->staff_id = 'S-001';
        $staff->first_name = 'Admin';
        $staff->last_name = 'User';
        $staff->contact_number = '09171234567';
        $staff->created_at = now();
        $staff->updated_at = now();
        // $staff->email = 'admin@example.com';
        $staff->save();

        $admin->roleable_type = \App\Models\Staff::class;
        $admin->roleable_id = $staff->staff_id;

        $admin->save();

        $this->actingAs($admin);

        $response = $this->post(route('admin.reports.export.csv'), [
            'report_type' => 'payments',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'format' => 'csv',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_export_reports_pdf()
    {
        $admin = new User;
        $admin->email = 'admin2@example.com';
        $admin->password = bcrypt('password');

        $role = \App\Models\Role::firstOrCreate(['role_name' => 'admin']);
        $admin->role_id = $role->role_id;

        $staff = new \App\Models\Staff;
        $staff->staff_id = 'S-002';
        $staff->first_name = 'Admin2';
        $staff->last_name = 'User';
        $staff->contact_number = '09171234568';
        $staff->created_at = now();
        $staff->updated_at = now();
        // $staff->email = 'admin2@example.com';
        $staff->save();

        $admin->roleable_type = \App\Models\Staff::class;
        $admin->roleable_id = $staff->staff_id;

        $admin->save();

        $this->actingAs($admin);

        $response = $this->post(route('admin.reports.export.csv'), [
            'report_type' => 'payments',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'format' => 'pdf',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_export_reports_excel()
    {
        $admin = new User;
        $admin->email = 'admin3@example.com';
        $admin->password = bcrypt('password');

        $role = \App\Models\Role::firstOrCreate(['role_name' => 'admin']);
        $admin->role_id = $role->role_id;

        $staff = new \App\Models\Staff;
        $staff->staff_id = 'S-003';
        $staff->first_name = 'Admin3';
        $staff->last_name = 'User';
        $staff->contact_number = '09171234569';
        $staff->created_at = now();
        $staff->updated_at = now();
        // $staff->email = 'admin3@example.com';
        $staff->save();

        $admin->roleable_type = \App\Models\Staff::class;
        $admin->roleable_id = $staff->staff_id;

        $admin->save();

        $this->actingAs($admin);

        $response = $this->post(route('admin.reports.export.csv'), [
            'report_type' => 'payments',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'format' => 'xlsx', // Assuming controller handles 'xlsx' mapped to Excel
        ]);

        // Note: Excel export might need Maatwebsite\Excel which sets specific headers
        // Just checking status 200 is often enough for feature test, or check disposition
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheet') ||
            str_contains($response->headers->get('content-type'), 'excel') ||
            str_contains($response->headers->get('content-disposition'), '.xlsx')
        );
    }
}
