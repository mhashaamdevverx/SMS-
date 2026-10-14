<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Student;
use App\Models\Role;
use App\Models\User;
use App\Models\RollSlip;
use App\Models\Result;
use App\Models\Arrangement;
use App\Models\Subject;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\InchargeAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    private function classOptions()
    {
        $presetClasses = ['Pre-KG', 'KG', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN'];

        return collect($presetClasses)
            ->merge(Classes::query()->pluck('class_name'))
            ->filter()
            ->map(fn ($className) => trim((string) $className))
            ->unique(fn ($className) => strtolower($className))
            ->values();
    }

    private function sectionOptions()
    {
        return Section::query()
            ->whereNotNull('section_name')
            ->where('section_name', '!=', '')
            ->orderBy('section_name')
            ->pluck('section_name')
            ->map(fn ($sectionName) => trim((string) $sectionName))
            ->filter()
            ->unique(fn ($sectionName) => strtolower($sectionName))
            ->values();
    }
    private function classFeeData($classes)
    {
        $assignment = InchargeAccess::assignment();
        $feeStructuresMap = $assignment ? collect() : \App\Models\FeeStructure::all()->keyBy(fn ($fs) => strtolower(trim($fs->class_name)));
        $classFeeData = [];
        foreach ($assignment ? [] : $classes as $cName) {
            $fs = $feeStructuresMap->get(strtolower(trim($cName)));
            $classFeeData[$cName] = [
                'monthly_fee'      => $fs ? (float) $fs->tuition_fee : 3500,
                'admission_fee'    => $fs ? (float) $fs->admission_fee : 5000,
                'test_session_fee' => $fs ? (float) $fs->test_session_fee : 500,
                'exam_fee'         => $fs ? (float) $fs->exam_fee : 500,
                'lab_sports_fee'   => $fs ? (float) $fs->lab_sports_fee : 300,
                'other_charges'    => $fs ? (float) $fs->other_charges : 200,
                'total_fee'        => $fs ? (float) $fs->total_fee : 4500,
            ];
        }
        return $classFeeData;
    }

    function list()
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        $user = User::with('role')->find(session('user_id'));
        if ($user && in_array($user->role?->slug, ['teacher', 'class-incharge'], true)) {
            return redirect()->route('teacher.dashboard');
        }

        $search = trim((string) request('search', ''));
        $selectedClass = trim((string) request('class', ''));
        $selectedSection = trim((string) request('section', ''));

        $assignment = InchargeAccess::assignment();
        $ownStudent = InchargeAccess::studentRecord();
        if ($assignment) {
            $selectedClass = $assignment->classRecord->class_name;
            $selectedSection = $assignment->classRecord->section_name;
        } elseif ($ownStudent) {
            $selectedClass = $ownStudent->class;
            $selectedSection = $ownStudent->section;
        }

        $classesQuery = Student::query()
            ->whereNotNull('class')
            ->where('class', '!=', '')
            ;
        InchargeAccess::constrainStudents($classesQuery);
        $classes = $classesQuery
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        $normalizedSearch = strtolower(preg_replace('/\s+/', ' ', $search));
        $typedClass = $classes->sortByDesc(function ($class) {
            return strlen($class);
        })->first(function ($class) use ($normalizedSearch) {
            $normalizedClass = strtolower(trim($class));

            return $normalizedSearch === $normalizedClass
                || str_ends_with($normalizedSearch, ' '.$normalizedClass);
        });

        $nameSearch = $search;
        if ($typedClass) {
            $nameSearch = trim(substr($search, 0, strlen($search) - strlen($typedClass)));
        }

        // PAGINATION: har page par 10 students
        $studentsQuery = Student::query();
        InchargeAccess::constrainStudents($studentsQuery);
        $students = $studentsQuery
            ->when($nameSearch !== '', function ($query) use ($nameSearch) {
                $query->where('name', 'like', '%'.$nameSearch.'%');
            })
            ->when($typedClass, function ($query) use ($typedClass) {
                $query->where('class', $typedClass);
            })
            ->when($selectedClass !== '', function ($query) use ($selectedClass) {
                $query->where('class', $selectedClass);
            })
            ->when($selectedSection !== '', function ($query) use ($selectedSection) {
                $query->where('section', $selectedSection);
            })
            ->orderByRaw("CASE UPPER(TRIM(`class`))
                WHEN 'PRE-KG' THEN 1
                WHEN 'KG' THEN 2
                WHEN 'ONE' THEN 3
                WHEN 'TWO' THEN 4
                WHEN 'THREE' THEN 5
                WHEN 'FOUR' THEN 6
                WHEN 'FIVE' THEN 7
                WHEN 'SIX' THEN 8
                WHEN 'SEVEN' THEN 9
                WHEN 'EIGHT' THEN 10
                WHEN 'NINE' THEN 11
                WHEN 'TEN' THEN 12
                ELSE 999
            END")
            ->orderBy('class')
            ->orderByRaw("CASE LOWER(TRIM(section)) WHEN 'red' THEN 1 WHEN 'blue' THEN 2 WHEN 'pink' THEN 3 WHEN 'green' THEN 4 ELSE 999 END")
            ->orderBy('section')
            ->orderBy('std_id')
            ->orderBy('name')
            ->paginate(10)          // pehle get() tha
            ->withQueryString();    // search/class page change par bhi barqarar rahein

        $subjectsQuery = Subject::query()->orderBy('subject_name');
        if ($assignment) {
            $subjectsQuery->whereRaw('LOWER(TRIM(class_name)) = ?', [mb_strtolower(trim($assignment->classRecord->class_name))])
                ->where(function ($query) use ($assignment) {
                    $query->whereRaw('LOWER(TRIM(section_name)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))])
                        ->orWhereNull('section_name')
                        ->orWhere('section_name', '');
                });
        }
        $subjects = $subjectsQuery->pluck('subject_name')
            ->unique()
            ->values();

        $allClasses = $this->classOptions();
        if ($assignment) {
            $allClasses = collect([$assignment->classRecord->class_name]);
        } elseif ($ownStudent) {
            $allClasses = collect([$ownStudent->class]);
        }
        $viewer = User::with('role.permissions')->find(session('user_id'));
        $classFeeData = $viewer->hasPermission('fees.view') ? $this->classFeeData($allClasses) : [];
        $sections = $assignment
            ? collect([$assignment->classRecord->section_name])
            : ($ownStudent ? collect([$ownStudent->section]) : $this->sectionOptions());

        return view('students', compact('students', 'subjects', 'classes', 'allClasses', 'sections', 'search', 'selectedClass', 'selectedSection', 'classFeeData'));
    }


    function addStudentForm()
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        $presetOrder = [
            'pre-kg' => 1, 'kg' => 2, 'one' => 3, 'two' => 4, 'three' => 5,
            'four' => 6, 'five' => 7, 'six' => 8, 'seven' => 9, 'eight' => 10,
            'nine' => 11, 'ten' => 12
        ];

        // Fetch registered classes with their section names
        $assignment = InchargeAccess::assignment();
        $registeredClassesQuery = Classes::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '');
        if ($assignment) {
            $registeredClassesQuery->whereKey($assignment->class_id);
        }
        $registeredClasses = $registeredClassesQuery->get();

        $classSectionsMap = [];
        foreach ($registeredClasses as $cr) {
            $cName = trim((string) $cr->class_name);
            $sName = trim((string) $cr->section_name);
            if (!isset($classSectionsMap[$cName])) {
                $classSectionsMap[$cName] = [];
            }
            if ($sName !== '' && !in_array($sName, $classSectionsMap[$cName])) {
                $classSectionsMap[$cName][] = $sName;
            }
        }

        $allSections = $assignment
            ? collect([$assignment->classRecord->section_name])
            : $this->sectionOptions();

        $classes = ($assignment ? collect([$assignment->classRecord->class_name]) : $this->classOptions())->sortBy(function ($className) use ($presetOrder) {
            return $presetOrder[strtolower(trim($className))] ?? 999;
        })->values();

        foreach ($classes as $cName) {
            if (empty($classSectionsMap[$cName])) {
                $classSectionsMap[$cName] = $allSections->toArray();
            }
        }

        // Per-class Roll No metadata
        $classRollData = [];
        foreach ($classes as $className) {
            $rollQuery = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($className))]);
            if ($assignment) {
                $rollQuery->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($assignment->classRecord->section_name))]);
            }
            $count = (clone $rollQuery)->count();
            $maxRoll = (clone $rollQuery)->max('std_id');

            $nextRoll = max(($count + 1), ($maxRoll !== null ? ((int)$maxRoll + 1) : 1));
            $isFirst = ($count === 0);

            $cleanClass = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $className));
            $formattedRoll = str_pad($nextRoll, 3, '0', STR_PAD_LEFT);
            $regNo = "REG-" . date('Y') . "-" . ($cleanClass ?: 'STD') . "-" . $formattedRoll;

            $classRollData[$className] = [
                'count' => $count,
                'max_roll_no' => $maxRoll,
                'next_roll_no' => $nextRoll,
                'is_first_student' => $isFirst,
                'registration_no' => $regNo,
            ];
        }

        // Per-class Fee metadata
        $feeStructuresMap = $assignment ? collect() : \App\Models\FeeStructure::all()->keyBy(fn ($fs) => strtolower(trim($fs->class_name)));
        $classFeeData = [];
        if (!$assignment) {
            foreach ($classes as $cName) {
                $fs = $feeStructuresMap->get(strtolower(trim($cName)));
                $classFeeData[$cName] = [
                    'monthly_fee'      => $fs ? (float) $fs->tuition_fee : 3500,
                    'admission_fee'    => $fs ? (float) $fs->admission_fee : 5000,
                    'test_session_fee' => $fs ? (float) $fs->test_session_fee : 500,
                    'total_fee'        => $fs ? (float) $fs->total_fee : 4500,
                ];
            }
        }

        $sections = $allSections;

        return view('add-student', compact('classes', 'sections', 'classSectionsMap', 'classRollData', 'classFeeData'));
    }

    public function getRollInfo(Request $request)
    {
        $className = trim((string) $request->query('class', ''));
        $assignment = InchargeAccess::assignment();
        if ($assignment) {
            InchargeAccess::assertClassSection($className, $assignment->classRecord->section_name);
        }

        if ($className === '') {
            return response()->json([
                'count' => 0,
                'next_roll_no' => 1,
                'is_first_student' => true,
                'registration_no' => 'REG-' . date('Y') . '-STD-001',
            ]);
        }

        $rollQuery = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower($className)]);
        InchargeAccess::constrainStudents($rollQuery);
        $count = (clone $rollQuery)->count();
        $maxRoll = (clone $rollQuery)->max('std_id');

        $nextRoll = max(($count + 1), ($maxRoll !== null ? ((int)$maxRoll + 1) : 1));
        $isFirst = ($count === 0);

        $cleanClass = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $className));
        $formattedRoll = str_pad($nextRoll, 3, '0', STR_PAD_LEFT);
        $regNo = "REG-" . date('Y') . "-" . ($cleanClass ?: 'STD') . "-" . $formattedRoll;

        return response()->json([
            'count' => $count,
            'max_roll_no' => $maxRoll,
            'next_roll_no' => $nextRoll,
            'is_first_student' => $isFirst,
            'registration_no' => $regNo,
        ]);
    }

    function addStudent(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|min:2|max:40',
            'last_name' => 'required|string|min:2|max:40',
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i', 'max:100', Rule::unique('Students', 'email'), Rule::unique('users', 'email')],
            'phone_no' => 'required|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'class' => 'required|string|max:50',
            'section' => 'required|string|max:50',
            'std_id' => 'required|integer|min:1',
            'registration_no' => 'nullable|string|max:60',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $validation = Validator::make($request->all(), $rules, [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);

        if ($validation->fails()) {
            return redirect('add-student')
                ->withErrors($validation)
                ->withInput();
        }

        $className = trim((string) $request->class);
        $sectionName = trim((string) $request->section);
        InchargeAccess::assertClassSection($className, $sectionName);
        $stdId = (int) $request->std_id;

        // Auto-increment roll no if duplicate exists in the same class
        $existsInClass = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower($className)])
            ->where('std_id', $stdId)
            ->exists();

        if ($existsInClass) {
            $maxRoll = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower($className)])->max('std_id');
            $stdId = $maxRoll ? ((int)$maxRoll + 1) : $stdId;
        }

        // Generate Registration No if empty
        $regNo = trim((string) $request->registration_no);
        if (empty($regNo)) {
            $cleanClass = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $className));
            $formattedRoll = str_pad($stdId, 3, '0', STR_PAD_LEFT);
            $regNo = "REG-" . date('Y') . "-" . ($cleanClass ?: 'STD') . "-" . $formattedRoll;
        }

        // Image upload (OPTIONAL)
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('upload', 'public');
        }

        $fullName = trim($request->first_name . ' ' . $request->last_name);

        $student = new Student();
        $student->first_name = $request->first_name;
        $student->last_name = $request->last_name;
        $student->name = $fullName;
        $student->std_id = $stdId;
        $student->registration_no = $regNo;
        $emailBase = Str::slug($fullName, '.');
        $emailBase = $emailBase !== '' ? $emailBase : 'student';
        $emailSuffix = 0;
        do {
            $emailSuffix++;
            $candidateEmail = $emailBase.($emailSuffix === 1 ? '' : '.'.$stdId.($emailSuffix > 2 ? '.'.($emailSuffix - 1) : '')).'@gmail.com';
        } while (Student::where('email', $candidateEmail)->exists() || User::where('email', $candidateEmail)->exists());
        $student->email = $request->filled('email') ? $request->email : $candidateEmail;
        $student->phone_no = $request->phone_no;
        $student->emergency_phone = $request->emergency_phone;
        $student->class = $className;
        $student->section = $sectionName;
        $student->image = $path;
        $student->user_id = session('user_id');

        $fsFee = \App\Models\FeeStructure::whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($className)])->first();
        $maySetFees = !InchargeAccess::isIncharge();
        $student->monthly_fee = $maySetFees && $request->filled('monthly_fee') ? (float) $request->monthly_fee : ($fsFee ? (float) $fsFee->tuition_fee : 3500);
        $student->admission_fee = $maySetFees && $request->filled('admission_fee') ? (float) $request->admission_fee : ($fsFee ? (float) $fsFee->admission_fee : 5000);
        $student->test_session_fee = $maySetFees && $request->filled('test_session_fee') ? (float) $request->test_session_fee : ($fsFee ? (float) $fsFee->test_session_fee : 500);

        // Auto-assign subjects based on class and section
        $classSubjects = Subject::query()
            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($className)])
            ->where(function ($q) use ($sectionName) {
                $q->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower($sectionName)])
                  ->orWhereNull('section_name')
                  ->orWhere('section_name', '');
            })
            ->orderBy('subject_name')
            ->pluck('subject_name');

        if ($classSubjects->isEmpty()) {
            $classSubjects = Subject::query()
                ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($className)])
                ->orderBy('subject_name')
                ->pluck('subject_name');
        }

        if ($classSubjects->isNotEmpty()) {
            $student->subject = $classSubjects->unique()->implode(', ');
        }

        if ($student->save()) {
            $temporaryPassword = 'Student@123';
            $studentUser = User::create([
                'username' => $student->name,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone_no,
                'image' => $student->image ?: 'dummy.jpeg',
                'password' => Hash::make($temporaryPassword),
                'role_id' => Role::where('slug', 'student')->value('id'),
                'must_change_password' => true,
                'password_changed' => false,
            ]);
            $student->update(['login_user_id' => $studentUser->id]);

            return redirect('list')
                ->with('success', 'Student and sign-in account created.')
                ->with('issued_credentials', ['email' => $student->email, 'password' => $temporaryPassword]);
        } else {
            return redirect('add-student')->with('error', 'Student could not be added.');
        }
    }

    public function show(Student $student)
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        InchargeAccess::assertStudent($student);
        $student->load('loginAccount');

        $assignedSubjects = collect(explode(',', (string) $student->subject))
            ->map(fn ($subject) => trim($subject))
            ->filter()
            ->unique(fn ($subject) => strtolower($subject))
            ->values();

        $assignment = InchargeAccess::assignment();
        $allSubjectsQuery = Subject::query()
            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($student->class))]);
        if ($assignment) {
            $allSubjectsQuery->where(function ($query) use ($assignment) {
                $query->whereRaw('LOWER(TRIM(section_name)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))])
                    ->orWhereNull('section_name')
                    ->orWhere('section_name', '');
            });
        }
        $allSubjects = $allSubjectsQuery->get();

        $resultRows = Result::query()
            ->where(function ($query) use ($student) {
                $query->whereRaw('LOWER(TRIM(student_name)) = ?', [strtolower(trim($student->name))])
                    ->whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower(trim($student->class))]);
                if (! empty($student->section)) {
                    $query->where(function ($secQuery) use ($student) {
                        $secQuery->whereRaw('LOWER(TRIM(student_section)) = ?', [strtolower(trim($student->section))])
                            ->orWhereNull('student_section')
                            ->orWhere('student_section', '');
                    });
                }
            })
            ->orderBy('subject')
            ->get();

        // Overall results summary metrics
        $resultsBySubject = $resultRows->keyBy(fn ($r) => strtolower(trim($r->subject)));
        $totalMarks = (float) $resultRows->sum('total_marks');
        $obtainedMarks = (float) $resultRows->sum('obtained_marks');
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : null;
        $grade = $percentage === null ? null : match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 75 => 'B+',
            $percentage >= 70 => 'B',
            $percentage >= 65 => 'C+',
            $percentage >= 60 => 'C',
            $percentage >= 55 => 'D+',
            $percentage >= 50 => 'D',
            default => 'F',
        };

        // 3 Standard Terms defined
        $standardTerms = ['First Term', 'Mid Term', 'Final Term'];

        // Group results by term
        $termResults = [];
        foreach ($standardTerms as $tName) {
            $tSlug = \Illuminate\Support\Str::slug($tName);

            // Filter result rows for this term
            $tRows = $resultRows->filter(function ($row) use ($tName) {
                $rowTerm = strtolower(trim((string) $row->term));
                if (empty($rowTerm)) {
                    return strtolower($tName) === 'first term'; // default unassigned results to First Term
                }
                if ($rowTerm === strtolower($tName)) {
                    return true;
                }
                if (str_contains($rowTerm, 'first') && strtolower($tName) === 'first term') return true;
                if (str_contains($rowTerm, 'mid') && strtolower($tName) === 'mid term') return true;
                if ((str_contains($rowTerm, 'final') || str_contains($rowTerm, 'annual')) && strtolower($tName) === 'final term') return true;
                return false;
            })->values();

            $tResultsBySubject = $tRows->keyBy(fn ($r) => strtolower(trim($r->subject)));
            $tTotalMarks = (float) $tRows->sum('total_marks');
            $tObtainedMarks = (float) $tRows->sum('obtained_marks');
            $tPercentage = $tTotalMarks > 0 ? round(($tObtainedMarks / $tTotalMarks) * 100, 2) : null;
            $tGrade = $tPercentage === null ? null : match (true) {
                $tPercentage >= 90 => 'A+',
                $tPercentage >= 80 => 'A',
                $tPercentage >= 75 => 'B+',
                $tPercentage >= 70 => 'B',
                $tPercentage >= 65 => 'C+',
                $tPercentage >= 60 => 'C',
                $tPercentage >= 55 => 'D+',
                $tPercentage >= 50 => 'D',
                default => 'F',
            };

            $termResults[$tName] = [
                'name' => $tName,
                'slug' => $tSlug,
                'rows' => $tRows,
                'resultsBySubject' => $tResultsBySubject,
                'totalMarks' => $tTotalMarks,
                'obtainedMarks' => $tObtainedMarks,
                'percentage' => $tPercentage,
                'grade' => $tGrade,
                'hasData' => $tRows->isNotEmpty(),
            ];
        }

        // Fetch Roll Slips for Student
        $rollSlips = RollSlip::with('exam')
            ->where(function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->orWhere(function ($q2) use ($student) {
                        $q2->whereRaw('LOWER(TRIM(student_name)) = ?', [strtolower(trim($student->name))])
                            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($student->class))]);
                    });
            })
            ->get();

        // Fetch Arrangement for Student
        $arrangement = Arrangement::query()
            ->whereIn('id', $rollSlips->pluck('arrangement_id')->filter())
            ->orWhere('roll_no', (string) $student->std_id)
            ->first();

        // Group Roll Slips by Term
        $termRollSlips = [];
        foreach ($standardTerms as $tName) {
            $tSlug = \Illuminate\Support\Str::slug($tName);
            $matchedSlip = $rollSlips->first(function ($slip) use ($tName) {
                $examName = strtolower(trim((string) ($slip->exam?->name ?? '')));
                if ($examName === strtolower($tName)) return true;
                if (str_contains($examName, 'first') && strtolower($tName) === 'first term') return true;
                if (str_contains($examName, 'mid') && strtolower($tName) === 'mid term') return true;
                if ((str_contains($examName, 'final') || str_contains($examName, 'annual')) && strtolower($tName) === 'final term') return true;
                return false;
            });

            // If no term-specific slip found, and we have unassigned slips, fallback for First Term
            if (!$matchedSlip && strtolower($tName) === 'first term' && $rollSlips->isNotEmpty()) {
                $matchedSlip = $rollSlips->first();
            }

            $termRollSlips[$tName] = [
                'name' => $tName,
                'slug' => $tSlug,
                'slip' => $matchedSlip,
                'hasData' => !empty($matchedSlip),
            ];
        }

        // Fetch Attendance Summary for Student
        $studentAttendances = \App\Models\Attendance::query()
            ->where('student_id', $student->id)
            ->orderByDesc('date')
            ->get();

        // Filter out weekends (Saturdays & Sundays) from total working count
        $workingAttendances = $studentAttendances->filter(function ($att) {
            $dt = \Carbon\Carbon::parse($att->date);
            return !$dt->isSaturday() && !$dt->isSunday();
        });

        $totalPresentCount = $workingAttendances->where('status', 'present')->count();
        $totalAbsentCount = $workingAttendances->where('status', 'absent')->count();
        $totalLeaveCount = $workingAttendances->where('status', 'leave')->count();
        $totalWorkingDays = $workingAttendances->count();
        $attendancePercentage = $totalWorkingDays > 0 ? round(($totalPresentCount / $totalWorkingDays) * 100, 1) : null;

        $recentAttendances = $studentAttendances->take(15);

        $currentUser = \App\Models\User::find(session('user_id'));
        $classFeeStructure = \App\Models\FeeStructure::whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($student->class))])->first();

        return view('student-profile', compact(
            'student',
            'assignedSubjects',
            'allSubjects',
            'resultRows',
            'resultsBySubject',
            'totalMarks',
            'obtainedMarks',
            'percentage',
            'grade',
            'arrangement',
            'rollSlips',
            'standardTerms',
            'termResults',
            'termRollSlips',
            'studentAttendances',
            'totalPresentCount',
            'totalAbsentCount',
            'totalLeaveCount',
            'totalWorkingDays',
            'attendancePercentage',
            'recentAttendances',
            'currentUser',
            'classFeeStructure'
        ));
    }

    public function downloadResults(Student $student)
    {
        InchargeAccess::assertStudent($student);

        $results = Result::query()
            ->whereRaw('LOWER(TRIM(student_name)) = ?', [mb_strtolower(trim($student->name))])
            ->whereRaw('LOWER(TRIM(student_class)) = ?', [mb_strtolower(trim($student->class))])
            ->whereRaw('LOWER(TRIM(student_section)) = ?', [mb_strtolower(trim($student->section))])
            ->orderBy('term')
            ->orderBy('subject')
            ->get();

        $pdf = Pdf::loadView('student-results-pdf', compact('student', 'results'))->setPaper('a4', 'portrait');

        return $pdf->download('results-'.Str::slug($student->name).'.pdf');
    }

    public function updateStudent(Request $request, Student $student)
    {
        InchargeAccess::assertStudent($student);
        $assignment = InchargeAccess::assignment();
        $validation = Validator::make($request->all(), [
            'name' => 'required|min:2|max:60',
            'std_id' => 'required|integer|min:1',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
                'max:60',
                Rule::unique('Students', 'email')->ignore($student->id),
                Rule::unique('users', 'email')->ignore($student->login_user_id),
            ],
            'phone_no' => 'required|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'class' => 'required|string|max:50',
            'section' => ['required', Rule::in($assignment ? [$assignment->classRecord->section_name] : $this->sectionOptions()->all())],
            'monthly_fee' => 'nullable|numeric|min:0',
            'test_session_fee' => 'nullable|numeric|min:0',
            'admission_fee' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'subjects' => 'nullable|array|max:20',
            'subjects.*' => 'nullable|string|max:50',
        ], [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);

        if ($validation->fails()) {
            return redirect('/list')
                ->withErrors($validation)
                ->withInput()
                ->with('edit_modal', 'student-'.$student->id);
        }

        InchargeAccess::assertClassSection($request->class, $request->section);

        $originalName = $student->name;
        $student->name = $request->name;
        $student->std_id = $request->std_id;
        $student->email = $request->filled('email') ? $request->email : $student->email;
        $student->phone_no = $request->phone_no;
        $student->emergency_phone = $request->emergency_phone;
        $student->class = $request->class;
        $student->section = $request->section;

        $fsFee = \App\Models\FeeStructure::whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($request->class)])->first();

        if (!InchargeAccess::isIncharge() && $request->filled('monthly_fee')) {
            $student->monthly_fee = (float) $request->monthly_fee;
        } elseif (!InchargeAccess::isIncharge() && $fsFee && (float)$fsFee->tuition_fee > 0) {
            $student->monthly_fee = (float) $fsFee->tuition_fee;
        }

        if (!InchargeAccess::isIncharge() && $request->filled('test_session_fee')) {
            $student->test_session_fee = (float) $request->test_session_fee;
        } elseif (!InchargeAccess::isIncharge() && $fsFee && (float)$fsFee->test_session_fee >= 0) {
            $student->test_session_fee = (float) $fsFee->test_session_fee;
        }

        if (!InchargeAccess::isIncharge() && $request->filled('admission_fee')) {
            $student->admission_fee = (float) $request->admission_fee;
        } elseif (!InchargeAccess::isIncharge() && $fsFee && (float)$fsFee->admission_fee > 0) {
            $student->admission_fee = (float) $fsFee->admission_fee;
        }

        $subjects = collect($request->input('subjects', []))
            ->map(fn ($subject) => trim($subject))
            ->filter()
            ->unique(fn ($subject) => strtolower($subject))
            ->values();
        $student->subject = $subjects->isEmpty() ? null : $subjects->implode(', ');

        if ($request->hasFile('image')) {
            $student->image = $request->file('image')->store('upload', 'public');
        }

        $student->save();

        if ($student->loginAccount) {
            $account = $student->loginAccount;
            $account->update([
                'name' => $student->name,
                'username' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone_no,
                'image' => $student->image ?: $account->image,
            ]);
            if ($account->must_change_password && $originalName !== $student->name) {
                $temporaryPassword = 'Student@123';
                $account->update(['password' => Hash::make($temporaryPassword)]);
            }
        }

        $response = redirect('/list')->with('success', 'Student updated successfully!');
        if (!empty($temporaryPassword)) {
            $response->with('issued_credentials', ['email' => $student->email, 'password' => $temporaryPassword]);
        }

        return $response;
    }

    public function deleteStudent(Student $student)
    {
        InchargeAccess::assertStudent($student);
        $student->loginAccount?->delete();
        $student->delete();

        return redirect('/list')->with('success', 'Student deleted successfully!');
    }

    public function updateStudentSubjects(Request $request, Student $student)
    {
        InchargeAccess::assertStudent($student);

        $selectedSubjects = collect($request->input('subjects', []))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->unique(fn ($s) => strtolower($s))
            ->values();

        $student->subject = $selectedSubjects->isEmpty() ? null : $selectedSubjects->implode(', ');
        $student->save();

        $marksInput = $request->input('marks', []);
        foreach ($selectedSubjects as $subjName) {
            $obtained = isset($marksInput[$subjName]) && $marksInput[$subjName] !== '' ? (float)$marksInput[$subjName] : null;

            if ($obtained !== null) {
                $subjRecord = Subject::whereRaw('LOWER(TRIM(subject_name)) = ?', [strtolower($subjName)])->first();
                $totalMarks = $subjRecord ? (float)$subjRecord->total_marks : 100;

                Result::updateOrCreate(
                    [
                        'student_name' => $student->name,
                        'student_section' => $student->section,
                        'student_class' => $student->class,
                        'subject' => $subjName,
                    ],
                    [
                        'user_id' => session('user_id'),
                        'total_marks' => $totalMarks,
                        'obtained_marks' => $obtained,
                        'percentage' => $totalMarks > 0 ? round(($obtained / $totalMarks) * 100, 2) : 0,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', "Subjects and marks updated successfully for {$student->name}!");
    }
}