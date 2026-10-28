<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$student = DB::table('students')->where('email', 'qa_student_01@gmail.com')->first();
if (!$student) {
    echo json_encode(['found' => false]);
    exit;
}

$user = DB::table('users')->where('email', 'qa_student_01@gmail.com')->first();
$voucher = DB::table('fee_collections')->where('student_id', $student->id)->latest('id')->first();

echo json_encode([
    'found' => true,
    'student' => [
        'id' => $student->id,
        'name' => $student->name,
        'email' => $student->email,
        'std_id' => $student->std_id,
        'registration_no' => $student->registration_no,
        'class' => $student->class,
        'section' => $student->section,
        'subject' => $student->subject
    ],
    'user' => $user ? [
        'id' => $user->id,
        'username' => $user->username,
        'email' => $user->email,
        'role_id' => $user->role_id
    ] : null,
    'voucher' => $voucher ? [
        'id' => $voucher->id,
        'voucher_no' => $voucher->voucher_no,
        'gross_amount' => $voucher->gross_amount,
        'net_amount' => $voucher->net_amount,
        'status' => $voucher->status
    ] : null
], JSON_PRETTY_PRINT);
