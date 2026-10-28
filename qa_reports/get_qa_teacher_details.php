<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$teacher = DB::table('teachers')->where('teacher_email', 'qa_teacher_01@gmail.com')->first();
if (!$teacher) {
    echo json_encode(['found' => false]);
    exit;
}

$user = DB::table('users')->where('email', 'qa_teacher_01@gmail.com')->first();
$salary = DB::table('teacher_salaries')->where('teacher_id', $teacher->id)->latest('id')->first();
$assignments = DB::table('teacher_assignments')->where('teacher_id', $teacher->id)->get();

echo json_encode([
    'found' => true,
    'teacher' => [
        'id' => $teacher->id,
        'teacher_name' => $teacher->teacher_name,
        'teacher_email' => $teacher->teacher_email,
        'teacher_phone' => $teacher->teacher_phone,
        'course' => $teacher->course,
        'login_user_id' => $teacher->login_user_id
    ],
    'user' => $user ? [
        'id' => $user->id,
        'username' => $user->username,
        'email' => $user->email,
        'role_id' => $user->role_id
    ] : null,
    'salary' => $salary ? [
        'id' => $salary->id,
        'teacher_id' => $salary->teacher_id,
        'month' => $salary->month,
        'basic_salary' => $salary->basic_salary,
        'net_salary' => $salary->net_salary,
        'status' => $salary->status
    ] : null,
    'assignments' => $assignments
], JSON_PRETTY_PRINT);