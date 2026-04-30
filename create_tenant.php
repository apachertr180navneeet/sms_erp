<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Stancl\Tenancy\Database\Models\Tenant;

$tenant = Tenant::create([
    'id' => 'demo-school',
    'name' => 'Demo School',
    'subscription_active' => true,
    'subscription_starts_at' => now(),
    'subscription_ends_at' => now()->addYear(),
]);

$tenant->domains()->create(['domain' => 'demo.local']);

echo "Tenant created successfully!\n";
echo "ID: " . $tenant->id . "\n";
echo "Domain: demo.local\n";
