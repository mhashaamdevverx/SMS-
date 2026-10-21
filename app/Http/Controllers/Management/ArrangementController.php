<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Arrangement;
use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ArrangementController extends Controller
{
    private function classOptions()
    {
        return Classes::query()
            ->whereNotNull('class_no')
            ->whereNotNull('class_name')
            ->whereNotNull('section_name')
            ->orderBy('class_no')
            ->get(['id', 'class_no', 'class_name', 'section_name', 'chairs']);
    }

    private function keyFor(string $className, string $sectionName): string
    {
        return mb_strtolower(trim($className)).'|'.mb_strtolower(trim($sectionName));
    }

    private function manualRules(?int $ignoreId, string $room, ?int $classId): array
    {
        return [
            'chairs' => [
                'required', 'integer', 'min:1', 'max:1000',
                Rule::unique('arrangements', 'chairs')
                    ->where(fn ($query) => $query->where('room', $room))
                    ->ignore($ignoreId),
            ],
            'room' => ['required', 'string', 'max:50'],
            'roll_no' => [
                'required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-]+$/',
                Rule::unique('arrangements', 'roll_no')
                    ->where(fn ($query) => $query->where('class_id', $classId))
                    ->ignore($ignoreId),
            ],
            'class_id' => ['required', Rule::exists('classes', 'id')],
        ];
    }

    public function list()
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        $currentUser = User::find(session('user_id'));
        $arrangements = Arrangement::with('classRecord')
            ->orderByRaw("CASE WHEN room REGEXP '^[0-9]+$' THEN CAST(room AS UNSIGNED) ELSE 999999 END")
            ->orderBy('room')
            ->orderBy('chairs')
            ->paginate(10)
            ->withQueryString();

        $rollNos = $arrangements->pluck('roll_no')->filter()->map(fn ($r) => (int) $r)->unique();
        $studentsMap = Student::whereIn('std_id', $rollNos)->get(['std_id', 'name'])->keyBy('std_id');

        $classOptions = $this->classOptions();

        return view('arrangements', compact('arrangements', 'classOptions', 'currentUser', 'studentsMap'));
    }

    public function create()
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        $currentUser = User::find(session('user_id'));
        $classOptions = $this->classOptions();

        return view('add-arrangement', compact('classOptions', 'currentUser'));
    }

    public function store(Request $request)
    {
        $classOptions = $this->classOptions();
        $availableClassIds = $classOptions->pluck('id')->map(fn ($id) => (int) $id)->all();

        $request->merge([
            'class_ids' => collect($request->input('class_ids', []))
                ->filter(fn ($classId) => filter_var($classId, FILTER_VALIDATE_INT) !== false)
                ->map(fn ($classId) => (int) $classId)
                ->unique()
                ->values()
                ->all(),
        ]);

        $validation = Validator::make($request->all(), [
            'class_ids' => ['required', 'array', 'min:1'],
            'class_ids.*' => ['required', 'integer', Rule::in($availableClassIds)],
        ], [
            'class_ids.required' => 'Please select at least one class-section.',
            'class_ids.*.in' => 'Please select class-sections from the configured list.',
        ]);

        if ($validation->fails()) {
            return redirect('add-arrangement')->withErrors($validation)->withInput();
        }

        $selectedClasses = $classOptions
            ->whereIn('id', $request->class_ids)
            ->values();

        if ($selectedClasses->isEmpty()) {
            return redirect('add-arrangement')->withErrors([
                'class_ids' => 'No selected class-section setup was found. Add a class, section and chairs first.',
            ])->withInput();
        }
        if ($selectedClasses->contains(fn ($class) => (int) $class->chairs < 1)) {
            return redirect('add-arrangement')->withErrors([
                'class_ids' => 'Every selected class-section must have at least one chair.',
            ])->withInput();
        }

        $classesByPair = $selectedClasses->keyBy(fn ($class) => $this->keyFor($class->class_name, $class->section_name));
        $students = Student::query()
            ->whereIn('class', $selectedClasses->pluck('class_name')->unique())
            ->whereIn('section', $selectedClasses->pluck('section_name')->unique())
            ->orderBy('std_id')
            ->get()
            ->filter(fn ($student) => $classesByPair->has($this->keyFor($student->class, $student->section)))
            ->values();

        if ($students->isEmpty()) {
            return redirect('add-arrangement')->withErrors([
                'class_ids' => 'No students were found for the selected class and section combination.',
            ])->withInput();
        }

        $totalChairs = $selectedClasses->sum(fn ($class) => (int) $class->chairs);
        if ($students->count() > $totalChairs) {
            return redirect('add-arrangement')->withErrors([
                'class_ids' => "{$students->count()} students were found, but the selected Class Nos have only {$totalChairs} chairs.",
            ])->withInput();
        }

        $rooms = $selectedClasses->pluck('class_no')->map(fn ($classNo) => (string) $classNo);
        if (Arrangement::query()->whereIn('room', $rooms)->exists()) {
            return redirect('add-arrangement')->withErrors([
                'class_ids' => 'One or more selected Class No rooms already have an arrangement. Delete those seats before generating again.',
            ])->withInput();
        }

        // Build queues per class-section, then take one student from each queue
        // in turn. This prevents adjacent seats from being only one class where
        // more than one selected group still has students.
        $studentsByPair = $students
            ->groupBy(fn ($student) => $this->keyFor($student->class, $student->section))
            ->map(fn ($group) => $group->sortBy('std_id')->values());
        $queuePositions = $selectedClasses->mapWithKeys(fn ($class) => [$class->id => 0])->all();
        $mixedStudents = collect();

        while (true) {
            $addedStudent = false;

            foreach ($selectedClasses as $class) {
                $queue = $studentsByPair->get($this->keyFor($class->class_name, $class->section_name), collect());
                $position = $queuePositions[$class->id];

                if ($queue->has($position)) {
                    $mixedStudents->push([
                        'student' => $queue->get($position),
                        'source_class' => $class,
                    ]);
                    $queuePositions[$class->id]++;
                    $addedStudent = true;
                }
            }

            if (! $addedStudent) {
                break;
            }
        }

        $rows = [];
        $studentPosition = 0;
        $now = now();

        foreach ($selectedClasses as $roomClass) {
            for ($chair = 1; $chair <= (int) $roomClass->chairs; $chair++) {
                if (! isset($mixedStudents[$studentPosition])) {
                    break 2;
                }

                $seat = $mixedStudents[$studentPosition++];
                $rows[] = [
                    'chairs' => $chair,
                    'room' => (string) $roomClass->class_no,
                    'roll_no' => (string) $seat['student']->std_id,
                    'class_id' => $seat['source_class']->id,
                    'user_id' => session('user_id') ?? auth()->id() ?? User::where('role_id', 1)->value('id') ?? 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::transaction(fn () => Arrangement::insert($rows));

        // Dispatch notifications to students, class incharges, and teachers
        try {
            foreach ($rows as $row) {
                $studentStdId = $row['roll_no'];
                $studentObj = $students->firstWhere('std_id', $studentStdId);
                if ($studentObj && !empty($studentObj->login_user_id)) {
                    AppNotification::sendToUser(
                        $studentObj->login_user_id,
                        'Exam Seating Arrangement Ready',
                        "Your exam desk seating plan is confirmed. Room #{$row['room']}, Seat/Chair #{$row['chairs']}.",
                        'exam',
                        '/student/roll-slip',
                        'bi-diagram-3-fill'
                    );
                }
            }

            foreach ($selectedClasses as $cls) {
                AppNotification::sendToClassIncharge(
                    $cls->class_name,
                    "Exam Seating Arrangements Generated",
                    "Classroom seating plan generated for Class {$cls->class_name} ({$cls->section_name}) in Room #{$cls->class_no}.",
                    'exam',
                    '/teacher/classes',
                    'bi-diagram-3-fill',
                    null,
                    $cls->section_name
                );

                $teacherUserIds = AppNotification::resolveTeachersForClassSubject($cls->class_name, $cls->section_name);
                foreach ($teacherUserIds as $tuid) {
                    AppNotification::sendToUser(
                        $tuid,
                        "Exam Seating Plan: Class {$cls->class_name}",
                        "Seating distribution plan generated for Class {$cls->class_name} ({$cls->section_name}) in Room #{$cls->class_no}.",
                        'exam',
                        '/teacher/classes',
                        'bi-diagram-3-fill'
                    );
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Arrangement notification dispatch failed: ' . $e->getMessage());
        }

        return redirect('/arrangements')->with('success', count($rows).' seats generated with alternating class-section students.');
    }

    public function update(Request $request, Arrangement $arrangement)
    {
        $request->merge([
            'room' => trim((string) $request->room),
            'roll_no' => trim((string) $request->roll_no),
        ]);

        $validation = Validator::make(
            $request->all(),
            $this->manualRules($arrangement->id, $request->room, $request->integer('class_id'))
        );

        if ($validation->fails()) {
            return redirect('/arrangements')
                ->withErrors($validation)
                ->withInput()
                ->with('edit_modal', 'arrangement-'.$arrangement->id);
        }

        $arrangement->update($validation->validated());

        return redirect('/arrangements')->with('success', 'Arrangement updated successfully!');
    }

    public function destroy(Arrangement $arrangement)
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        $arrangement->delete();

        return redirect('/arrangements')->with('success', 'Arrangement deleted successfully!');
    }
}