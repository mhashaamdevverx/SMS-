<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "FOLDER: " . base_path() . "\n";
echo "DATABASE: " . config('database.connections.mysql.database') . "\n";
echo "--------------------------------------------------\n";
echo "AUDIT OF ALL QA_ RECORDS IN DATABASE:\n";

$tables = [
    'users' => ['name', 'email', 'username'],
    'admission_applications' => ['student_name', 'guardian_name', 'guardian_email'],
    'career_applications' => ['full_name', 'email'],
    'teacher_salaries' => ['month', 'status'],
    'student_leaves' => ['student_name', 'reason'],
    'teacher_leaves' => ['teacher_name', 'reason'],
    'teacher_attendances' => ['remarks'],
    'class_incharges' => ['id'],
    'teacher_access_requests' => ['name', 'email'],
    'students' => ['name', 'email'],
    'teachers' => ['teacher_name', 'teacher_email'],
    'classes' => ['class_name'],
    'sections' => ['section_name'],
    'subjects' => ['subject_name'],
    'courses' => ['course_name'],
    'fee_collections' => ['voucher_no', 'month'],
];

$found = [];

foreach ($tables as $table => $columns) {
    if (!Schema::hasTable($table)) {
        continue;
    }
    $query = DB::table($table);
    $query->where(function($q) use ($columns, $table) {
        foreach ($columns as $col) {
            if (Schema::hasColumn($table, $col)) {
                $q->orWhere($col, 'LIKE', 'QA_%')
                  ->orWhere($col, 'LIKE', 'qa_%')
                  ->orWhere($col, 'LIKE', 'QA %')
                  ->orWhere($col, 'LIKE', 'qa %');
            }
        }
    });
    $rows = $query->get();
    if ($rows->count() > 0) {
        $found[$table] = $rows->toArray();
        echo "Table [{$table}]: found " . $rows->count() . " records\n";
        foreach ($rows as $r) {
            echo "  - " . json_encode($r) . "\n";
        }
    } else {
        echo "Table [{$table}]: 0 QA_ records\n";
    }
}

echo "--------------------------------------------------\n";
echo "AUDIT OF ACTIVE CLASS IN-CHARGE STATUS (REAL TEACHERS):\n";
$incharges = DB::table('class_incharges')
    ->join('teachers', 'class_incharges.teacher_id', '=', 'teachers.id')
    ->join('classes', 'class_incharges.class_id', '=', 'classes.id')
    ->select('class_incharges.id', 'teachers.teacher_name', 'classes.class_name', 'classes.section_name')
    ->get();

echo "Total Class Incharges in DB: " . $incharges->count() . "\n";
foreach ($incharges as $inc) {
    echo "  - Incharge #{$inc->id}: Teacher [{$inc->teacher_name}] -> Class [{$inc->class_name} {$inc->section_name}]\n";
}
echo "Audit complete.\n";