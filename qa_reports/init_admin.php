<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$logPath = __DIR__ . '/qa_records_log.json';
$log = [
    [
        'table' => 'admission_applications',
        'id' => 3,
        'identifier' => 'QA_Test_Applicant_01',
        'details' => ['application_no' => 'ADM-2026-0003', 'student_name' => 'QA_Test_Applicant_01', 'guardian_email' => 'qa_applicant_01@test.com']
    ],
    [
        'table' => 'career_applications',
        'id' => 3,
        'identifier' => 'QA_Test_Candidate_01',
        'details' => ['application_no' => 'JOB-2026-0003', 'full_name' => 'QA_Test_Candidate_01', 'email' => 'qa_candidate_01@test.com']
    ]
];

// Check if user already exists
$existing = DB::table('users')->where('email', 'qa_admin@gmail.com')->first();
if (!$existing) {
    $userId = DB::table('users')->insertGetId([
        'username' => 'qa_test_admin',
        'name' => 'QA_Test_Admin',
        'email' => 'qa_admin@gmail.com',
        'password' => Hash::make('QA_Admin@2026!'),
        'role_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "CREATED_USER_ID: " . $userId . "\n";
} else {
    $userId = $existing->id;
    echo "EXISTING_USER_ID: " . $userId . "\n";
}

$log[] = [
    'table' => 'users',
    'id' => $userId,
    'identifier' => 'qa_admin@gmail.com',
    'details' => [
        'username' => 'qa_test_admin',
        'name' => 'QA_Test_Admin',
        'email' => 'qa_admin@gmail.com',
        'role_id' => 1
    ]
];

file_put_contents($logPath, json_encode($log, JSON_PRETTY_PRINT));
echo "LOG_UPDATED: " . $logPath . "\n";
