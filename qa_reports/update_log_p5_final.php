<?php
$logPath = __DIR__ . '/qa_records_log.json';
$log = [
    [
        'table' => 'admission_applications',
        'id' => 3,
        'identifier' => 'QA_Test_Applicant_01',
        'status' => 'active_in_db',
        'details' => ['application_no' => 'ADM-2026-0003', 'student_name' => 'QA_Test_Applicant_01', 'guardian_email' => 'qa_applicant_01@test.com']
    ],
    [
        'table' => 'career_applications',
        'id' => 3,
        'identifier' => 'QA_Test_Candidate_01',
        'status' => 'active_in_db',
        'details' => ['application_no' => 'JOB-2026-0003', 'full_name' => 'QA_Test_Candidate_01', 'email' => 'qa_candidate_01@test.com']
    ],
    [
        'table' => 'users',
        'id' => 133,
        'identifier' => 'qa_admin@gmail.com',
        'status' => 'active_in_db',
        'details' => ['username' => 'qa_test_admin', 'name' => 'QA_Test_Admin', 'email' => 'qa_admin@gmail.com', 'role_id' => 1]
    ],
    [
        'table' => 'students',
        'id' => 72,
        'identifier' => 'qa_student_01@gmail.com',
        'status' => 'deleted_during_test',
        'details' => ['name' => 'QA_Test Student_01', 'std_id' => 6, 'registration_no' => 'REG-2026-ONE-006', 'class' => 'ONE', 'section' => 'A']
    ],
    [
        'table' => 'users',
        'id' => 134,
        'identifier' => 'qa_student_01@gmail.com',
        'status' => 'deleted_during_test',
        'details' => ['username' => 'QA_Test Student_01', 'email' => 'qa_student_01@gmail.com', 'role_id' => 4]
    ],
    [
        'table' => 'fee_collections',
        'id' => 611,
        'identifier' => 'V-S72-1026',
        'status' => 'deleted_during_test',
        'details' => ['voucher_no' => 'V-S72-1026', 'student_id' => 72, 'gross_amount' => 8500, 'net_amount' => 8500]
    ]
];

file_put_contents($logPath, json_encode($log, JSON_PRETTY_PRINT));
echo "QA_RECORDS_LOG_FINALIZED\n";
