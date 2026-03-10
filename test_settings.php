<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

(new \App\Http\Controllers\SuperAdminSettingsController())->update(new \Illuminate\Http\Request([
    'school_year' => '2025-2026',
    'institution_name' => 'Efees Test School',
    'maintenance_mode' => 'off',
]));

$log = \App\Models\AuditLog::where('action', 'System Settings Updated')->latest()->first();

echo 'Audit log created at: ' . $log->created_at . PHP_EOL;
echo 'New school year: ' . $log->new_values['school_year'];
