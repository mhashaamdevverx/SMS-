<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Support\InchargeAccess;
use App\Models\AppNotification;

class ResultController extends Controller
{
    private function ensureAuthenticated(): void
    {
        if (!session()->has('user_id')) {
            redirect('login')->send();
            exit;
        }
    }

    private function currentUser()
    {
        return User::find(session('user_id'));
    }

    /*
    |--------------------------------------------------------------------------
    | Results List with Term & Class/Section Filter
    |--------------------------------------------------------------------------
    */

    public function list(Request $request)
    {
        $this->ensureAuthenticated();

        $selectedTerm = $request->input('term', '');
        $selectedClass = $request->input('class_section', '');

        $query = Result::query();
        $assignment = InchargeAccess::assignment();
        if ($assignment) {
            $query->whereRaw('LOWER(TRIM(student_class)) = ?', [mb_strtolower(trim($assignment->classRecord->class_name))])
                ->whereRaw('LOWER(TRIM(student_section)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))]);
        } elseif ($student = InchargeAccess::studentRecord()) {
            $query->whereRaw('LOWER(TRIM(student_name)) = ?', [mb_strtolower(trim($student->name))])
                ->whereRaw('LOWER(TRIM(student_class)) = ?', [mb_strtolower(trim($student->class))])
                ->whereRaw('LOWER(TRIM(student_section)) = ?', [mb_strtolower(trim($student->section))]);
        }

        if (!empty($selectedTerm)) {
            $query->where('term', $selectedTerm);
        }

        if (!empty($selectedClass)) {
            $rawInput = trim($selectedClass);
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawInput, $matches)) {
                $cName = trim($matches[1]);
                $sName = trim($matches[2]);
                $query->whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower($cName)])
                      ->whereRaw('LOWER(TRIM(student_section)) = ?', [strtolower($sName)]);
            } else {
                $query->whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower($rawInput)]);
            }
        }

        $results = $query->latest()->get();

        $results->each(function ($result) {
            if (!$result->grade) {
                $result->grade = $this->grade((float) $result->percentage);
            }
        });

        $studentResults = $results
            ->groupBy(fn ($result) => implode('|', [
                $result->student_name,
                $result->student_section,
                $result->student_class,
                $result->term ?? 'First Term',
            ]))
            ->map(function ($group) {
                $first         = $group->first();
                $totalMarks    = (float) $group->sum('total_marks');
                $obtainedMarks = (float) $group->sum('obtained_marks');

                $percentage = $totalMarks > 0
                    ? $this->percentage($obtainedMarks, $totalMarks)
                    : 0;

                return [
                    'student_name'    => $first->student_name,
                    'student_section' => $first->student_section,
                    'student_class'   => $first->student_class,
                    'term'            => $first->term ?? 'First Term',
                    'results'         => $group->sortBy('subject')->values(),
                    'total_marks'     => $totalMarks,
                    'obtained_marks'  => $obtainedMarks,
                    'percentage'      => $percentage,
                    'grade'           => $this->grade($percentage),
                ];
            })
            ->values();

        $presetOrder = [
            'pre-kg' => 1, 'kg' => 2, 'one' => 3, 'two' => 4, 'three' => 5,
            'four' => 6, 'five' => 7, 'six' => 8, 'seven' => 9, 'eight' => 10,
            'nine' => 11, 'ten' => 12
        ];

        $assignment = InchargeAccess::assignment();
        $registeredClasses = Classes::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->when($assignment, fn ($query) => $query->whereKey($assignment->class_id))
            ->get()
            ->sortBy(function ($class) use ($presetOrder) {
                $order = $presetOrder[strtolower(trim($class->class_name))] ?? 999;
                return sprintf('%04d-%s-%s', $order, strtolower($class->class_name), strtolower($class->section_name ?? ''));
            })
            ->values();

        $modelOptions = $registeredClasses->map(fn ($c) => $c->class_label);

        $studentClassesQuery = Student::query()
            ->select('class', 'section')
            ->whereNotNull('class')
            ->where('class', '!=', '');
        InchargeAccess::constrainStudents($studentClassesQuery);
        $studentClasses = $studentClassesQuery->get()
            ->map(function ($s) {
                return $s->section ? "{$s->class} ({$s->section})" : $s->class;
            });

        $classOptions = $studentClasses->concat($modelOptions)->unique()->values();
        $availableTerms = ['First Term', 'Mid Term', 'Final Term'];

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentPageItems = $studentResults->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $studentResults = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $studentResults->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('results', compact('studentResults', 'classOptions', 'availableTerms', 'selectedTerm', 'selectedClass'))
            ->with('currentUser', $this->currentUser());
    }


    /*
    |--------------------------------------------------------------------------
    | Add Result - Class Selection Page
    |--------------------------------------------------------------------------
    */

    public function addResultForm()
    {
        $this->ensureAuthenticated();

        $presetOrder = [
            'pre-kg' => 1, 'kg' => 2, 'one' => 3, 'two' => 4, 'three' => 5,
            'four' => 6, 'five' => 7, 'six' => 8, 'seven' => 9, 'eight' => 10,
            'nine' => 11, 'ten' => 12
        ];

        $registeredClasses = Classes::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->get()
            ->sortBy(function ($class) use ($presetOrder) {
                $order = $presetOrder[strtolower(trim($class->class_name))] ?? 999;
                return sprintf('%04d-%s-%s', $order, strtolower($class->class_name), strtolower($class->section_name ?? ''));
            })
            ->values();

        $modelOptions = $registeredClasses->map(fn ($c) => $c->class_label);

        $studentClasses = Student::query()
            ->select('class', 'section')
            ->whereNotNull('class')
            ->where('class', '!=', '')
            ->get()
            ->map(function ($s) {
                return $s->section ? "{$s->class} ({$s->section})" : $s->class;
            });

        $classOptions = $studentClasses->concat($modelOptions)->unique()->values();

        $studentsQuery = Student::orderBy('name');
        InchargeAccess::constrainStudents($studentsQuery);
        $students = $studentsQuery->get(['id', 'name', 'section', 'class', 'subject']);

        $availableTerms = ['First Term', 'Mid Term', 'Final Term'];

        return view('add-result', compact('students', 'classOptions', 'availableTerms'))
            ->with('currentUser', $this->currentUser());
    }


    /*
    |--------------------------------------------------------------------------
    | Show All Students Of Selected Class & Term
    |--------------------------------------------------------------------------
    */

    public function classResults(Request $request)
    {
        $this->ensureAuthenticated();

        $request->validate([
            'class' => ['required', 'string', 'max:100'],
            'term'  => ['nullable', 'string', 'max:50'],
        ]);

        $rawInput = trim($request->class);
        $selectedTerm = $request->input('term', 'First Term');
        if (empty($selectedTerm)) {
            $selectedTerm = 'First Term';
        }

        $className = $rawInput;
        $sectionName = null;

        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawInput, $matches)) {
            $className = trim($matches[1]);
            $sectionName = trim($matches[2]);
        }

        InchargeAccess::assertClassSection($className, $sectionName);

        $selectedClass = $rawInput;

        $subjectTotals = Subject::query()
            ->get(['subject_name', 'total_marks'])
            ->mapWithKeys(fn ($subject) => [
                strtolower(trim($subject->subject_name)) => (float) $subject->total_marks,
            ]);

        $studentQuery = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower($className)]);
        if ($sectionName !== null && $sectionName !== '') {
            $studentQuery->whereRaw('LOWER(TRIM(section)) = ?', [strtolower($sectionName)]);
        }
        InchargeAccess::constrainStudents($studentQuery);

        $students = $studentQuery->orderBy('name')
            ->get(['id', 'std_id', 'name', 'section', 'class', 'subject']);

        if ($students->isEmpty() && $className !== '') {
            $fallbackStudentQuery = Student::whereRaw('LOWER(TRIM(class)) = ?', [strtolower($className)])
                ->orderBy('name');
            InchargeAccess::constrainStudents($fallbackStudentQuery);
            $students = $fallbackStudentQuery->get(['id', 'std_id', 'name', 'section', 'class', 'subject']);
        }

        $existingResultsQuery = Result::whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower($className)])
            ->where('term', $selectedTerm);
        if ($sectionName !== null && $sectionName !== '') {
            $existingResultsQuery->whereRaw('LOWER(TRIM(student_section)) = ?', [strtolower($sectionName)]);
        }
        $existingResults = $existingResultsQuery->get()
            ->groupBy(fn ($result) =>
                strtolower(trim((string) $result->student_name)) . '|' .
                strtolower(trim((string) $result->student_section)) . '|' .
                strtolower(trim((string) $result->subject)));

        $students->each(function ($student) use ($subjectTotals, $existingResults) {
            $student->assigned_subjects = $this
                ->parseSubjects($student->subject)
                ->map(function ($subjectName) use ($student, $subjectTotals, $existingResults) {
                    $key = strtolower(trim((string) $student->name)) . '|' .
                           strtolower(trim((string) $student->section)) . '|' .
                           strtolower(trim($subjectName));

                    $existing = $existingResults->get($key)?->sortByDesc('id')->first();

                    return (object) [
                        'name'           => $subjectName,
                        'total_marks'    => $subjectTotals->get(strtolower(trim($subjectName)), 0),
                        'obtained_marks' => $existing ? $existing->obtained_marks : null,
                        'result_id'      => $existing ? $existing->id : null,
                    ];
                })
                ->values();
        });

        $classSubjectsList = Subject::query()
            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($className)])
            ->when($sectionName !== null && $sectionName !== '', function ($query) use ($sectionName) {
                $query->where(function ($sectionQuery) use ($sectionName) {
                    $sectionQuery->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower($sectionName)])
                        ->orWhereNull('section_name')
                        ->orWhere('section_name', '');
                });
            })
            ->orderBy('subject_name')
            ->get(['id', 'subject_name', 'total_marks']);

        if ($classSubjectsList->isEmpty()) {
            $classSubjectsList = Subject::query()
                ->orderBy('subject_name')
                ->get(['id', 'subject_name', 'total_marks'])
                ->unique('subject_name')
                ->values();
        }

        $students->each(function ($student) use ($classSubjectsList, $existingResults) {
            $assignedNames = $this->parseSubjects($student->subject)->map(fn ($s) => trim($s));
            $subjectsMap = collect();

            foreach ($classSubjectsList as $sub) {
                $subjectsMap->put(strtolower(trim($sub->subject_name)), (object) [
                    'subject_name' => $sub->subject_name,
                    'total_marks'  => (float) $sub->total_marks,
                ]);
            }

            foreach ($assignedNames as $name) {
                if (blank($name)) continue;
                $key = strtolower(trim($name));
                if (!$subjectsMap->has($key)) {
                    $subjectsMap->put($key, (object) [
                        'subject_name' => $name,
                        'total_marks'  => 100,
                    ]);
                }
            }

            $student->class_subjects = $subjectsMap->map(function ($subObj) use ($student, $existingResults) {
                $key = strtolower(trim((string) $student->name)) . '|' .
                       strtolower(trim((string) $student->section)) . '|' .
                       strtolower(trim($subObj->subject_name));

                $existing = $existingResults->get($key)?->sortByDesc('id')->first();

                return (object) [
                    'subject_name'   => $subObj->subject_name,
                    'total_marks'    => $subObj->total_marks,
                    'obtained_marks' => $existing ? $existing->obtained_marks : null,
                    'result_id'      => $existing ? $existing->id : null,
                ];
            })->values();
        });

        $allClassSubjects = $classSubjectsList;
        $availableTerms = ['First Term', 'Mid Term', 'Final Term'];

        return view('class-results', compact('students', 'selectedClass', 'allClassSubjects', 'selectedTerm', 'availableTerms'))
            ->with('currentUser', $this->currentUser());
    }


    /*
    |--------------------------------------------------------------------------
    | Save Single Student Result from Modal
    |--------------------------------------------------------------------------
    */

    public function storeSingleStudentResult(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'student_id'    => ['required', 'integer'],
            'student_class' => ['required', 'string', 'max:100'],
            'term'          => ['nullable', 'string', 'max:50'],
            'marks'         => ['nullable', 'array'],
            'marks.*'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $termVal = $validated['term'] ?? 'First Term';
        if (empty($termVal)) $termVal = 'First Term';

        $student = Student::findOrFail($validated['student_id']);
        InchargeAccess::assertStudent($student);

        $subjectRecords = Subject::all()->keyBy(
            fn ($subject) => strtolower(trim($subject->subject_name))
        );
        $studentSubjects = $this->parseSubjects($student->subject)
            ->map(fn ($subjectName) => strtolower(trim($subjectName)))
            ->all();

        DB::transaction(function () use ($student, $validated, $subjectRecords, $studentSubjects, $termVal) {
            $marksList = $validated['marks'] ?? [];

            foreach ($marksList as $subjectName => $obtainedVal) {
                if ($obtainedVal === null || $obtainedVal === '') {
                    continue;
                }

                $obtainedMarks = (float) $obtainedVal;
                $subjectKey = strtolower(trim($subjectName));
                if (InchargeAccess::isIncharge() && !in_array($subjectKey, $studentSubjects, true)) {
                    throw ValidationException::withMessages([
                        'marks' => "{$subjectName} is not assigned to {$student->name}.",
                    ]);
                }

                $subject = $subjectRecords->get($subjectKey);

                $totalMarks = $subject ? (float) $subject->total_marks : 100;
                if ($totalMarks <= 0) $totalMarks = 100;

                if ($obtainedMarks > $totalMarks) {
                    throw ValidationException::withMessages([
                        'marks' => "Obtained marks for {$subjectName} cannot exceed total marks ({$totalMarks}).",
                    ]);
                }

                $percentage = $this->percentage($obtainedMarks, $totalMarks);

                $result = Result::firstOrNew([
                    'student_name'    => $student->name,
                    'student_section' => $student->section ?? '',
                    'student_class'   => $student->class,
                    'subject'         => $subjectName,
                    'term'            => $termVal,
                ]);

                if (!$result->exists) {
                    $result->user_id = session('user_id');
                }

                $result->term           = $termVal;
                $result->total_marks    = $totalMarks;
                $result->obtained_marks = $obtainedMarks;
                $result->percentage     = $percentage;
                $result->grade          = $this->grade($percentage);
                $result->save();
            }
        });

        // Requirement 5: Send notification to affected student
        $sUserIds = [];
        if ($student->login_user_id) $sUserIds[] = $student->login_user_id;
        if ($student->user_id && !in_array($student->user_id, [1, 2])) $sUserIds[] = $student->user_id;
        if ($student->email) {
            $u = User::where('email', $student->email)->first();
            if ($u) $sUserIds[] = $u->id;
        }
        if ($student->registration_no) {
            $u = User::where('username', $student->registration_no)->first();
            if ($u) $sUserIds[] = $u->id;
        }
        if ($student->name) {
            $uNames = User::where('name', $student->name)->pluck('id')->toArray();
            $sUserIds = array_merge($sUserIds, $uNames);
        }
        if (empty($sUserIds)) $sUserIds[] = $student->user_id ?: 1;

        foreach (array_unique(array_filter($sUserIds)) as $sUserId) {
            AppNotification::sendToUser(
                $sUserId,
                'Result Updated 🏆',
                "Your result for {$termVal} has been updated.",
                'result',
                route('student.page', 'results'),
                'bi-award-fill'
            );
        }

        return redirect()
            ->back()
            ->with('success', "Result for {$student->name} ({$termVal}) saved successfully!");
    }


    /*
    |--------------------------------------------------------------------------
    | Save / Update Complete Class Results
    |--------------------------------------------------------------------------
    */

    public function storeClassResults(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'student_class'                       => ['required', 'string', 'max:100'],
            'term'                                => ['nullable', 'string', 'max:50'],
            'results'                             => ['required', 'array', 'min:1'],
            'results.*.student_id'                => ['required', 'integer'],
            'results.*.section'                   => ['nullable', 'string', 'max:100'],
            'results.*.subjects'                  => ['required', 'array', 'min:1'],
            'results.*.subjects.*.subject'        => ['required', 'string', 'max:255'],
            'results.*.subjects.*.obtained_marks' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $termVal = $validated['term'] ?? 'First Term';
        if (empty($termVal)) $termVal = 'First Term';

        $subjectRecords = Subject::all()->keyBy(
            fn ($subject) => strtolower(trim($subject->subject_name))
        );

        DB::transaction(function () use ($validated, $subjectRecords, $termVal) {

            foreach ($validated['results'] as $studentRow) {

                $student = Student::findOrFail($studentRow['student_id']);
                InchargeAccess::assertStudent($student);

                if (
                    strtolower(trim((string) $student->class)) !==
                    strtolower(trim($validated['student_class']))
                ) {
                    throw ValidationException::withMessages([
                        'results' => "Invalid student/class selection for {$student->name}.",
                    ]);
                }

                if (blank($student->section)) {
                    throw ValidationException::withMessages([
                        'results' => "Please add section for {$student->name} before saving results.",
                    ]);
                }

                $assigned = $this->parseSubjects($student->subject)
                    ->map(fn ($name) => strtolower(trim($name)))
                    ->all();

                foreach ($studentRow['subjects'] as $row) {

                    $subjectName = trim($row['subject']);
                    $subjectKey  = strtolower($subjectName);

                    if (!in_array($subjectKey, $assigned, true)) {
                        throw ValidationException::withMessages([
                            'results' => "{$subjectName} is not assigned to {$student->name}.",
                        ]);
                    }

                    $subject = $subjectRecords->get($subjectKey);

                    if (!$subject) {
                        throw ValidationException::withMessages([
                            'results' => "Subject {$subjectName} could not be found in the Subjects section.",
                        ]);
                    }

                    $totalMarks = (float) $subject->total_marks;

                    if ($totalMarks <= 0) {
                        throw ValidationException::withMessages([
                            'results' => "Total marks for {$subjectName} must be greater than zero.",
                        ]);
                    }

                    $obtainedMarks = (float) $row['obtained_marks'];

                    if ($obtainedMarks > $totalMarks) {
                        throw ValidationException::withMessages([
                            'results' => "Obtained marks for {$student->name} in {$subjectName} cannot exceed total marks ({$totalMarks}).",
                        ]);
                    }

                    $percentage = $this->percentage($obtainedMarks, $totalMarks);

                    $result = Result::firstOrNew([
                        'student_name'    => $student->name,
                        'student_section' => $student->section,
                        'student_class'   => $student->class,
                        'subject'         => $subjectName,
                        'term'            => $termVal,
                    ]);

                    if (!$result->exists) {
                        $result->user_id = session('user_id');
                    }

                    $result->term           = $termVal;
                    $result->total_marks    = $totalMarks;
                    $result->obtained_marks = $obtainedMarks;
                    $result->percentage     = $percentage;
                    $result->grade          = $this->grade($percentage);
                    $result->save();
                }
            }
        });

        // Requirement 5: Notify affected students for class results bulk save
        $studentIds = array_unique(array_column($validated['results'], 'student_id'));
        foreach ($studentIds as $stId) {
            $st = Student::find($stId);
            if ($st) {
                $sUserIds = [];
                if ($st->login_user_id) $sUserIds[] = $st->login_user_id;
                if ($st->user_id && !in_array($st->user_id, [1, 2])) $sUserIds[] = $st->user_id;
                if ($st->email) {
                    $u = User::where('email', $st->email)->first();
                    if ($u) $sUserIds[] = $u->id;
                }
                if ($st->registration_no) {
                    $u = User::where('username', $st->registration_no)->first();
                    if ($u) $sUserIds[] = $u->id;
                }
                if ($st->name) {
                    $uNames = User::where('name', $st->name)->pluck('id')->toArray();
                    $sUserIds = array_merge($sUserIds, $uNames);
                }
                if (empty($sUserIds)) $sUserIds[] = $st->user_id ?: 1;

                foreach (array_unique(array_filter($sUserIds)) as $sUserId) {
                    AppNotification::sendToUser(
                        $sUserId,
                        'Result Updated 🏆',
                        "Your result for {$termVal} has been updated.",
                        'result',
                        route('student.page', 'results'),
                        'bi-award-fill'
                    );
                }
            }
        }

        return redirect()
            ->route('results.class', ['class' => $validated['student_class'], 'term' => $termVal])
            ->with('success', "Class results for {$validated['student_class']} ({$termVal}) saved successfully!");
    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Update / Delete (Legacy Support)
    |--------------------------------------------------------------------------
    */

    public function bulkUpdate(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'results'                  => ['required', 'array', 'min:1'],
            'results.*.id'             => ['required', 'integer', 'exists:results,id'],
            'results.*.obtained_marks' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $resultsMap = Result::whereIn('id', array_column($validated['results'], 'id'))
            ->get()
            ->keyBy('id');

        DB::transaction(function () use ($validated, $resultsMap) {
            foreach ($validated['results'] as $row) {

                $result = $resultsMap->get($row['id']);
                if (!$result) continue;

                $obtainedMarks = (float) $row['obtained_marks'];
                $totalMarks    = (float) $result->total_marks;

                if ($totalMarks > 0 && $obtainedMarks > $totalMarks) {
                    throw ValidationException::withMessages([
                        'results' => "Obtained marks for {$result->student_name} ({$result->subject}) cannot exceed total marks ({$totalMarks}).",
                    ]);
                }

                $percentage = $totalMarks > 0 ? $this->percentage($obtainedMarks, $totalMarks) : 0;

                $result->obtained_marks = $obtainedMarks;
                $result->percentage     = $percentage;
                $result->grade          = $this->grade($percentage);
                $result->save();
            }
        });

        return redirect('/results')->with('success', 'Results updated successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'result_ids'   => ['required', 'array', 'min:1'],
            'result_ids.*' => ['required', 'integer', 'exists:results,id'],
        ]);

        Result::whereIn('id', $validated['result_ids'])->delete();

        return redirect('/results')->with('success', 'Results deleted successfully!');
    }

    private function parseSubjects($subjectField): Collection
    {
        return collect(explode(',', (string) $subjectField))
            ->map(fn ($s) => trim($s))
            ->filter();
    }

    private function percentage(float $obtained, float $total): float
    {
        return round(($obtained / $total) * 100, 2);
    }

    private function grade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 75 => 'B+',
            $percentage >= 70 => 'B',
            $percentage >= 65 => 'C+',
            $percentage >= 60 => 'C',
            $percentage >= 55 => 'D+',
            $percentage >= 50 => 'D',
            default           => 'F',
        };
    }
}