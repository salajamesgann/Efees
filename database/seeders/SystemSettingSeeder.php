<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'school_name' => 'E-Fees Academy',
            'school_year' => '2025-2026', // Default active year
            'semester' => 'Full Year',
            'school_address' => '123 Education Street, Learning City, 1000',
            'school_logo' => 'images/logo.png', // Placeholder
            'contact_email' => 'admin@efees.edu.ph',
            'contact_phone' => '09123456789',
            'currency_symbol' => '₱',
            'payment_instructions' => "Please pay at the cashier's office or use our online payment partners.\nBank Transfer: BDO 123-456-7890",
            'auto_generate_fees_on_enrollment' => '1',
            'notifications_enabled' => '1',
            'maintenance_mode' => '0',
            'allow_staff_edit_fees' => '0',
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
