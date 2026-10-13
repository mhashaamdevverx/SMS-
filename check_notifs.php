<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "User 65:\n";
print_r(App\Models\User::find(65)?->toArray());

echo "\nUser 125:\n";
print_r(App\Models\User::find(125)?->toArray());

echo "\nTeacher ID 1:\n";
print_r(App\Models\Teacher::find(1)?->toArray());

echo "\nTeacher ID 61:\n";
print_r(App\Models\Teacher::find(61)?->toArray());
