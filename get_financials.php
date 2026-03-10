<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = new \App\Http\Controllers\SuperAdminController();
$response = $controller->dashboard();
$stats = $response->getData()['stats'];

echo 'Total Collected: ' . $stats['total_collected'] . PHP_EOL;
echo 'Outstanding Balance: ' . $stats['total_outstanding'] . PHP_EOL;
