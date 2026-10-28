<?php
$logPath = __DIR__ . '/qa_records_log.json';
$log = json_decode(file_get_contents($logPath), true) ?: [];

$log[] = [
    'table' => 'students',
    'id' => 72,
    'identifier' => 'qa_student_01@gmail.com',
    'details' => [
        'name' => 'QA_Test Student_01',
        'std_id' => 6,
        'registration_no' => 'REG-2026-ONE-006',
        'class' => 'ONE',
        'section' => 'A'
    ]
];

$log[] = [
    'table' => 'users',
    'id' => 134,
    'identifier' => 'qa_student_01@gmail.com',
    'details' => [
        'username' => 'QA_Test Student_01',
        'email' => 'qa_student_01@gmail.com',
        'role_id' => 4
    ]
];

$log[] = [
    'table' => 'fee_collections',
    'id' => 611,
    'identifier' => 'V-S72-1026',
    'details' => [
        'voucher_no' => 'V-S72-1026',
        'student_id' => 72,
        'gross_amount' => 8500.00,
        'net_amount' => 8500.00,
        'status' => 'Pending'
    ]
];

file_put_contents($logPath, json_encode($log, JSON_PRETTY_PRINT));
echo "QA_RECORDS_LOG_UPDATED_WITH_STUDENT_USER_VOUCHER\n";
