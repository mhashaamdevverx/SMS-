<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TestSessionController extends Controller
{
    private function getStudentUser(): array
    {
        $user = User::with('studentRecord')->findOrFail(session('user_id'));
        abort_unless($user->role?->slug === 'student' && $user->studentRecord, 403);
        return [$user, $user->studentRecord];
    }

    public function index(Request $request)
    {
        [$user, $student] = $this->getStudentUser();

        // Seed test sessions for the student if not yet seeded
        $existingCount = TestSession::where('student_id', $student->id)->count();
        if ($existingCount < 12) {
            $this->seedTestSessionsForStudent($student);
        }

        $testSessions = TestSession::where('student_id', $student->id)
            ->orderBy('month_key', 'asc')
            ->get()
            ->keyBy('month_key');

        if ($testSessions->isEmpty() || ($testSessions->first() && $testSessions->first()->total_max_marks < 300)) {
            $this->seedTestSessionsForStudent($student);
            $testSessions = TestSession::where('student_id', $student->id)
                ->orderBy('month_key', 'asc')
                ->get()
                ->keyBy('month_key');
        }

        $stdRegNo = $student->registration_no ?: ('REG-2026-' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')) . '-' . str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT));

        $page = 'tests';

        return view('student.test-sessions', compact('user', 'student', 'testSessions', 'stdRegNo', 'page'));
    }

    public function downloadResult(string $month)
    {
        [$user, $student] = $this->getStudentUser();

        $existingCount = TestSession::where('student_id', $student->id)->count();
        if ($existingCount < 12) {
            $this->seedTestSessionsForStudent($student);
        }

        $testSession = TestSession::where('student_id', $student->id)
            ->where('month_key', $month)
            ->firstOrFail();

        $stdRegNo = $student->registration_no ?: ('REG-2026-' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')) . '-' . str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT));

        return view('student.test-session-print', compact('user', 'student', 'testSession', 'stdRegNo'));
    }

    private function getExactStudentSubjects(Student $student): array
    {
        if (!empty(trim((string)$student->subject))) {
            $subs = collect(explode(',', (string)$student->subject))
                ->map(fn($x) => trim($x))
                ->filter()
                ->values()
                ->toArray();
            if (!empty($subs)) {
                return $subs;
            }
        }

        $dbSubs = Subject::query()
            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($student->class))])
            ->where(fn($q) => $q->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($student->section))])->orWhereNull('section_name')->orWhere('section_name',''))
            ->orderBy('subject_name')
            ->pluck('subject_name')
            ->filter()
            ->values()
            ->toArray();

        if (!empty($dbSubs)) {
            return $dbSubs;
        }

        return ['English', 'Urdu', 'Mathematics', 'General Science', 'Islamiyat'];
    }

    public function seedTestSessionsForStudent(Student $student): void
    {
        TestSession::where('student_id', $student->id)->delete();

        $subjects = $this->getExactStudentSubjects($student);

        $months = [
            '2026-01' => 'January 2026',
            '2026-02' => 'February 2026',
            '2026-03' => 'March 2026',
            '2026-04' => 'April 2026',
            '2026-05' => 'May 2026',
            '2026-06' => 'June 2026',
            '2026-07' => 'July 2026',
            '2026-08' => 'August 2026',
            '2026-09' => 'September 2026',
            '2026-10' => 'October 2026',
            '2026-11' => 'November 2026',
            '2026-12' => 'December 2026',
        ];

        $stdId = $student->std_id ?: 1;

        foreach ($months as $mKey => $mTitle) {
            $carbon = Carbon::parse($mKey . '-12');
            $currentDay = $carbon->copy()->startOfWeek(Carbon::MONDAY);
            $startMon = $currentDay->copy();

            $isCompleted = $mKey <= '2026-10';
            $schedule = [];
            $totalMax = 0;
            $totalObtained = 0;
            $lastExamDate = $startMon->copy();

            foreach ($subjects as $subIdx => $subName) {
                while ($currentDay->isSaturday() || $currentDay->isSunday()) {
                    $schedule[] = [
                        'date' => $currentDay->format('d M Y'),
                        'day' => $currentDay->format('l'),
                        'subject' => 'Weekend Off',
                        'is_off' => true,
                        'time' => '—',
                        'max_marks' => 0,
                        'obtained_marks' => null,
                        'venue' => '—',
                    ];
                    $currentDay->addDay();
                }

                $maxM = 50;
                $mNum = intval(substr($mKey, 5));
                $seed = ($stdId * 13) + ($subIdx * 17) + ($mNum * 11);
                $var = ($seed % 13) - 3;
                $obtainedM = $isCompleted ? min(50, max(34, 42 + $var)) : null;

                if ($isCompleted) {
                    $totalMax += $maxM;
                    $totalObtained += $obtainedM;
                }

                $schedule[] = [
                    'date' => $currentDay->format('d M Y'),
                    'day' => $currentDay->format('l'),
                    'subject' => $subName,
                    'is_off' => false,
                    'time' => '09:00 AM - 11:30 AM',
                    'max_marks' => $maxM,
                    'obtained_marks' => $obtainedM,
                    'venue' => 'Exam Hall ' . (($subIdx % 3) + 1),
                ];

                $lastExamDate = $currentDay->copy();
                $currentDay->addDay();
            }

            if ($lastExamDate->isFriday()) {
                $sat = $lastExamDate->copy()->addDay();
                $sun = $lastExamDate->copy()->addDays(2);
                $schedule[] = [
                    'date' => $sat->format('d M Y'),
                    'day' => $sat->format('l'),
                    'subject' => 'Weekend Off',
                    'is_off' => true,
                    'time' => '—',
                    'max_marks' => 0,
                    'obtained_marks' => null,
                    'venue' => '—',
                ];
                $schedule[] = [
                    'date' => $sun->format('d M Y'),
                    'day' => $sun->format('l'),
                    'subject' => 'Weekend Off',
                    'is_off' => true,
                    'time' => '—',
                    'max_marks' => 0,
                    'obtained_marks' => null,
                    'venue' => '—',
                ];
            }

            $pct = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0;
            $grade = $pct >= 85 ? 'A+' : ($pct >= 75 ? 'A' : ($pct >= 65 ? 'B' : 'C'));

            TestSession::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'month_key' => $mKey,
                ],
                [
                    'std_id' => (string)$student->std_id,
                    'month_title' => $mTitle,
                    'title' => 'Monthly Test Series • ' . $mTitle,
                    'start_date' => $startMon->format('Y-m-d'),
                    'end_date' => $lastExamDate->format('Y-m-d'),
                    'status' => $isCompleted ? 'Evaluated' : 'Upcoming',
                    'total_max_marks' => $totalMax,
                    'total_obtained_marks' => $totalObtained,
                    'percentage' => $pct,
                    'grade' => $grade,
                    'schedule_json' => $schedule,
                    'user_id' => session('user_id') ?: 1,
                ]
            );
        }
    }
}
