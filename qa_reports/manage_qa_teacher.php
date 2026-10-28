<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$action = $argv[1] ?? 'status';
$arg1 = $argv[2] ?? null;

switch ($action) {
    case 'create_test_class':
        DB::table('classes')->where('class_name', 'QA_Incharge_Class')->delete();
        $classId = DB::table('classes')->insertGetId([
            'class_no' => 999,
            'class_name' => 'QA_Incharge_Class',
            'chairs' => 30,
            'section_name' => 'A',
            'user_id' => 133
        ]);
        echo json_encode(['success' => true, 'class_id' => $classId]);
        break;

    case 'delete_test_class':
        DB::table('class_incharges')->where('class_id', $arg1)->delete();
        DB::table('classes')->where('id', $arg1)->delete();
        echo json_encode(['success' => true]);
        break;

    case 'get_incharge':
        $teacherId = (int)$arg1;
        $incharge = DB::table('class_incharges')->where('teacher_id', $teacherId)->first();
        echo json_encode(['found' => (bool)$incharge, 'incharge' => $incharge]);
        break;

    case 'create_access_request':
        $teacherId = (int)$arg1;
        DB::table('teacher_access_requests')->where('email', 'qa_access_req_01@gmail.com')->delete();
        $id = DB::table('teacher_access_requests')->insertGetId([
            'teacher_id' => $teacherId,
            'name' => 'QA Access Teacher',
            'email' => 'qa_access_req_01@gmail.com',
            'phone' => '03009988776',
            'password' => 'dummy',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo json_encode(['success' => true, 'request_id' => $id]);
        break;

    case 'check_access_request':
        $reqId = (int)$arg1;
        $req = DB::table('teacher_access_requests')->where('id', $reqId)->first();
        echo json_encode(['found' => (bool)$req, 'status' => $req?->status, 'request' => $req]);
        break;

    case 'clean_access_request':
        $reqId = (int)$arg1;
        DB::table('teacher_access_requests')->where('id', $reqId)->delete();
        echo json_encode(['success' => true]);
        break;

    case 'link_attendance_and_leave':
        $teacherId = (int)$arg1;
        DB::table('teacher_attendances')->where('teacher_id', $teacherId)->delete();
        DB::table('teacher_leaves')->where('teacher_id', $teacherId)->delete();
        
        $attId = DB::table('teacher_attendances')->insertGetId([
            'teacher_id' => $teacherId,
            'date' => '2026-10-27',
            'status' => 'present',
            'remarks' => 'QA_Teacher_Attendance'
        ]);
        
        $leaveId = DB::table('teacher_leaves')->insertGetId([
            'teacher_id' => $teacherId,
            'teacher_name' => 'QA Test Teacher One',
            'leave_date' => '2026-10-28',
            'leave_type' => 'Casual',
            'reason' => 'QA_Teacher_Leave_Reason',
            'status' => 'Pending'
        ]);
        
        echo json_encode(['success' => true, 'attendance_id' => $attId, 'leave_id' => $leaveId]);
        break;

    case 'check_cascade':
        $teacherId = (int)$arg1;
        $teacher = DB::table('teachers')->where('id', $teacherId)->first();
        $user = DB::table('users')->where('email', 'qa_teacher_01@gmail.com')->first();
        $salaries = DB::table('teacher_salaries')->where('teacher_id', $teacherId)->get();
        $assignments = DB::table('teacher_assignments')->where('teacher_id', $teacherId)->get();
        $attendance = DB::table('teacher_attendances')->where('teacher_id', $teacherId)->get();
        $leaves = DB::table('teacher_leaves')->where('teacher_id', $teacherId)->get();
        $incharges = DB::table('class_incharges')->where('teacher_id', $teacherId)->get();

        echo json_encode([
            'teacher_deleted' => ($teacher === null),
            'user_deleted' => ($user === null),
            'salaries_count' => count($salaries),
            'assignments_count' => count($assignments),
            'attendance_orphaned_count' => count($attendance),
            'leaves_orphaned_count' => count($leaves),
            'incharges_orphaned_count' => count($incharges)
        ], JSON_PRETTY_PRINT);
        break;

    case 'clean_qa_teacher':
        $teacher = DB::table('teachers')->where('teacher_email', 'qa_teacher_01@gmail.com')->first();
        if ($teacher) {
            DB::table('teacher_salaries')->where('teacher_id', $teacher->id)->delete();
            DB::table('teacher_assignments')->where('teacher_id', $teacher->id)->delete();
            DB::table('teacher_attendances')->where('teacher_id', $teacher->id)->delete();
            DB::table('teacher_leaves')->where('teacher_id', $teacher->id)->delete();
            DB::table('class_incharges')->where('teacher_id', $teacher->id)->delete();
            DB::table('users')->where('id', $teacher->login_user_id)->delete();
            DB::table('teachers')->where('id', $teacher->id)->delete();
        }
        DB::table('users')->where('email', 'qa_teacher_01@gmail.com')->delete();
        DB::table('classes')->where('class_name', 'QA_Incharge_Class')->delete();
        DB::table('teacher_access_requests')->where('email', 'qa_access_req_01@gmail.com')->delete();
        echo json_encode(['cleaned' => true]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}