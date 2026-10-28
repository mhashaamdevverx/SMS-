<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$subDeleted = DB::table('subjects')->where('subject_name', 'LIKE', 'QA_%')->delete();
$secDeleted = DB::table('sections')->where('section_name', 'LIKE', 'QA-%')->delete();
$clsDeleted = DB::table('classes')->where('class_name', 'LIKE', 'QA_%')->delete();
$crsDeleted = DB::table('courses')->where('course_code', 'LIKE', 'QA%')->delete();

echo "CLEANUP COMPLETE: Subjects: $subDeleted, Sections: $secDeleted, Classes: $clsDeleted, Courses: $crsDeleted\n";
