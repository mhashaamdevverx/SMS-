<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Support\InchargeAccess;
use App\Models\AppNotification;

class AttendanceController extends Controller
{
    private function ensureAuthenticated(): void
    {
        abort_unless(session()->has('user_id'), 403);
    }

    private function classOptions()
    {
        $assignment = InchargeAccess::assignment();
        if ($assignment) {
            return collect([$assignment->classRecord->class_name]);
        }

        $presetOrder = [
            'pre-kg' => 1, 'kg' => 2, 'one' => 3, 'two' => 4, 'three' => 5,
            'four' => 6, 'five' => 7, 'six' => 8, 'seven' => 9, 'eight' => 10,
            'nine' => 11, 'ten' => 12,
        ];

        return Classes::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->pluck('class_name')
            ->merge(['Pre-KG', 'KG', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN'])
            ->merge(Student::query()->whereNotNull('class')->where('class', '!=', '')->pluck('class'))
            ->filter()
            ->map(fn ($c) => trim((string) $c))
            ->unique(fn ($c) => strtolower($c))
            ->sortBy(function ($className) use ($presetOrder) {
                return $presetOrder[strtolower($className)] ?? 999;
            })
            ->values();
    }

    private function sectionOptions()
    {
        $assignment = InchargeAccess::assignment();
        if ($assignment) {
            return collect([$assignment->classRecord->section_name]);
        }

        $dbSections = Section::query()
            ->whereNotNull('section_name')
            ->where('section_name', '!=', '')
            ->orderBy('section_name')
            ->pluck('section_name')
            ->map(fn ($sectionName) => trim((string) $sectionName))
            ->filter()
            ->unique(fn ($sectionName) => strtolower($sectionName))
            ->values();

        return collect(['All Sections'])->merge($dbSections)->values();
    }

    public function index(Request $request)
    {
        $this->ensureAuthenticated();

        $mode = $request->input('mode', 'daily'); // 'daily', 'monthly', 'yearly'
        $classes = $this->classOptions();
        $sections = $this->sectionOptions();
        $assignment = InchargeAccess::assignment();

        $selectedClass = trim((string) $request->input('class', $assignment?->classRecord->class_name ?? $classes->first() ?? 'Pre-KG'));
        $selectedSection = trim((string) $request->input('section', $assignment?->classRecord->section_name ?? 'All Sections'));
        InchargeAccess::assertClassSection($selectedClass, $selectedSection);
        $selectedDate = $request->input('date', now()->toDateString());
        $selectedMonth = $request->input('month', now()->format('Y-m')); // e.g. "2026-09"
        $selectedYear = (int) $request->input('year', now()->year); // e.g. 2026

        $currentUser = User::find(session('user_id'));

        // Query students of this class and section
        $studentsQuery = Student::query()
            ->when($selectedClass !== '', function ($q) use ($selectedClass) {
                $q->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($selectedClass))]);
            })
            ->when($selectedSection !== '' && strtolower($selectedSection) !== 'all sections', function ($q) use ($selectedSection) {
                $q->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($selectedSection))]);
            })
            ->orderBy('std_id')
            ->orderBy('name');
        InchargeAccess::constrainStudents($studentsQuery);

        $students = $studentsQuery->paginate(10)->withQueryString();

        // Fallback: If chosen section has 0 students, fallback to all students of the class
        if (!$assignment && $students->isEmpty() && strtolower($selectedSection) !== 'all sections') {
            $students = Student::query()
                ->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($selectedClass))])
                ->orderBy('std_id')
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();
        }

        // 1. DAILY ATTENDANCE DATA
        $dailyRecords = [];
        $dailySummary = ['total' => $students->count(), 'present' => 0, 'absent' => 0, 'leave' => 0, 'unmarked' => 0];

        if ($mode === 'daily') {
            $studentIds = $students->pluck('id');
            $existing = Attendance::query()
                ->whereIn('student_id', $studentIds)
                ->where('date', $selectedDate)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $record = $existing->get($student->id);
                $status = $record ? $record->status : null;
                $dailyRecords[$student->id] = [
                    'record_id' => $record?->id,
                    'status' => $status,
                    'remarks' => $record?->remarks,
                ];

                if ($status === 'present') {
                    $dailySummary['present']++;
                } elseif ($status === 'absent') {
                    $dailySummary['absent']++;
                } elseif ($status === 'leave') {
                    $dailySummary['leave']++;
                } else {
                    $dailySummary['unmarked']++;
                }
            }
        }

        // 2. MONTHLY ATTENDANCE REGISTER DATA
        $monthlyDays = [];
        $monthlyStudentsData = [];
        $monthlyClassStats = ['total_marked' => 0, 'present' => 0, 'absent' => 0, 'leave' => 0, 'percentage' => 0];

        if ($mode === 'monthly') {
            try {
                $carbonMonth = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            } catch (\Exception $e) {
                $carbonMonth = now()->startOfMonth();
                $selectedMonth = $carbonMonth->format('Y-m');
            }

            $daysInMonth = $carbonMonth->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dayDate = $carbonMonth->copy()->day($d);
                $monthlyDays[] = [
                    'day' => $d,
                    'date' => $dayDate->toDateString(),
                    'day_name' => $dayDate->format('D'),
                    'is_weekend' => $dayDate->isSunday(),
                ];
            }

            $monthStart = $carbonMonth->toDateString();
            $monthEnd = $carbonMonth->copy()->endOfMonth()->toDateString();

            $studentIds = $students->pluck('id');
            $attendancesInMonth = Attendance::query()
                ->whereIn('student_id', $studentIds)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $stdRecords = ($attendancesInMonth->get($student->id) ?? collect())->keyBy(function ($item) {
                    return Carbon::parse($item->date)->day;
                });

                $presentCount = 0;
                $absentCount = 0;
                $leaveCount = 0;
                $daysMap = [];

                foreach ($monthlyDays as $dayInfo) {
                    $dayNum = $dayInfo['day'];
                    $att = $stdRecords->get($dayNum);
                    $status = $att ? $att->status : null;
                    $daysMap[$dayNum] = [
                        'status' => $status,
                        'id' => $att?->id,
                        'remarks' => $att?->remarks,
                    ];

                    if ($status === 'present') {
                        $presentCount++;
                    } elseif ($status === 'absent') {
                        $absentCount++;
                    } elseif ($status === 'leave') {
                        $leaveCount++;
                    }
                }

                $totalMarked = $presentCount + $absentCount + $leaveCount;
                $pct = $totalMarked > 0 ? round(($presentCount / $totalMarked) * 100, 1) : 0;

                $monthlyStudentsData[] = [
                    'student' => $student,
                    'days' => $daysMap,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'leave' => $leaveCount,
                    'total_marked' => $totalMarked,
                    'percentage' => $pct,
                ];

                $monthlyClassStats['present'] += $presentCount;
                $monthlyClassStats['absent'] += $absentCount;
                $monthlyClassStats['leave'] += $leaveCount;
                $monthlyClassStats['total_marked'] += $totalMarked;
            }

            if ($monthlyClassStats['total_marked'] > 0) {
                $monthlyClassStats['percentage'] = round(($monthlyClassStats['present'] / $monthlyClassStats['total_marked']) * 100, 1);
            }
        }

        // 3. YEARLY ATTENDANCE SUMMARY DATA
        $yearlyMonths = [];
        $yearlyStudentsData = [];
        $yearlyClassStats = ['total_marked' => 0, 'present' => 0, 'absent' => 0, 'leave' => 0, 'percentage' => 0];

        if ($mode === 'yearly') {
            for ($m = 1; $m <= 12; $m++) {
                $carbonM = Carbon::create($selectedYear, $m, 1);
                $yearlyMonths[$m] = [
                    'month_num' => $m,
                    'name' => $carbonM->format('M'),
                    'full_name' => $carbonM->format('F'),
                ];
            }

            $yearStart = Carbon::create($selectedYear, 1, 1)->toDateString();
            $yearEnd = Carbon::create($selectedYear, 12, 31)->toDateString();

            $studentIds = $students->pluck('id');
            $yearAttendances = Attendance::query()
                ->whereIn('student_id', $studentIds)
                ->whereBetween('date', [$yearStart, $yearEnd])
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $stdRecords = ($yearAttendances->get($student->id) ?? collect());
                $monthBreakdown = [];
                $studentYearPresent = 0;
                $studentYearAbsent = 0;
                $studentYearLeave = 0;

                for ($m = 1; $m <= 12; $m++) {
                    $mRecords = $stdRecords->filter(function ($item) use ($m) {
                        return Carbon::parse($item->date)->month === $m;
                    });

                    $mPresent = $mRecords->where('status', 'present')->count();
                    $mAbsent = $mRecords->where('status', 'absent')->count();
                    $mLeave = $mRecords->where('status', 'leave')->count();
                    $mTotal = $mPresent + $mAbsent + $mLeave;
                    $mPct = $mTotal > 0 ? round(($mPresent / $mTotal) * 100, 1) : null;

                    $monthBreakdown[$m] = [
                        'present' => $mPresent,
                        'absent' => $mAbsent,
                        'leave' => $mLeave,
                        'total' => $mTotal,
                        'percentage' => $mPct,
                    ];

                    $studentYearPresent += $mPresent;
                    $studentYearAbsent += $mAbsent;
                    $studentYearLeave += $mLeave;
                }

                $studentYearTotal = $studentYearPresent + $studentYearAbsent + $studentYearLeave;
                $studentYearPct = $studentYearTotal > 0 ? round(($studentYearPresent / $studentYearTotal) * 100, 1) : 0;

                $yearlyStudentsData[] = [
                    'student' => $student,
                    'months' => $monthBreakdown,
                    'present' => $studentYearPresent,
                    'absent' => $studentYearAbsent,
                    'leave' => $studentYearLeave,
                    'total_marked' => $studentYearTotal,
                    'percentage' => $studentYearPct,
                ];

                $yearlyClassStats['present'] += $studentYearPresent;
                $yearlyClassStats['absent'] += $studentYearAbsent;
                $yearlyClassStats['leave'] += $studentYearLeave;
                $yearlyClassStats['total_marked'] += $studentYearTotal;
            }

            if ($yearlyClassStats['total_marked'] > 0) {
                $yearlyClassStats['percentage'] = round(($yearlyClassStats['present'] / $yearlyClassStats['total_marked']) * 100, 1);
            }
        }

        return view('attendance', compact(
            'mode',
            'classes',
            'sections',
            'selectedClass',
            'selectedSection',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'students',
            'dailyRecords',
            'dailySummary',
            'monthlyDays',
            'monthlyStudentsData',
            'monthlyClassStats',
            'yearlyMonths',
            'yearlyStudentsData',
            'yearlyClassStats',
            'currentUser'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'class' => ['required', 'string'],
            'section' => ['required', 'string'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,leave'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $date = $validated['date'];
        $className = $validated['class'];
        $sectionName = $validated['section'];
        $attendanceData = $validated['attendance'];
        $userId = session('user_id');
        InchargeAccess::assertClassSection($className, $sectionName);

        if (InchargeAccess::isIncharge()) {
            abort_unless($date === now()->toDateString(), 403, 'Class incharges can only record attendance for today.');

            $submittedIds = array_unique(array_map('intval', array_keys($attendanceData)));
            $eligibleCount = InchargeAccess::constrainStudents(Student::query()->whereIn('id', $submittedIds))->count();
            abort_unless($eligibleCount === count($submittedIds), 403, 'Attendance can only be recorded for your assigned students.');
            $existingIds = Attendance::query()
                ->whereIn('student_id', $submittedIds)
                ->whereDate('date', $date)
                ->pluck('student_id')
                ->map(fn ($studentId) => (int) $studentId)
                ->all();
            $attendanceData = array_diff_key($attendanceData, array_flip($existingIds));
            abort_unless($attendanceData !== [], 403, 'Attendance has already been recorded for these students.');
        }

        $isIncharge = InchargeAccess::isIncharge();
        DB::transaction(function () use ($attendanceData, $date, $className, $sectionName, $userId, $isIncharge) {
            foreach ($attendanceData as $studentId => $item) {
                $attributes = [
                    'status' => $item['status'],
                    'class_name' => $className,
                    'section_name' => $sectionName,
                    'remarks' => $item['remarks'] ?? null,
                    'user_id' => $userId,
                ];

                if ($isIncharge) {
                    Attendance::create($attributes + [
                        'student_id' => $studentId,
                        'date' => $date,
                    ]);
                } else {
                    Attendance::updateOrCreate(['student_id' => $studentId, 'date' => $date], $attributes);
                }
            }
        });

        // Requirement 10: Send notification to affected students, In-Charge Teacher, and Admin
        AppNotification::notifyAttendanceSubmitted($className, $sectionName, $date, $attendanceData, User::find($userId));

        return redirect()->route('attendance.index', [
            'mode' => 'daily',
            'class' => $className,
            'section' => $sectionName,
            'date' => $date,
        ])->with('success', 'Attendance for '.Carbon::parse($date)->format('d M, Y').' saved successfully for '.count($attendanceData).' students.');
    }

    public function destroy(Attendance $attendance)
    {
        $this->ensureAuthenticated();

        $date = $attendance->date ? $attendance->date->toDateString() : now()->toDateString();
        $class = $attendance->class_name;
        $section = $attendance->section_name;

        $attendance->delete();

        return redirect()->route('attendance.index', [
            'mode' => 'daily',
            'class' => $class,
            'section' => $section,
            'date' => $date,
        ])->with('success', 'Attendance record removed successfully.');
    }

    public function destroyDate(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'class' => ['required', 'string'],
            'section' => ['required', 'string'],
        ]);

        $deletedCount = Attendance::query()
            ->where('date', $validated['date'])
            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($validated['class']))])
            ->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($validated['section']))])
            ->delete();

        return redirect()->route('attendance.index', [
            'mode' => 'daily',
            'class' => $validated['class'],
            'section' => $validated['section'],
            'date' => $validated['date'],
        ])->with('success', $deletedCount.' attendance records deleted for '.Carbon::parse($validated['date'])->format('d M, Y').'.');
    }
}
