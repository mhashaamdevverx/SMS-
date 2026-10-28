<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$action = $argv[1] ?? 'status';

switch ($action) {
    case 'setup':
        // 1. Create QA Class
        DB::table('classes')->where('class_name', 'QA_Class_Exam')->delete();
        $classId = DB::table('classes')->insertGetId([
            'class_no' => 998,
            'class_name' => 'QA_Class_Exam',
            'chairs' => 30,
            'section_name' => 'A',
            'user_id' => 133,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create QA Subject
        DB::table('subjects')->where('subject_name', 'QA_Subject_Physics')->delete();
        $subjectId = DB::table('subjects')->insertGetId([
            'class_id' => $classId,
            'subject_name' => 'QA_Subject_Physics',
            'class_name' => 'QA_Class_Exam',
            'section_name' => 'A',
            'total_marks' => 100,
            'obtained_marks' => 0,
            'user_id' => 133,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create QA Student
        DB::table('students')->where('email', 'qa_exam_student01@gmail.com')->delete();
        $studentId = DB::table('students')->insertGetId([
            'user_id' => 133,
            'std_id' => 9901,
            'registration_no' => 'REG-2026-QAEXAM-001',
            'name' => 'QA_Test Student_Exam01',
            'father_name' => 'QA_Guardian_Exam',
            'email' => 'qa_exam_student01@gmail.com',
            'phone_no' => '03009988111',
            'class' => 'QA_Class_Exam',
            'section' => 'A',
            'subject' => 'QA_Subject_Physics',
            'total_marks' => 100,
            'obtained_marks' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo json_encode([
            'success' => true,
            'class_id' => $classId,
            'subject_id' => $subjectId,
            'student_id' => $studentId,
            'std_id' => 9901
        ]);
        break;

    case 'get_arrangement':
        $arr = DB::table('arrangements')->where('roll_no', '9901')->first();
        echo json_encode(['found' => (bool)$arr, 'arrangement' => $arr]);
        break;

    case 'get_roll_slip':
        $slip = DB::table('roll_slips')->where('roll_no', 9901)->first();
        $exam = $slip ? DB::table('exams')->where('id', $slip->exam_id)->first() : null;
        $schedules = $slip ? DB::table('exam_schedules')->where('exam_id', $slip->exam_id)->get() : [];
        echo json_encode([
            'found' => (bool)$slip,
            'roll_slip' => $slip,
            'exam' => $exam,
            'schedules' => $schedules
        ]);
        break;

    case 'get_result':
        $term = $argv[2] ?? null;
        $q = DB::table('results')->where('student_name', 'QA_Test Student_Exam01');
        if ($term) {
            $q->where('term', $term);
        }
        $res = $q->latest('id')->first();
        echo json_encode(['found' => (bool)$res, 'result' => $res]);
        break;

    case 'cleanup':
        DB::table('results')->where('student_name', 'LIKE', 'QA_%')->delete();
        DB::table('roll_slips')->where('student_name', 'LIKE', 'QA_%')->orWhere('class_name', 'QA_Class_Exam')->delete();
        $examIds = DB::table('exams')->where('name', 'LIKE', 'QA_%')->pluck('id')->toArray();
        if (!empty($examIds)) {
            DB::table('exam_schedules')->whereIn('exam_id', $examIds)->delete();
            DB::table('exams')->whereIn('id', $examIds)->delete();
        }
        DB::table('arrangements')->where('room', 'LIKE', 'QA_%')->orWhere('roll_no', '9901')->orWhere('room', '998')->delete();
        
        $qaStudent = DB::table('students')->where('email', 'qa_exam_student01@gmail.com')->first();
        if ($qaStudent) {
            DB::table('fee_collections')->where('student_id', $qaStudent->id)->delete();
            DB::table('students')->where('id', $qaStudent->id)->delete();
        }
        DB::table('users')->where('email', 'qa_exam_student01@gmail.com')->delete();
        DB::table('subjects')->where('subject_name', 'QA_Subject_Physics')->delete();
        DB::table('classes')->where('class_name', 'QA_Class_Exam')->delete();
        echo json_encode(['cleaned' => true]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}