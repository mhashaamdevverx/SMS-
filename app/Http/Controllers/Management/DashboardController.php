<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Arrangement;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Course;
use App\Models\FeeCollection;
use App\Models\Result;
use App\Models\RollSlip;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Support\InchargeAccess;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $currentUser = User::with('role')->findOrFail(session('user_id'));
        if ($currentUser->role?->slug === 'student') {
            return redirect()->route('student.dashboard');
        }
        if (in_array($currentUser->role?->slug, ['teacher', 'class-incharge'], true)) {
            return redirect()->route('teacher.dashboard');
        }
        if ($currentUser->isAdmin() || in_array($currentUser->role?->slug, ['admin', 'super-admin', 'head'], true)) {
            return redirect()->route('admin.dashboard');
        }
        $assignment = InchargeAccess::assignment();
        $ownStudent = InchargeAccess::studentRecord();
        $canViewTeachers = true;
        $canViewFees = true;
        $canViewClasses = true;
        $canViewStudents = true;
        $canViewResults = true;
        $canViewAttendance = true;

        $studentQuery = InchargeAccess::constrainStudents(Student::query());
        $totalStudents = $canViewStudents ? $studentQuery->count() : 0;
        $totalTeachers = $canViewTeachers ? Teacher::query()->count() : 0;
        $resultsQuery = Result::query();
        if ($assignment) {
            $resultsQuery->whereRaw('LOWER(TRIM(student_class)) = ?', [mb_strtolower(trim($assignment->classRecord->class_name))])
                ->whereRaw('LOWER(TRIM(student_section)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))]);
        } elseif ($ownStudent) {
            $resultsQuery->whereRaw('LOWER(TRIM(student_name)) = ?', [mb_strtolower(trim($ownStudent->name))])
                ->whereRaw('LOWER(TRIM(student_class)) = ?', [mb_strtolower(trim($ownStudent->class))])
                ->whereRaw('LOWER(TRIM(student_section)) = ?', [mb_strtolower(trim($ownStudent->section))]);
        }
        $totalResults = $canViewResults ? $resultsQuery->count() : 0;

        $todayAttendanceQuery = Attendance::query()->whereDate('date', $today);
        if ($assignment) {
            $todayAttendanceQuery->whereRaw('LOWER(TRIM(class_name)) = ?', [mb_strtolower(trim($assignment->classRecord->class_name))])
                ->whereRaw('LOWER(TRIM(section_name)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))]);
        } elseif ($ownStudent) {
            $todayAttendanceQuery->where('student_id', $ownStudent->id);
        }
        $todayAttendance = $canViewAttendance
            ? $todayAttendanceQuery->selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total', 'status')
            : collect();

        $attendanceMarked = (int) $todayAttendance->sum();
        $presentToday = (int) $todayAttendance->get('present', 0);
        $attendanceRate = $attendanceMarked > 0
            ? (int) round(($presentToday / $attendanceMarked) * 100)
            : 0;

        $weekStart = now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6);
        $weeklyRecordsQuery = Attendance::query()
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ;
        if ($assignment) {
            $weeklyRecordsQuery->whereRaw('LOWER(TRIM(class_name)) = ?', [mb_strtolower(trim($assignment->classRecord->class_name))])
                ->whereRaw('LOWER(TRIM(section_name)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))]);
        } elseif ($ownStudent) {
            $weeklyRecordsQuery->where('student_id', $ownStudent->id);
        }
        $weeklyRecords = $canViewAttendance
            ? $weeklyRecordsQuery->selectRaw('date, status, COUNT(*) AS total')->groupBy('date', 'status')->get()
            : collect();
        $weeklyRecordsByDate = $weeklyRecords->groupBy(fn ($record) => $record->date->toDateString());

        $attendanceLabels = [];
        $weeklyPresent = [];
        $weeklyAbsent = [];
        for ($day = 0; $day < 7; $day++) {
            $date = $weekStart->copy()->addDays($day);
            $attendanceLabels[] = $date->format('D');
            $dayRecords = $weeklyRecordsByDate->get($date->toDateString(), collect());
            $weeklyPresent[] = (int) ($dayRecords->firstWhere('status', 'present')->total ?? 0);
            $weeklyAbsent[] = (int) ($dayRecords->firstWhere('status', 'absent')->total ?? 0);
        }

        $classDistributionQuery = Student::query();
        InchargeAccess::constrainStudents($classDistributionQuery);
        $classDistribution = $canViewStudents ? $classDistributionQuery
            ->selectRaw("COALESCE(NULLIF(TRIM(class), ''), 'Unassigned') AS class_label, COUNT(*) AS total")
            ->groupBy('class_label')
            ->orderByDesc('total')
            ->limit(6)
            ->get() : collect();

        $feeSummary = $canViewFees ? FeeCollection::query()
            ->selectRaw('status, COUNT(*) AS total, COALESCE(SUM(net_amount), 0) AS amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status') : collect();
        $rollSlipCount = RollSlip::query()
            ->when($ownStudent, fn ($query) => $query->where('student_id', $ownStudent->id))
            ->count();
        $feeStatusCounts = [
            (int) ($feeSummary->get('Paid')->total ?? 0),
            (int) ($feeSummary->get('Pending')->total ?? 0),
            (int) ($feeSummary->get('Overdue')->total ?? 0),
        ];
        $collectedFees = (float) ($feeSummary->get('Paid')->amount ?? 0);
        $outstandingFees = (float) ($feeSummary->get('Pending')->amount ?? 0)
            + (float) ($feeSummary->get('Overdue')->amount ?? 0);

        $allModuleCards = [
            ['title' => 'Students', 'detail' => number_format($totalStudents).' enrolled', 'href' => '/list', 'icon' => 'bi-mortarboard-fill', 'tone' => 'green', 'permission' => 'students.view'],
            ['title' => 'Teachers', 'detail' => number_format($totalTeachers).' on the team', 'href' => '/teachers', 'icon' => 'bi-person-badge-fill', 'tone' => 'blue', 'permission' => 'teachers.view'],
            ['title' => 'Courses', 'detail' => Course::query()->count().' courses', 'href' => '/courses', 'icon' => 'bi-journal-bookmark-fill', 'tone' => 'coral', 'permission' => 'courses.view'],
            ['title' => 'Classes', 'detail' => Classes::query()->count().' class groups', 'href' => '/classes', 'icon' => 'bi-easel2-fill', 'tone' => 'gold', 'permission' => 'classes.view'],
            ['title' => 'Sections', 'detail' => Section::query()->count().' sections', 'href' => '/sections', 'icon' => 'bi-grid-3x3-gap-fill', 'tone' => 'blue', 'permission' => 'sections.view'],
            ['title' => 'Subjects', 'detail' => Subject::query()->count().' subjects', 'href' => '/subjects', 'icon' => 'bi-book-half', 'tone' => 'green', 'permission' => 'subjects.view'],
            ['title' => 'Attendance', 'detail' => $attendanceMarked > 0 ? $attendanceRate.'% present today' : 'Today is not marked', 'href' => '/attendance', 'icon' => 'bi-calendar2-check-fill', 'tone' => 'coral', 'permission' => 'attendance.view'],
            ['title' => 'Results', 'detail' => number_format($totalResults).' records', 'href' => '/results', 'icon' => 'bi-clipboard2-data-fill', 'tone' => 'gold', 'permission' => 'results.view'],
            ['title' => 'Fee management', 'detail' => 'Rs '.number_format($outstandingFees, 0).' outstanding', 'href' => '/fee-structure', 'icon' => 'bi-cash-coin', 'tone' => 'green', 'permission' => 'fees.view'],
            ['title' => 'Roll number slips', 'detail' => number_format($rollSlipCount).' issued', 'href' => '/roll-slips', 'icon' => 'bi-file-earmark-text-fill', 'tone' => 'blue', 'permission' => 'roll-slips.view'],
            ['title' => 'Seating plans', 'detail' => number_format(Arrangement::query()->count()).' arrangements', 'href' => '/arrangements', 'icon' => 'bi-columns-gap', 'tone' => 'coral', 'permission' => 'arrangements.view'],
            ['title' => 'User accounts', 'detail' => number_format(User::query()->count()).' accounts', 'href' => '/users', 'icon' => 'bi-people-fill', 'tone' => 'gold', 'permission' => 'users.view'],
        ];
        $moduleCards = array_values(array_filter($allModuleCards, fn ($card) => $currentUser->hasPermission($card['permission'])));

        $greeting = match (true) {
            now()->hour < 12 => 'Good morning',
            now()->hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        return view('dashboard', [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalResults' => $totalResults,
            'isAdmin' => $currentUser->isAdmin(),
            'inchargeLabel' => $assignment ? $assignment->classRecord->class_name.' / '.$assignment->classRecord->section_name : null,
            'canViewTeachers' => $canViewTeachers,
            'canViewFees' => $canViewFees,
            'canViewClasses' => $canViewClasses,
            'canViewStudents' => $canViewStudents,
            'canViewResults' => $canViewResults,
            'canViewAttendance' => $canViewAttendance,
            'presentToday' => $presentToday,
            'attendanceMarked' => $attendanceMarked,
            'attendanceRate' => $attendanceRate,
            'collectedFees' => $collectedFees,
            'outstandingFees' => $outstandingFees,
            'attendanceLabels' => $attendanceLabels,
            'weeklyPresent' => $weeklyPresent,
            'weeklyAbsent' => $weeklyAbsent,
            'classLabels' => $classDistribution->pluck('class_label')->all(),
            'classTotals' => $classDistribution->pluck('total')->map(fn ($total) => (int) $total)->all(),
            'feeStatusCounts' => $feeStatusCounts,
            'chartData' => [
                'attendanceLabels' => $attendanceLabels,
                'weeklyPresent' => $weeklyPresent,
                'weeklyAbsent' => $weeklyAbsent,
                'classLabels' => $classDistribution->pluck('class_label')->all(),
                'classTotals' => $classDistribution->pluck('total')->map(fn ($total) => (int) $total)->all(),
                'feeStatusCounts' => $feeStatusCounts,
            ],
            'moduleCards' => $moduleCards,
            'greeting' => $greeting,
        ]);
    }
}