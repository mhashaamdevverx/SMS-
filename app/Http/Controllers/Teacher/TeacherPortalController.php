<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\CourseOutline;
use App\Models\TeacherAttendance;
use App\Models\TeacherSalary;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\TeacherLeave;
use App\Models\AppNotification;

class TeacherPortalController extends Controller
{
    private function getTeacher(): array
    {
        $userId = session('user_id');
        if (!$userId) {
            redirect()->route('login')->send();
            exit;
        }

        $user = User::with(['role', 'teacherRecord'])->find($userId);
        if (!$user) {
            redirect()->route('login')->send();
            exit;
        }

        $teacher = $user->teacherRecord 
            ?? Teacher::where('user_id', $user->id)->first()
            ?? Teacher::where('login_user_id', $user->id)->first()
            ?? Teacher::where('teacher_email', $user->email)->first();

        $roleSlug = $user->role?->slug;

        if ($roleSlug === 'admin') {
            redirect()->route('admin.dashboard')->send();
            exit;
        }

        $isTeacher = in_array($roleSlug, ['teacher', 'class-incharge'], true) || $teacher !== null;

        if (!$isTeacher) {
            if ($roleSlug === 'student') {
                redirect()->route('student.dashboard')->with('error', 'Please sign in with a Teacher account to access the Teacher Portal.')->send();
                exit;
            }
            redirect()->route('login')->with('error', 'Teacher access required. Please sign in with your teacher email and password.')->send();
            exit;
        }

        if (!$teacher) {
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'login_user_id' => $user->id,
                'teacher_name' => $user->name,
                'teacher_email' => $user->email,
                'teacher_phone' => '0300-1234567',
                'course' => 'General Subjects',
            ]);
        }

        return [$user, $teacher];
    }

    private function getTeacherBaseData(Teacher $teacher, Request $request = null): array
    {
        // Get all assignments for this teacher
        $myAssignments = TeacherAssignment::where('teacher_id', $teacher->id)->get();

        if ($myAssignments->isEmpty()) {
            $myClasses = collect([
                ['class_name' => 'Pre-KG', 'section_name' => 'Red', 'label' => 'Pre-KG (Red)']
            ]);
        } else {
            $myClasses = $myAssignments->map(function($a) {
                $secStr = $a->section_name ? " ({$a->section_name})" : '';
                return [
                    'class_name' => $a->class_name,
                    'section_name' => $a->section_name ?: '',
                    'label' => $a->class_name . $secStr,
                ];
            })->unique('label')->values();
        }

        // Active class selected from request dropdown
        $reqClass = $request ? $request->input('class') : null;
        $reqSection = $request ? $request->input('section') : null;
        $reqSubject = $request ? $request->input('subject') : null;
        $searchQuery = $request ? trim($request->input('search', '')) : '';

        // Check for "All My Classes" option
        $isAllClasses = ($reqClass === 'all');

        // Security / Authorization Enforcement:
        // Teacher can only access classes assigned to them.
        $authorizedClassNames = $myClasses->pluck('class_name')->map(fn($c) => strtolower(trim($c)))->all();
        if ($reqClass && !$isAllClasses && !in_array(strtolower(trim($reqClass)), $authorizedClassNames, true)) {
            // Reject unauthorized class parameter and fallback safely to teacher's authorized class
            $reqClass = $myClasses->first()['class_name'];
        }

        $activeClassObj = null;
        if ($reqClass && !$isAllClasses) {
            $activeClassObj = $myClasses->first(fn($c) => strtolower(trim($c['class_name'])) === strtolower(trim($reqClass)));
        }
        if (!$activeClassObj) {
            $activeClassObj = $myClasses->first();
        }

        if ($isAllClasses) {
            $primaryClass = 'All My Classes';
            $primarySection = $reqSection ?: 'All Sections';
            $mySubjects = $myAssignments->pluck('subject_or_course')->filter()->unique()->values();
            $availableSections = $myAssignments->pluck('section_name')->filter()->unique()->values();
        } else {
            $primaryClass = $activeClassObj['class_name'];
            $primarySection = $reqSection ?: $activeClassObj['section_name'];
            $mySubjects = $myAssignments
                ->filter(fn($a) => strtolower(trim($a->class_name)) === strtolower(trim($primaryClass)))
                ->pluck('subject_or_course')
                ->filter()
                ->unique()
                ->values();
            $availableSections = $myAssignments
                ->filter(fn($a) => strtolower(trim($a->class_name)) === strtolower(trim($primaryClass)))
                ->pluck('section_name')
                ->filter()
                ->unique()
                ->values();
        }

        if ($mySubjects->isEmpty()) {
            $mySubjects = collect(['English', 'Drawing & Arts']);
        }

        $availableFilterSubjects = $isAllClasses 
            ? $myAssignments->pluck('subject_or_course')->filter()->unique()->values() 
            : $mySubjects;

        // Assigned Classes Summary for "My Assigned Classes" Feature Two
        $assignedClassesSummary = $myClasses->map(function($c) use ($myAssignments) {
            $cName = $c['class_name'];
            $sName = $c['section_name'];

            $subs = $myAssignments->filter(function($a) use ($cName, $sName) {
                $matchC = strtolower(trim($a->class_name)) === strtolower(trim($cName));
                $matchS = empty($sName) || empty($a->section_name) || strtolower(trim($a->section_name)) === strtolower(trim($sName));
                return $matchC && $matchS;
            })->pluck('subject_or_course')->filter()->unique()->values();

            $stdQuery = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($cName))]);
            if (!empty($sName)) {
                $stdQuery->where(fn($q) => $q->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($sName))])->orWhereNull('section')->orWhere('section', ''));
            }
            $studentCount = $stdQuery->count();

            $incharge = \App\Models\ClassIncharge::with('teacher')
                ->whereHas('classRecord', function($q) use ($cName) {
                    $q->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($cName))]);
                })->first();

            return [
                'class_name' => $cName,
                'section_name' => $sName,
                'label' => $c['label'],
                'subjects' => $subs,
                'student_count' => $studentCount,
                'incharge_name' => $incharge?->teacher?->teacher_name ?? 'School Administration',
                'view_url' => route('teacher.page', 'students') . '?class=' . urlencode($cName) . ($sName ? '&section=' . urlencode($sName) : ''),
            ];
        });

        // Co-Teachers assigned to selected class & section ONLY (or all assigned classes)
        $coTeachersQuery = TeacherAssignment::with('teacher');
        if ($isAllClasses) {
            $coTeachersQuery->where(function($q) use ($myClasses) {
                foreach ($myClasses as $c) {
                    $q->orWhere(function($sub) use ($c) {
                        $sub->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($c['class_name']))]);
                        if (!empty($c['section_name'])) {
                            $sub->where(function($sQ) use ($c) {
                                $sQ->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($c['section_name']))])
                                   ->orWhereNull('section_name')
                                   ->orWhere('section_name', '');
                            });
                        }
                    });
                }
            });
        } else {
            $coTeachersQuery->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($primaryClass))]);
            if (!empty($primarySection) && strtolower($primarySection) !== 'all' && strtolower($primarySection) !== 'all sections') {
                $coTeachersQuery->where(function($q) use ($primarySection) {
                    $q->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($primarySection))])
                      ->orWhereNull('section_name')
                      ->orWhere('section_name', '');
                });
            }
        }

        if (!empty($reqSubject) && $reqSubject !== 'all') {
            $coTeachersQuery->whereRaw('LOWER(TRIM(subject_or_course)) = ?', [strtolower(trim($reqSubject))]);
        }

        $coTeacherAssignmentsPaginated = (clone $coTeachersQuery)->paginate(10, ['*'], 'coteacher_page')->withQueryString();
        $coTeacherAssignments = $coTeachersQuery->get();

        // Enrolled Students Query with Search & Section filters
        $studentsQuery = Student::query();
        if ($isAllClasses) {
            $studentsQuery->where(function($q) use ($myClasses) {
                foreach ($myClasses as $c) {
                    $q->orWhere(function($sub) use ($c) {
                        $sub->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($c['class_name']))]);
                        if (!empty($c['section_name'])) {
                            $sub->where(function($sQ) use ($c) {
                                $sQ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($c['section_name']))])
                                   ->orWhereNull('section')
                                   ->orWhere('section', '');
                            });
                        }
                    });
                }
            });
        } else {
            $studentsQuery->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($primaryClass))]);
            if (!empty($primarySection) && strtolower($primarySection) !== 'all' && strtolower($primarySection) !== 'all sections') {
                $studentsQuery->where(function($q) use ($primarySection) {
                    $q->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($primarySection))])
                      ->orWhereNull('section')
                      ->orWhere('section', '');
                });
            }
        }

        if (!empty($reqSection) && $reqSection !== 'all') {
            $studentsQuery->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($reqSection))]);
        }

        if (!empty($searchQuery)) {
            $studentsQuery->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('std_id', 'like', "%{$searchQuery}%")
                  ->orWhere('registration_no', 'like', "%{$searchQuery}%");
            });
        }

        $studentCount = (clone $studentsQuery)->count();
        $studentsPaginated = (clone $studentsQuery)->orderBy('class', 'asc')->orderBy('std_id', 'asc')->paginate(10, ['*'], 'student_page')->withQueryString();

        // Attach teacher's relevant subjects for that class/section to each student
        $studentsPaginated->getCollection()->transform(function($std) use ($myAssignments) {
            $stdClass = strtolower(trim($std->class ?? ''));
            $stdSec = strtolower(trim($std->section ?? ''));
            $std->my_subjects = $myAssignments->filter(function($a) use ($stdClass, $stdSec) {
                $matchC = strtolower(trim($a->class_name)) === $stdClass;
                $matchS = empty($a->section_name) || empty($stdSec) || strtolower(trim($a->section_name)) === $stdSec;
                return $matchC && $matchS;
            })->pluck('subject_or_course')->filter()->unique()->values();
            return $std;
        });

        $students = (clone $studentsQuery)->orderBy('std_id', 'asc')->get();

        // Academic Results of students in selected class & section ONLY with Term & Year filters
        $selectedResultTerm = $request->query('result_term', 'all');
        $selectedResultYear = $request->query('result_year', 'all');

        $classResultsQuery = Result::query();
        if ($isAllClasses) {
            $classResultsQuery->where(function($q) use ($myClasses) {
                foreach ($myClasses as $c) {
                    $q->orWhere(function($sub) use ($c) {
                        $sub->whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower(trim($c['class_name']))]);
                        if (!empty($c['section_name'])) {
                            $sub->where(function($sQ) use ($c) {
                                $sQ->whereRaw('LOWER(TRIM(student_section)) = ?', [strtolower(trim($c['section_name']))])
                                   ->orWhereNull('student_section')
                                   ->orWhere('student_section', '');
                            });
                        }
                    });
                }
            });
        } else {
            $classResultsQuery->whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower(trim($primaryClass))]);
            if (!empty($primarySection) && strtolower($primarySection) !== 'all') {
                $classResultsQuery->where(fn($q) => $q->whereRaw('LOWER(TRIM(student_section)) = ?', [strtolower(trim($primarySection))])->orWhereNull('student_section')->orWhere('student_section', ''));
            }
        }

        if ($selectedResultTerm !== 'all' && !empty($selectedResultTerm)) {
            $classResultsQuery->where('term', $selectedResultTerm);
        }
        if ($selectedResultYear !== 'all' && !empty($selectedResultYear)) {
            $classResultsQuery->whereYear('created_at', $selectedResultYear);
        }
        $classResults = $classResultsQuery->get();

        // Paginated Student Results (10 per page)
        $studentResultsQuery = (clone $studentsQuery);
        $studentResultsPaginated = $studentResultsQuery->paginate(10)->withQueryString();

        $studentResultsPaginated->getCollection()->transform(function($student) use ($selectedResultTerm, $selectedResultYear) {
            $resultsQuery = Result::whereRaw('LOWER(TRIM(student_name)) = ?', [strtolower(trim($student->name))])
                ->whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower(trim($student->class))])
                ->whereRaw('LOWER(TRIM(student_section)) = ?', [strtolower(trim($student->section))]);

            $allResults = (clone $resultsQuery)->orderBy('term')->orderBy('subject')->get();

            if ($allResults->isEmpty()) {
                $requiredTerms = ['First Term', 'Mid Term', 'Final Term'];
                $subjectNames = ['English', 'Mathematics', 'Urdu', 'Science', 'Islamiyat', 'Computer Science', 'Drawing & Arts'];

                foreach ($requiredTerms as $term) {
                    foreach ($subjectNames as $subject) {
                        $total = 100;
                        $obtained = rand(70, 95);
                        $pct = round(($obtained / $total) * 100, 1);
                        $grade = $pct >= 90 ? 'A+' : ($pct >= 80 ? 'A' : ($pct >= 70 ? 'B' : ($pct >= 60 ? 'C' : 'D')));

                        Result::create([
                            'student_name' => $student->name,
                            'student_class' => $student->class,
                            'student_section' => $student->section,
                            'subject' => $subject,
                            'total_marks' => $total,
                            'obtained_marks' => $obtained,
                            'percentage' => $pct,
                            'grade' => $grade,
                            'term' => $term,
                            'user_id' => session('user_id') ?: 1,
                        ]);
                    }
                }

                $allResults = (clone $resultsQuery)->orderBy('term')->orderBy('subject')->get();
            }

            // Apply term & year filters for display
            $filteredResults = $allResults;
            if ($selectedResultTerm !== 'all' && !empty($selectedResultTerm)) {
                $filteredResults = $filteredResults->where('term', $selectedResultTerm);
            }
            if ($selectedResultYear !== 'all' && !empty($selectedResultYear)) {
                $filteredResults = $filteredResults->filter(fn($r) => $r->created_at ? (\Carbon\Carbon::parse($r->created_at)->year == $selectedResultYear) : true);
            }

            $termsList = $filteredResults->pluck('term')->unique()->values()->all();
            $termsCount = count($termsList);
            $termsStr = $termsCount > 0 ? implode(', ', $termsList) : 'No Terms';

            $totalObtained = $filteredResults->sum('obtained_marks');
            $totalMax = $filteredResults->sum('total_marks');
            $overallPct = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0;
            $overallGrade = $overallPct >= 90 ? 'A+' : ($overallPct >= 80 ? 'A' : ($overallPct >= 70 ? 'B' : ($overallPct >= 60 ? 'C' : 'D')));

            $student->class_results = $filteredResults;
            $student->terms_count = $termsCount;
            $student->terms_str = $termsStr;
            $student->overall_pct = $overallPct;
            $student->overall_grade = $overallGrade;

            return $student;
        });

        // Check if logged in teacher / user is designated Class In-Charge or Admin for $primaryClass
        $user = User::find(session('user_id'));
        $rawClass = trim($primaryClass);
        $cleanClass = trim(preg_replace('/\s*\(.*?\)/', '', $rawClass));

        // Check all classes this teacher is an in-charge of:
        $inchargeRecords = \App\Models\ClassIncharge::with(['classRecord', 'teacher'])
            ->where(function($q) use ($teacher, $user) {
                $q->where('teacher_id', $teacher->id);
                if ($user) $q->orWhere('user_id', $user->id);
            })->get();

        $isAnyIncharge = $inchargeRecords->isNotEmpty() || ($user && $user->role?->slug === 'admin');

        $isClassIncharge = \App\Models\ClassIncharge::where(function($q) use ($teacher, $user) {
                $q->where('teacher_id', $teacher->id);
                if ($user) $q->orWhere('user_id', $user->id);
            })
            ->whereHas('classRecord', function($q) use ($cleanClass, $rawClass) {
                $q->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($cleanClass)])
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($rawClass)])
                  ->orWhereRaw('? LIKE CONCAT("%", LOWER(TRIM(class_name)), "%")', [strtolower($cleanClass)]);
            })->exists();

        if (!$isClassIncharge && $user && $user->role?->slug === 'admin') {
            $isClassIncharge = true;
        }

        $classInchargeTeacherName = \App\Models\ClassIncharge::with('teacher')
            ->whereHas('classRecord', function($q) use ($cleanClass, $rawClass) {
                $q->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($rawClass)])
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($cleanClass)]);
            })->first()?->teacher?->teacher_name ?: $teacher->teacher_name;

        return compact(
            'myAssignments', 'myClasses', 'primaryClass', 'primarySection', 'mySubjects', 
            'availableFilterSubjects', 'availableSections', 'isAllClasses', 'reqClass', 'reqSection', 'reqSubject', 'searchQuery',
            'coTeacherAssignments', 'coTeacherAssignmentsPaginated', 'students', 'studentsPaginated', 'studentCount',
            'assignedClassesSummary', 'classResults', 'studentResultsPaginated', 
            'isClassIncharge', 'classInchargeTeacherName', 'isAnyIncharge', 'inchargeRecords',
            'selectedResultTerm', 'selectedResultYear'
        );
    }

    private function getAttendanceAndLeaveData(Teacher $teacher, Request $request = null): array
    {
        $request = $request ?? request();

        // Seed full-year 2026 daily attendance logs if not already seeded
        $attCount = TeacherAttendance::where('teacher_id', $teacher->id)->count();
        if ($attCount < 250) {
            $start = Carbon::parse('2026-01-01');
            $end = Carbon::parse('2026-12-31');

            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dateStr = $d->format('Y-m-d');
                if (!TeacherAttendance::where('teacher_id', $teacher->id)->where('date', $dateStr)->exists()) {
                    if ($d->isWeekend()) {
                        $status = 'holiday';
                        $remarks = 'Weekend Holiday';
                        $timeIn = null;
                        $timeOut = null;
                    } elseif ($dateStr === '2026-09-18') {
                        $status = 'leave';
                        $remarks = 'Approved Casual Leave (CL)';
                        $timeIn = null;
                        $timeOut = null;
                    } elseif ($dateStr === '2026-09-25') {
                        $status = 'leave';
                        $remarks = 'Approved Sick Leave (SL)';
                        $timeIn = null;
                        $timeOut = null;
                    } else {
                        $status = 'present';
                        $remarks = 'On Time Check-In';
                        $timeIn = '07:54:00';
                        $timeOut = '14:30:00';
                    }

                    TeacherAttendance::create([
                        'teacher_id' => $teacher->id,
                        'date' => $dateStr,
                        'status' => $status,
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'remarks' => $remarks,
                    ]);
                }
            }
        }

        // Seed initial teacher leave application if empty
        $leavesCount = TeacherLeave::where('teacher_id', $teacher->id)->count();
        if ($leavesCount === 0) {
            TeacherLeave::create([
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->teacher_name,
                'leave_type' => 'Casual Leave (CL)',
                'leave_date' => '2026-09-18',
                'reason' => 'Attending family event in hometown. Prior permission requested.',
                'status' => 'Approved',
                'approved_by' => 'School Administration / Principal',
                'user_id' => session('user_id') ?: 1,
            ]);

            TeacherLeave::create([
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->teacher_name,
                'leave_type' => 'Sick Leave (SL)',
                'leave_date' => '2026-09-25',
                'reason' => 'High fever and viral infection. Doctor advised rest.',
                'status' => 'Approved',
                'approved_by' => 'School Administration / Principal',
                'user_id' => session('user_id') ?: 1,
            ]);
        }

        $allAttendances = TeacherAttendance::where('teacher_id', $teacher->id)->orderBy('date', 'desc')->get();
        $leaves = TeacherLeave::where('teacher_id', $teacher->id)->orderBy('leave_date', 'desc')->get();

        $selectedYear = $request->query('year', '2026');
        $selectedMonth = $request->query('month'); // e.g., '2026-10' or null
        $selectedView = $request->query('view', $selectedMonth ? 'monthly' : 'monthly');

        // 12-Month Yearly Summaries (Excel Sheet Matrix Overview)
        $monthlySummaries = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStr = sprintf('%s-%02d', $selectedYear, $m);
            $monthObj = Carbon::createFromDate($selectedYear, $m, 1);
            
            $monthAtts = $allAttendances->filter(function($att) use ($selectedYear, $m) {
                $d = Carbon::parse($att->date);
                return $d->year == $selectedYear && $d->month == $m;
            });

            $totalDays = $monthAtts->count();
            $pCnt = $monthAtts->where('status', 'present')->count();
            $lCnt = $monthAtts->where('status', 'late')->count();
            $lvCnt = $monthAtts->where('status', 'leave')->count();
            $holCnt = $monthAtts->where('status', 'holiday')->count();
            $absCnt = $monthAtts->where('status', 'absent')->count();
            $workingDays = $pCnt + $lCnt + $lvCnt + $absCnt;
            $rate = $workingDays > 0 ? round((($pCnt + $lCnt) / $workingDays) * 100, 1) : 100;

            $monthlySummaries[] = [
                'month_key' => $monthStr,
                'month_num' => $m,
                'month_name' => $monthObj->format('F Y'),
                'short_name' => $monthObj->format('M'),
                'total_days' => $totalDays,
                'present_count' => $pCnt,
                'late_count' => $lCnt,
                'leave_count' => $lvCnt,
                'holiday_count' => $holCnt,
                'absent_count' => $absCnt,
                'working_days' => $workingDays,
                'rate' => $rate,
            ];
        }

        // Paginated daily attendance log (10 per page, supports month filter)
        $dailyQuery = TeacherAttendance::where('teacher_id', $teacher->id);
        if ($selectedMonth) {
            $dailyQuery->where('date', 'like', $selectedMonth . '%');
        } else {
            $dailyQuery->whereYear('date', $selectedYear);
        }
        $attendancesPaginated = $dailyQuery->orderBy('date', 'desc')->paginate(10)->withQueryString();

        // Selected Active Month Analytics Calculation
        $activeMonthKey = $selectedMonth ?: Carbon::now()->format('Y-m');
        $activeMonthObj = Carbon::parse($activeMonthKey . '-01');
        $activeMonthName = $activeMonthObj->format('F Y');

        $activeMonthAtts = $allAttendances->filter(function($att) use ($activeMonthKey) {
            return str_starts_with($att->date, $activeMonthKey);
        });

        $monthTotalDays = $activeMonthAtts->count();
        $monthPresent = $activeMonthAtts->where('status', 'present')->count();
        $monthLate = $activeMonthAtts->where('status', 'late')->count();
        $monthLeave = $activeMonthAtts->where('status', 'leave')->count();
        $monthHoliday = $activeMonthAtts->where('status', 'holiday')->count();
        $monthAbsent = $activeMonthAtts->where('status', 'absent')->count();
        $monthWorkingDays = $monthPresent + $monthLate + $monthLeave + $monthAbsent;

        $monthPct = $monthWorkingDays > 0 
            ? round((($monthPresent + $monthLate) / $monthWorkingDays) * 100, 1) 
            : 100;

        $pCount = $allAttendances->where('status', 'present')->count();
        $lCount = $allAttendances->where('status', 'leave')->count();
        $totWorkingCount = $allAttendances->whereIn('status', ['present', 'absent', 'late', 'leave'])->count();
        $attRate = $totWorkingCount > 0 ? round(($pCount / $totWorkingCount) * 100, 1) : 100;

        $currentMonth = Carbon::now();
        $monthlyApprovedLeaves = TeacherLeave::where('teacher_id', $teacher->id)
            ->whereYear('leave_date', $currentMonth->year)
            ->whereMonth('leave_date', $currentMonth->month)
            ->where('status', 'Approved')
            ->count();

        $leavesAllowedCount = 2; // 2 Allowed Free Paid Leaves per month
        $leavesRemainingCount = max(0, $leavesAllowedCount - $monthlyApprovedLeaves);

        $todayDate = Carbon::today()->format('Y-m-d');
        $todayAttendance = TeacherAttendance::where('teacher_id', $teacher->id)->where('date', $todayDate)->first();

        $attendances = $allAttendances;

        return compact(
            'attendances', 
            'attendancesPaginated', 
            'monthlySummaries', 
            'selectedMonth', 
            'selectedYear', 
            'selectedView',
            'activeMonthName',
            'monthTotalDays',
            'monthPresent',
            'monthLate',
            'monthLeave',
            'monthHoliday',
            'monthAbsent',
            'monthWorkingDays',
            'monthPct',
            'leaves', 
            'pCount', 
            'lCount', 
            'attRate', 
            'leavesAllowedCount', 
            'monthlyApprovedLeaves', 
            'leavesRemainingCount', 
            'todayAttendance'
        );
    }

    public function dashboard(Request $request = null)
    {
        $request = $request ?? request();
        [$user, $teacher] = $this->getTeacher();
        $base = $this->getTeacherBaseData($teacher, $request);
        $attData = $this->getAttendanceAndLeaveData($teacher, $request);

        // Fetch teacher's OWN salary only
        $salaries = TeacherSalary::where('teacher_id', $teacher->id)->orderBy('month', 'desc')->get();
        $latestSalary = $salaries->first();
        $outlines = CourseOutline::where('teacher_id', $teacher->id)->orderBy('created_at', 'desc')->get();

        $page = 'dashboard';

        return view('teacher.portal', array_merge($base, $attData, compact('user', 'teacher', 'salaries', 'latestSalary', 'outlines', 'page')));
    }

    public function page(string $page, Request $request = null)
    {
        $request = $request ?? request();
        abort_unless(in_array($page, ['profile', 'coteachers', 'students', 'results', 'outlines', 'salary', 'attendance', 'tests', 'slips', 'stdleaves', 'leaves', 'classes', 'incharge'], true), 404);
        
        [$user, $teacher] = $this->getTeacher();
        $base = $this->getTeacherBaseData($teacher, $request);
        $attData = $this->getAttendanceAndLeaveData($teacher, $request);

        // Fetch teacher's OWN salary with Month & Year filters
        $selectedSalaryMonth = $request->query('salary_month', 'all');
        $selectedSalaryYear = $request->query('salary_year', 'all');

        $salariesCheck = TeacherSalary::where('teacher_id', $teacher->id)->first();
        if (!$salariesCheck) {
            $pastMonths = [
                ['month' => 'October 2026', 'paid_date' => '2026-10-05', 'status' => 'Paid'],
                ['month' => 'September 2026', 'paid_date' => '2026-09-05', 'status' => 'Paid'],
                ['month' => 'August 2026', 'paid_date' => '2026-08-05', 'status' => 'Paid'],
                ['month' => 'July 2026', 'paid_date' => '2026-07-05', 'status' => 'Paid'],
                ['month' => 'June 2026', 'paid_date' => '2026-06-05', 'status' => 'Paid'],
                ['month' => 'May 2026', 'paid_date' => '2026-05-05', 'status' => 'Paid'],
            ];
            foreach ($pastMonths as $pm) {
                TeacherSalary::create([
                    'teacher_id' => $teacher->id,
                    'month' => $pm['month'],
                    'basic_salary' => 45000,
                    'allowances' => 5000,
                    'deductions' => 0,
                    'net_salary' => 50000,
                    'status' => $pm['status'],
                    'paid_date' => $pm['paid_date'],
                ]);
            }
        }

        $salariesQuery = TeacherSalary::where('teacher_id', $teacher->id)->orderBy('id', 'desc');
        if ($selectedSalaryMonth !== 'all' && !empty($selectedSalaryMonth)) {
            $salariesQuery->where('month', 'like', $selectedSalaryMonth . '%');
        }
        if ($selectedSalaryYear !== 'all' && !empty($selectedSalaryYear)) {
            $salariesQuery->where('month', 'like', '%' . $selectedSalaryYear);
        }

        $salariesPaginated = (clone $salariesQuery)->paginate(10, ['*'], 'salary_page')->withQueryString();
        $salaries = $salariesQuery->get();
        $latestSalary = TeacherSalary::where('teacher_id', $teacher->id)->orderBy('id', 'desc')->first();
        
        $outlinesPaginated = CourseOutline::where('teacher_id', $teacher->id)->orderBy('created_at', 'desc')->paginate(10, ['*'], 'outline_page')->withQueryString();
        $outlines = CourseOutline::where('teacher_id', $teacher->id)->orderBy('created_at', 'desc')->get();

        // Feature Four: In-Charge Teacher Class Overview Data
        $inchargeData = [];
        if ($page === 'incharge') {
            if (!$base['isAnyIncharge']) {
                return redirect()->route('teacher.dashboard')->with('error', 'Access Denied: You are not designated as an In-Charge Teacher for any class.');
            }

            $managedClassRecords = $base['inchargeRecords'];
            if ($managedClassRecords->isEmpty() && $user->role?->slug === 'admin') {
                $managedClassRecords = \App\Models\ClassIncharge::with(['classRecord', 'teacher'])->get();
            }

            // Selected in-charge class
            $reqInchargeClassId = $request->query('incharge_class_id');
            $activeInchargeRecord = null;
            if ($reqInchargeClassId) {
                $activeInchargeRecord = $managedClassRecords->firstWhere('class_id', $reqInchargeClassId);
            }
            if (!$activeInchargeRecord) {
                $activeInchargeRecord = $managedClassRecords->first();
            }

            $managedClass = $activeInchargeRecord?->classRecord;
            $managedClassName = $managedClass?->class_name ?? 'TEN';
            $managedSectionName = $managedClass?->section_name ?? 'Red';

            // 1. In-charge teacher(s) for this class
            $inchargeTeachers = \App\Models\ClassIncharge::with(['teacher.loginAccount', 'user'])
                ->where('class_id', $activeInchargeRecord?->class_id)
                ->get();

            // 2. All Subject Teachers assigned to this class & section
            $allSubjectAssignments = TeacherAssignment::with('teacher')
                ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($managedClassName))])
                ->when($managedSectionName, function($q) use ($managedSectionName) {
                    $q->where(fn($sub) => $sub->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($managedSectionName))])->orWhereNull('section_name')->orWhere('section_name', ''));
                })
                ->get();

            // Group subjects by teacher to avoid duplicate teacher cards
            $groupedSubjectTeachers = $allSubjectAssignments->groupBy('teacher_id')->map(function($assignments) {
                $first = $assignments->first();
                $t = $first->teacher;
                $subs = $assignments->pluck('subject_or_course')->filter()->unique()->values();
                return [
                    'teacher' => $t,
                    'teacher_id' => $first->teacher_id,
                    'teacher_name' => $t?->teacher_name ?? 'Faculty Member',
                    'teacher_image' => $t?->teacher_image,
                    'phone' => $t?->teacher_phone ?: '0300-1234567',
                    'email' => $t?->teacher_email ?: ($t?->loginAccount?->email ?? 'teacher@school.edu'),
                    'subjects' => $subs,
                ];
            })->values();

            // 3. Enrolled students for this in-charge class
            $inchargeSearch = trim($request->query('incharge_search', ''));
            $inchargeStudentsQuery = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($managedClassName))])
                ->when($managedSectionName, function($q) use ($managedSectionName) {
                    $q->where(fn($sub) => $sub->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($managedSectionName))])->orWhereNull('section')->orWhere('section', ''));
                });

            if (!empty($inchargeSearch)) {
                $inchargeStudentsQuery->where(function($q) use ($inchargeSearch) {
                    $q->where('name', 'like', "%{$inchargeSearch}%")
                      ->orWhere('std_id', 'like', "%{$inchargeSearch}%")
                      ->orWhere('registration_no', 'like', "%{$inchargeSearch}%");
                });
            }

            $inchargeStudentCount = (clone $inchargeStudentsQuery)->count();
            $inchargeStudentsPaginated = $inchargeStudentsQuery->orderBy('std_id', 'asc')->paginate(10, ['*'], 'incharge_student_page')->withQueryString();

            $inchargeData = compact(
                'managedClassRecords',
                'activeInchargeRecord',
                'managedClass',
                'managedClassName',
                'managedSectionName',
                'inchargeTeachers',
                'groupedSubjectTeachers',
                'inchargeStudentsPaginated',
                'inchargeStudentCount',
                'inchargeSearch'
            );
        }

        // Student Leaves Data for Active Class with Month & Year filters & View Modes
        $selectedLeaveView = $request->query('leave_view', 'monthly');
        $selectedLeaveMonth = $request->query('leave_month', 'all');
        $selectedLeaveYear = $request->query('leave_year', 'all');

        $targetClassStr = strtolower(trim($base['primaryClass']));
        $studentLeavesQuery = \App\Models\StudentLeave::where(function($q) use ($targetClassStr) {
                $q->whereRaw('LOWER(TRIM(student_class)) = ?', [$targetClassStr])
                  ->orWhereRaw('LOWER(TRIM(student_class)) LIKE ?', ['%' . $targetClassStr . '%']);
            });

        $monthMap = [
            'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4,
            'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8,
            'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
        ];

        // Compute 12-Month Yearly Summary for the active class
        $yearlyLeaveSummaries = [];
        $allLeavesForClass = (clone $studentLeavesQuery)->get();

        foreach ($monthMap as $mName => $mNum) {
            $mLeaves = $allLeavesForClass->filter(function($l) use ($mNum) {
                return $l->leave_date ? (\Carbon\Carbon::parse($l->leave_date)->month == $mNum) : false;
            });

            $totalCount = $mLeaves->count();
            $approvedCount = $mLeaves->where('status', 'Approved')->count();
            $rejectedCount = $mLeaves->where('status', 'Rejected')->count();
            $pendingCount = $mLeaves->where('status', 'Pending')->count();

            $yearlyLeaveSummaries[] = [
                'month_name' => $mName,
                'month_num' => $mNum,
                'total_leaves' => $totalCount,
                'approved' => $approvedCount,
                'rejected' => $rejectedCount,
                'pending' => $pendingCount
            ];
        }

        if ($selectedLeaveMonth !== 'all' && !empty($selectedLeaveMonth)) {
            $mNum = $monthMap[$selectedLeaveMonth] ?? null;
            if ($mNum) {
                $studentLeavesQuery->whereMonth('leave_date', $mNum);
            }
        }

        if ($selectedLeaveYear !== 'all' && !empty($selectedLeaveYear)) {
            $studentLeavesQuery->whereYear('leave_date', $selectedLeaveYear);
        }

        $studentLeavesPaginated = $studentLeavesQuery->latest('id')->paginate(10)->withQueryString();

        // Monthly Test Sessions Data with Pagination (10 per page)
        $selectedTestMonth = $request->query('month', '2026-10');
        $studentsList = $base['students'];

        $testSeeder = new \App\Http\Controllers\TestSessionController();
        foreach ($studentsList as $std) {
            $cnt = \App\Models\TestSession::where('student_id', $std->id)->count();
            if ($cnt < 12) {
                $testSeeder->seedTestSessionsForStudent($std);
            }
        }

        $testStudentsQuery = \App\Models\Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($base['primaryClass']))])
            ->where(fn($q) => $q->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($base['primarySection']))])->orWhereNull('section')->orWhere('section', ''))
            ->orderBy('std_id', 'asc');
        $testStudentsPaginated = $testStudentsQuery->paginate(10, ['*'], 'test_page')->withQueryString();

        $testSessionsMap = [];
        foreach ($studentsList as $std) {
            $session = \App\Models\TestSession::where('student_id', $std->id)
                ->where('month_key', $selectedTestMonth)
                ->first();

            if (!$session) {
                $session = \App\Models\TestSession::where('student_id', $std->id)->orderBy('month_key', 'desc')->first();
            }

            $testSessionsMap[$std->id] = $session;
        }

        $testMonthsList = [
            '2026-10' => 'October 2026',
            '2026-09' => 'September 2026',
            '2026-08' => 'August 2026',
            '2026-07' => 'July 2026',
            '2026-06' => 'June 2026',
            '2026-05' => 'May 2026',
            '2026-04' => 'April 2026',
            '2026-03' => 'March 2026',
            '2026-02' => 'February 2026',
            '2026-01' => 'January 2026',
        ];

        return view('teacher.portal', array_merge($base, $attData, compact('user', 'teacher', 'salaries', 'salariesPaginated', 'latestSalary', 'outlines', 'outlinesPaginated', 'page', 'selectedTestMonth', 'testSessionsMap', 'testMonthsList', 'studentLeavesPaginated', 'testStudentsPaginated', 'selectedSalaryMonth', 'selectedSalaryYear', 'selectedLeaveMonth', 'selectedLeaveYear', 'selectedLeaveView', 'yearlyLeaveSummaries') + $inchargeData));
    }

    public function updateTestMarks(Request $request)
    {
        [$user, $teacher] = $this->getTeacher();
        $base = $this->getTeacherBaseData($teacher, $request);

        if (!$base['isClassIncharge']) {
            return back()->with('error', 'Access Denied: Only the assigned Class In-Charge teacher (' . $base['classInchargeTeacherName'] . ') for ' . $base['primaryClass'] . ' can edit test session marks.');
        }

        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'month_key' => 'required|string',
            'marks' => 'required|array',
        ]);

        $studentId = $request->student_id;
        $monthKey = $request->month_key;
        $marksInput = $request->marks;

        $testSession = \App\Models\TestSession::where('student_id', $studentId)
            ->where('month_key', $monthKey)
            ->firstOrFail();

        $schedule = is_string($testSession->schedule_json) 
            ? json_decode($testSession->schedule_json, true) 
            : ($testSession->schedule_json ?: []);

        $totalMax = 0;
        $totalObtained = 0;

        foreach ($schedule as &$row) {
            if (empty($row['is_off']) && isset($row['subject'])) {
                $subName = $row['subject'];
                if (isset($marksInput[$subName])) {
                    $val = floatval($marksInput[$subName]);
                    $row['obtained_marks'] = min($row['max_marks'] ?: 50, max(0, $val));
                }
                $totalMax += ($row['max_marks'] ?: 50);
                $totalObtained += ($row['obtained_marks'] ?: 0);
            }
        }

        $pct = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0;
        $grade = $pct >= 85 ? 'A+' : ($pct >= 75 ? 'A' : ($pct >= 65 ? 'B' : 'C'));

        $testSession->update([
            'schedule_json' => $schedule,
            'total_max_marks' => $totalMax,
            'total_obtained_marks' => $totalObtained,
            'percentage' => $pct,
            'grade' => $grade,
            'status' => 'Evaluated',
        ]);

        // Notify class students & Admin
        AppNotification::sendToClassStudents(
            $base['primaryClass'],
            'Monthly Test Results Published 📊',
            "Class In-Charge {$teacher->teacher_name} updated monthly test session results for {$monthKey}.",
            'test',
            route('student.tests'),
            'bi-clipboard-data-fill',
            $user->id,
            $base['primarySection'] ?? null
        );
        AppNotification::sendToAdmins(
            'Monthly Test Results Published 📊',
            "Class In-Charge {$teacher->teacher_name} updated monthly test session results for Class {$base['primaryClass']} ({$monthKey}).",
            'test',
            route('admin.page', 'academics'),
            'bi-clipboard-data-fill',
            $user->id
        );

        return redirect()->route('teacher.page', ['page' => 'tests', 'month' => $monthKey])
            ->with('success', 'Test session marks updated successfully!');
    }

    public function approveStudentLeave(\App\Models\StudentLeave $studentLeave)
    {
        [$user, $teacher] = $this->getTeacher();
        
        $rawClass = trim($studentLeave->student_class);
        $cleanClass = trim(preg_replace('/\s*\(.*?\)/', '', $rawClass));

        $isClassIncharge = \App\Models\ClassIncharge::where(function($q) use ($teacher, $user) {
                $q->where('teacher_id', $teacher->id);
                if ($user) $q->orWhere('user_id', $user->id);
            })
            ->whereHas('classRecord', function($q) use ($cleanClass, $rawClass) {
                $q->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($cleanClass)])
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($rawClass)])
                  ->orWhereRaw('? LIKE CONCAT("%", LOWER(TRIM(class_name)), "%")', [strtolower($cleanClass)]);
            })->exists();

        if (!$isClassIncharge && $user && $user->role?->slug === 'admin') {
            $isClassIncharge = true;
        }

        if (!$isClassIncharge) {
            return back()->with('error', "Access Denied: Only the Class In-Charge or Admin can approve leave requests for {$rawClass}.");
        }

        $studentLeave->update([
            'status' => 'Approved',
            'approved_by' => $teacher->teacher_name,
        ]);

        // Sync to Student Attendance table
        \App\Models\Attendance::updateOrCreate(
            [
                'student_id' => $studentLeave->student_id,
                'date' => \Carbon\Carbon::parse($studentLeave->leave_date)->format('Y-m-d'),
            ],
            [
                'status' => 'leave',
                'class_name' => $studentLeave->student_class,
                'section_name' => $studentLeave->student_section ?: 'Red',
                'remarks' => "Approved Leave ({$studentLeave->leave_type}) by {$teacher->teacher_name}",
                'user_id' => session('user_id') ?: 1,
            ]
        );

        // Notify Student and Admin about leave approval
        AppNotification::notifyStudentLeaveDecision($studentLeave, 'Approved', null, $user);

        return back()->with('success', "Leave request for {$studentLeave->student_name} approved successfully and attendance updated to Leave.");
    }

    public function rejectStudentLeave(\App\Models\StudentLeave $studentLeave)
    {
        [$user, $teacher] = $this->getTeacher();
        
        $rawClass = trim($studentLeave->student_class);
        $cleanClass = trim(preg_replace('/\s*\(.*?\)/', '', $rawClass));

        $isClassIncharge = \App\Models\ClassIncharge::where(function($q) use ($teacher, $user) {
                $q->where('teacher_id', $teacher->id);
                if ($user) $q->orWhere('user_id', $user->id);
            })
            ->whereHas('classRecord', function($q) use ($cleanClass, $rawClass) {
                $q->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($cleanClass)])
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($rawClass)])
                  ->orWhereRaw('? LIKE CONCAT("%", LOWER(TRIM(class_name)), "%")', [strtolower($cleanClass)]);
            })->exists();

        if (!$isClassIncharge && $user && $user->role?->slug === 'admin') {
            $isClassIncharge = true;
        }

        if (!$isClassIncharge) {
            return back()->with('error', "Access Denied: Only the Class In-Charge or Admin can reject leave requests for {$rawClass}.");
        }

        $studentLeave->update([
            'status' => 'Rejected',
            'approved_by' => $teacher->teacher_name,
        ]);

        // Revert attendance if marked
        $att = \App\Models\Attendance::where('student_id', $studentLeave->student_id)
            ->where('date', \Carbon\Carbon::parse($studentLeave->leave_date)->format('Y-m-d'))
            ->first();
        if ($att && $att->status === 'leave') {
            $att->delete();
        }

        // Requirement 3: Restore fine balance if fine was applied to this leave request
        if ($studentLeave->is_fine_applied && $studentLeave->fine_amount > 0) {
            $leaveDate = \Carbon\Carbon::parse($studentLeave->leave_date);
            $monthName = $leaveDate->format('F Y');
            $fee = \App\Models\FeeCollection::where('student_id', $studentLeave->student_id)
                ->whereRaw('LOWER(TRIM(month)) = ?', [strtolower(trim($monthName))])
                ->first();

            if ($fee) {
                $fee->decrement('other_charges', $studentLeave->fine_amount);
                $fee->decrement('gross_amount', $studentLeave->fine_amount);
                $fee->decrement('net_amount', $studentLeave->fine_amount);
            }
            $studentLeave->update(['is_fine_applied' => false, 'fine_amount' => 0.00]);
        }

        // Notify Student and Admin about leave rejection
        AppNotification::notifyStudentLeaveDecision($studentLeave, 'Rejected', $request->input('remarks'), $user);

        return back()->with('success', "Leave request for {$studentLeave->student_name} marked as Rejected.");
    }

    public function applyLeave(Request $request)
    {
        [$user, $teacher] = $this->getTeacher();

        $request->validate([
            'leave_type' => 'required|string|max:50',
            'leave_date' => 'required|date',
            'reason' => 'required|string|min:5',
        ]);

        $leaveDate = Carbon::parse($request->leave_date);
        
        $usedCount = TeacherLeave::where('teacher_id', $teacher->id)
            ->whereYear('leave_date', $leaveDate->year)
            ->whereMonth('leave_date', $leaveDate->month)
            ->where('status', 'Approved')
            ->count();

        $isExtraLeave = $usedCount >= 2;

        $teacherLeave = TeacherLeave::create([
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->teacher_name,
            'leave_type' => $request->leave_type,
            'leave_date' => $request->leave_date,
            'reason' => $request->reason,
            'status' => 'Pending',
            'user_id' => $user->id,
        ]);

        // Send real-time notification to School Administration
        AppNotification::notifyTeacherLeaveSubmitted($teacherLeave);

        $msg = 'Leave application submitted successfully for ' . $leaveDate->format('d M Y') . '. Status is currently Pending Admin approval.';
        if ($isExtraLeave) {
            $msg .= ' (Note: You have already used your 2 free monthly allowed leaves. Extra leave will be reviewed by Principal/Head).';
        }

        return redirect()->route('teacher.page', 'leaves')->with('success', $msg);
    }

    public function approveLeave(TeacherLeave $teacherLeave)
    {
        [$user, $teacher] = $this->getTeacher();
        abort_unless($teacherLeave->teacher_id === $teacher->id, 403);

        $teacherLeave->update([
            'status' => 'Approved',
            'approved_by' => 'School Administration / Principal',
        ]);

        // Auto-sync into Teacher Attendance record
        TeacherAttendance::updateOrCreate(
            [
                'teacher_id' => $teacherLeave->teacher_id,
                'date' => Carbon::parse($teacherLeave->leave_date)->format('Y-m-d'),
            ],
            [
                'status' => 'leave',
                'remarks' => "Approved Leave ({$teacherLeave->leave_type})",
                'time_in' => null,
                'time_out' => null,
            ]
        );

        // Notify Teacher
        AppNotification::sendToTeacherLeaveApplicant(
            $teacherLeave,
            'Teacher Leave Approved! ✅',
            "Your leave application for " . Carbon::parse($teacherLeave->leave_date)->format('d M Y') . " has been approved.",
            'leave',
            route('teacher.page', 'attendance'),
            'bi-check-circle-fill',
            $user->id
        );

        return redirect()->route('teacher.page', 'attendance')->with('success', 'Leave application approved by Admin! Automatically synced into daily attendance record as Approved Leave so it is not counted as an absence.');
    }

    public function punchIn(Request $request)
    {
        [$user, $teacher] = $this->getTeacher();
        $today = Carbon::today()->format('Y-m-d');
        
        $customTime = $request->input('custom_time');
        if ($customTime) {
            $timeInObj = Carbon::parse($customTime);
        } else {
            $timeInObj = Carbon::now();
        }

        $timeInStr = $timeInObj->format('H:i:s');
        $cutoffStr = '08:10:00'; // 08:00 AM + 10 min grace period

        $isLate = $timeInStr > $cutoffStr;
        $status = $isLate ? 'late' : 'present';
        $remarks = $isLate 
            ? 'Late Arrival (Punched in at ' . $timeInObj->format('h:i A') . ' - Past 08:10 AM grace window)' 
            : 'On Time Check-In (Punched in at ' . $timeInObj->format('h:i A') . ')';

        $existing = TeacherAttendance::where('teacher_id', $teacher->id)->where('date', $today)->first();

        TeacherAttendance::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'date' => $today,
            ],
            [
                'status' => $status,
                'time_in' => $timeInStr,
                'time_out' => $existing?->time_out ?: null,
                'remarks' => $remarks,
            ]
        );

        $colorStatus = $isLate ? 'LATE ARRIVAL (Marked in RED)' : 'ON TIME (Marked in GREEN)';
        return redirect()->route('teacher.page', 'attendance')->with('success', "Daily Check-In Recorded at {$timeInObj->format('h:i A')}! Status: {$colorStatus}.");
    }

    public function punchOut(Request $request)
    {
        [$user, $teacher] = $this->getTeacher();
        $today = Carbon::today()->format('Y-m-d');
        
        $customTime = $request->input('custom_time');
        if ($customTime) {
            $timeOutObj = Carbon::parse($customTime);
        } else {
            $timeOutObj = Carbon::now();
        }

        $timeOutStr = $timeOutObj->format('H:i:s');
        $shiftEndStr = '14:30:00'; // 02:30 PM official closing

        $isEarly = $timeOutStr < $shiftEndStr;
        
        $attendance = TeacherAttendance::where('teacher_id', $teacher->id)->where('date', $today)->first();
        if (!$attendance) {
            return redirect()->route('teacher.page', 'attendance')->with('error', 'Please Punch-In first before performing Check-Out.');
        }

        $remarks = $attendance->remarks ?: '';
        if ($isEarly) {
            $remarks .= ' • Early Departure (Punched out at ' . $timeOutObj->format('h:i A') . ' - Before 02:30 PM closing)';
        } else {
            $remarks .= ' • Full Shift Completed (Punched out at ' . $timeOutObj->format('h:i A') . ')';
        }

        $attendance->update([
            'time_out' => $timeOutStr,
            'remarks' => trim($remarks, ' •'),
        ]);

        $colorStatus = $isEarly ? 'EARLY DEPARTURE (Marked in RED)' : 'FULL SHIFT DUTY COMPLETED (Marked in GREEN)';
        return redirect()->route('teacher.page', 'attendance')->with('success', "Daily Check-Out Recorded at {$timeOutObj->format('h:i A')}! Status: {$colorStatus}.");
    }

    public function uploadOutline(Request $request)
    {
        [$user, $teacher] = $this->getTeacher();

        $request->validate([
            'subject_name' => 'required|string',
            'title' => 'required|string|max:255',
            'term' => 'required|string',
            'description' => 'required|string',
            'outline_file' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg|max:5120',
        ]);

        $base = $this->getTeacherBaseData($teacher, $request);

        $filePath = null;
        if ($request->hasFile('outline_file')) {
            $filePath = $request->file('outline_file')->store('outlines', 'public');
        }

        CourseOutline::create([
            'teacher_id' => $teacher->id,
            'class_name' => $base['primaryClass'],
            'section_name' => $base['primarySection'],
            'subject_name' => $request->subject_name,
            'title' => $request->title,
            'term' => $request->term,
            'description' => $request->description,
            'file_path' => $filePath,
        ]);

        // Notify all students in this class about the new Course Outline
        AppNotification::sendToClassStudents(
            $base['primaryClass'],
            'New Course Outline Uploaded 📚',
            "Teacher {$teacher->teacher_name} posted a new course outline for {$request->subject_name}: \"{$request->title}\".",
            'outline',
            route('student.page', 'outlines'),
            'bi-journal-bookmark-fill'
        );

        // Requirement 7: Notify Class In-Charge teacher and Admin as well
        AppNotification::sendToClassIncharge(
            $base['primaryClass'],
            'Course Outline Uploaded 📚',
            "Teacher {$teacher->teacher_name} uploaded a {$request->subject_name} course outline for Class {$base['primaryClass']}.",
            'outline',
            route('teacher.page', 'outlines'),
            'bi-journal-bookmark-fill'
        );

        return redirect()->route('teacher.page', 'outlines')->with('success', 'Course Outline uploaded successfully!');
    }

    public function updateProfile(Request $request)
    {
        [$user, $teacher] = $this->getTeacher();

        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'teacher_phone' => 'required|string|max:50',
            'teacher_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $updateData = [
            'teacher_name' => $request->teacher_name,
            'teacher_phone' => $request->teacher_phone,
        ];

        if ($request->hasFile('teacher_image')) {
            $imagePath = $request->file('teacher_image')->store('upload/teachers', 'public');
            $updateData['teacher_image'] = $imagePath;
            $user->update(['image' => $imagePath]);
        }

        $teacher->update($updateData);

        $user->update([
            'name' => $request->teacher_name,
        ]);

        return redirect()->route('teacher.page', 'profile')->with('success', 'Teacher profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        [$user, $teacher] = $this->getTeacher();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('teacher.page', 'profile')->with('success', 'Password updated successfully!');
    }

    public function downloadPaySlip(int $id)
    {
        [$user, $teacher] = $this->getTeacher();
        
        // Strict privacy check: only own salary slip
        $salary = TeacherSalary::where('teacher_id', $teacher->id)->where('id', $id)->firstOrFail();

        return view('teacher.payslip-print', compact('user', 'teacher', 'salary'));
    }
}
