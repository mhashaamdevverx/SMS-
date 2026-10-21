<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Arrangement;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\RollSlip;
use App\Models\Student;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classes;
use App\Support\InchargeAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\AppNotification;

class RollSlipController extends Controller
{
    private function ensureAuthenticated(): void
    {
        abort_unless(session()->has('user_id'), 403);
    }

    private function classOptions()
    {
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

    private function assignedSubjects(?string $subjectList): array
    {
        return collect(explode(',', (string) $subjectList))
            ->map(fn ($subject) => trim($subject))
            ->filter()
            ->unique(fn ($subject) => mb_strtolower($subject))
            ->values()
            ->all();
    }

    public function list()
    {
        $this->ensureAuthenticated();

        $student = InchargeAccess::studentRecord();
        $assignment = InchargeAccess::assignment();
        $rollSlipsQuery = RollSlip::with('exam');
        if ($student) {
            $rollSlipsQuery->where('student_id', $student->id);
        } elseif ($assignment) {
            $rollSlipsQuery->whereRaw('LOWER(TRIM(class_name)) = ?', [mb_strtolower(trim($assignment->classRecord->class_name))])
                ->whereRaw('LOWER(TRIM(section_name)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))]);
        }
        $rollSlips = $rollSlipsQuery
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
        $currentUser = User::find(session('user_id'));
        $classes = $student ? collect([$student->class]) : $this->classOptions();

        return view('roll-slips', compact('rollSlips', 'currentUser', 'classes'));
    }

    public function create(Request $request)
    {
        $this->ensureAuthenticated();

        // Get unique classes registered in the system
        $classes = Classes::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->orderBy('class_no')
            ->orderBy('class_name')
            ->get()
            ->unique(fn ($c) => strtolower(trim($c->class_name)))
            ->values();

        $selectedClass = trim((string) $request->query('class', ''));
        if ($selectedClass === '' && $classes->isNotEmpty()) {
            $selectedClass = $classes->first()->class_name;
        }

        $subjectsQuery = Subject::query()->orderBy('subject_name');
        if ($selectedClass !== '' && strtolower($selectedClass) !== 'all') {
            $subjectsQuery->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower($selectedClass)]);
        }

        // Dedup by subject_name so a subject never repeats
        $subjects = $subjectsQuery->get(['id', 'subject_name', 'class_name', 'section_name'])
            ->unique(fn ($s) => strtolower(trim($s->subject_name)))
            ->values();

        // All subjects map per class for instant client-side switching without page reload
        $allClassSubjects = Subject::query()
            ->orderBy('subject_name')
            ->get(['id', 'subject_name', 'class_name'])
            ->groupBy(fn ($s) => strtolower(trim((string) $s->class_name)))
            ->map(fn ($group) => $group->unique(fn ($s) => strtolower(trim($s->subject_name)))->values());

        $arrangementCount = Arrangement::count();
        $issuedArrangementIds = RollSlip::query()->whereNotNull('arrangement_id')->pluck('arrangement_id');
        $availableArrangementCount = Arrangement::query()->whereNotIn('id', $issuedArrangementIds)->count();
        $currentUser = User::find(session('user_id'));

        return view('issue-roll-slips', compact(
            'classes',
            'selectedClass',
            'subjects',
            'allClassSubjects',
            'arrangementCount',
            'availableArrangementCount',
            'currentUser'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'class_name' => ['nullable', 'string', 'max:100'],
            'exam_name' => ['required', 'string', 'max:150'],
            'exam_session' => ['nullable', 'string', 'max:100'],
            'schedules' => ['required', 'array', 'min:1'],
            'schedules.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'schedules.*.exam_date' => ['required', 'date'],
        ]);

        if (! Arrangement::query()->exists()) {
            return redirect('issue-roll-slips')->withErrors([
                'exam_name' => 'Generate student arrangements before issuing roll-number slips.',
            ])->withInput();
        }

        $issuedArrangementIds = RollSlip::query()->whereNotNull('arrangement_id')->pluck('arrangement_id');
        $arrangements = Arrangement::query()
            ->whereNotIn('id', $issuedArrangementIds)
            ->orderBy('room')
            ->orderBy('chairs')
            ->get();

        if ($arrangements->isEmpty()) {
            return redirect('issue-roll-slips')->withErrors([
                'exam_name' => 'Roll-number slips have already been issued for all current arrangements. Delete a roll slip to issue it again.',
            ])->withInput();
        }

        $subjects = Subject::query()
            ->whereIn('id', collect($validated['schedules'])->pluck('subject_id'))
            ->get()
            ->keyBy('id');
        $scheduleRows = collect($validated['schedules'])
            ->map(function ($schedule) use ($subjects) {
                $subject = $subjects->get($schedule['subject_id']);

                return [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->subject_name,
                    'exam_date' => $schedule['exam_date'],
                    'start_time' => null,
                    'end_time' => null,
                ];
            })
            ->sortBy('exam_date')
            ->values();

        $selectedClass = trim((string) ($validated['class_name'] ?? ''));

        $studentsQuery = Student::query()
            ->whereIn('std_id', $arrangements->pluck('roll_no')->map(fn ($rollNo) => (int) $rollNo));

        if ($selectedClass !== '' && strtolower($selectedClass) !== 'all') {
            $studentsQuery->whereRaw('LOWER(TRIM(class)) = ?', [strtolower($selectedClass)]);
        }

        $studentsByStdId = $studentsQuery->get()->keyBy('std_id');

        $slipRows = [];
        foreach ($arrangements as $arrangement) {
            $student = $studentsByStdId->get((int) $arrangement->roll_no);
            if (! $student) {
                continue;
            }

            // Every configured paper is printed on every slip as the official datesheet.
            $datesheet = $scheduleRows->values()->all();

            $slipRows[] = [
                'student' => $student,
                'arrangement' => $arrangement,
                'datesheet' => $datesheet,
            ];
        }

        if (empty($slipRows)) {
            $msg = ($selectedClass !== '' && strtolower($selectedClass) !== 'all')
                ? "No unissued student desk arrangement found for Class '{$selectedClass}'. Make sure students of this class have arrangements generated."
                : 'No arranged student could be matched with a Std. ID.';

            return redirect('issue-roll-slips')->withErrors([
                'exam_name' => $msg,
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $scheduleRows, $slipRows) {
            $exam = Exam::create([
                'name' => $validated['exam_name'],
                'session' => $validated['exam_session'] ?? null,
                'user_id' => session('user_id') ?? auth()->id() ?? \App\Models\User::where('role_id', 1)->value('id') ?? 1,
            ]);

            $exam->schedules()->createMany($scheduleRows->all());

            foreach ($slipRows as $row) {
                RollSlip::create([
                    'exam_id' => $exam->id,
                    'student_id' => $row['student']->id,
                    'arrangement_id' => $row['arrangement']->id,
                    'student_name' => $row['student']->name,
                    'roll_no' => $row['student']->std_id,
                    'class_name' => $row['student']->class,
                    'section_name' => $row['student']->section,
                    'room_no' => $row['arrangement']->room,
                    'chair_no' => $row['arrangement']->chairs,
                    'datesheet' => $row['datesheet'],
                    'issued_by' => session('user_id') ?? auth()->id() ?? \App\Models\User::where('role_id', 1)->value('id') ?? 1,
                ]);
            }
        });

        // Requirement 8: Notify each affected student, In-Charge Teacher, and Admin about Examination & Roll Slip
        $notifiedClasses = [];
        foreach ($slipRows as $row) {
            $student = $row['student'];
            if ($student) {
                $cKey = strtolower(trim($student->class)) . '|' . strtolower(trim($student->section ?? ''));
                $notifiedClasses[$cKey] = ['class' => $student->class, 'section' => $student->section];

                $sUserIds = [];
                if ($student->login_user_id) $sUserIds[] = $student->login_user_id;
                if ($student->user_id && !in_array($student->user_id, [1, 2])) $sUserIds[] = $student->user_id;
                if ($student->email) {
                    $u = User::where('email', $student->email)->pluck('id')->toArray();
                    $sUserIds = array_merge($sUserIds, $u);
                }
                if ($student->registration_no) {
                    $u = User::where('username', $student->registration_no)->pluck('id')->toArray();
                    $sUserIds = array_merge($sUserIds, $u);
                }
                if ($student->name) {
                    $uNames = User::where('name', $student->name)->pluck('id')->toArray();
                    $sUserIds = array_merge($sUserIds, $uNames);
                }
                if (empty($sUserIds)) $sUserIds[] = $student->user_id ?: 1;

                foreach (array_unique(array_filter($sUserIds)) as $sUserId) {
                    AppNotification::sendToUser(
                        $sUserId,
                        'Roll Number Slip Available 🎫',
                        "Your examination roll number slip and date sheet for '{$validated['exam_name']}' is now available.",
                        'roll_slip',
                        route('student.page', 'slips'),
                        'bi-card-heading'
                    );
                }
            }
        }

        // Notify In-Charge Teachers and Admins for each affected class/section
        foreach ($notifiedClasses as $cData) {
            AppNotification::sendToClassIncharge(
                $cData['class'],
                "Examination Roll Slips Issued: {$validated['exam_name']}",
                "Roll slips and date sheet for '{$validated['exam_name']}' issued for Class {$cData['class']}" . ($cData['section'] ? " ({$cData['section']})" : '') . ".",
                'exam',
                route('roll-slips.index'),
                'bi-calendar-event',
                session('user_id'),
                $cData['section']
            );
            AppNotification::sendToAdmins(
                "Examination Roll Slips Issued: {$validated['exam_name']}",
                "Roll slips and date sheet for '{$validated['exam_name']}' issued for Class {$cData['class']}" . ($cData['section'] ? " ({$cData['section']})" : '') . ".",
                'exam',
                route('roll-slips.index'),
                'bi-calendar-event',
                session('user_id')
            );
        }

        return redirect('roll-slips')->with('success', count($slipRows).' roll-number slips issued and stored.');
    }

    public function update(Request $request, RollSlip $rollSlip)
    {
        $this->ensureAuthenticated();

        $validation = Validator::make($request->all(), [
            'student_name' => ['required', 'string', 'max:150'],
            'roll_no' => ['required', 'integer', 'min:1'],
            'class_name' => ['required', 'string', 'max:50'],
            'section_name' => ['nullable', 'string', 'max:100'],
            'room_no' => ['required', 'string', 'max:50'],
            'chair_no' => ['required', 'integer', 'min:1'],
        ]);

        if ($validation->fails()) {
            return redirect()->route('roll-slips.index')
                ->withErrors($validation)
                ->withInput()
                ->with('edit_modal', 'roll-slip-'.$rollSlip->id);
        }

        $rollSlip->update($validation->validated());

        // Notify Student and Incharge about updated slip details
        $student = $rollSlip->student ?: Student::where('std_id', $rollSlip->roll_no)->first();
        if ($student) {
            $sUserIds = [];
            if ($student->login_user_id) $sUserIds[] = $student->login_user_id;
            if ($student->email) {
                $u = User::where('email', $student->email)->pluck('id')->toArray();
                $sUserIds = array_merge($sUserIds, $u);
            }
            foreach (array_unique(array_filter($sUserIds)) as $sUserId) {
                AppNotification::sendToUser(
                    $sUserId,
                    'Roll Slip Details Updated 🎫',
                    "Your roll slip room/seat assignment for Room {$rollSlip->room_no}, Chair #{$rollSlip->chair_no} has been updated.",
                    'roll_slip',
                    route('student.page', 'slips'),
                    'bi-card-heading',
                    session('user_id')
                );
            }
        }

        AppNotification::sendToClassIncharge(
            $rollSlip->class_name,
            'Roll Slip Details Updated 🎫',
            "Roll slip for {$rollSlip->student_name} (Class {$rollSlip->class_name} {$rollSlip->section_name}) was updated.",
            'roll_slip',
            route('roll-slips.index'),
            'bi-card-heading',
            session('user_id'),
            $rollSlip->section_name
        );

        return redirect()->route('roll-slips.index')->with('success', 'Roll-number slip updated successfully.');
    }

    public function destroy(RollSlip $rollSlip)
    {
        $this->ensureAuthenticated();

        $cName = $rollSlip->class_name;
        $sName = $rollSlip->section_name;
        $stName = $rollSlip->student_name;
        $rollNo = $rollSlip->roll_no;

        $student = $rollSlip->student ?: Student::where('std_id', $rollNo)->first();

        $rollSlip->delete();

        if ($student) {
            $sUserIds = [];
            if ($student->login_user_id) $sUserIds[] = $student->login_user_id;
            if ($student->email) {
                $u = User::where('email', $student->email)->pluck('id')->toArray();
                $sUserIds = array_merge($sUserIds, $u);
            }
            foreach (array_unique(array_filter($sUserIds)) as $sUserId) {
                AppNotification::sendToUser(
                    $sUserId,
                    'Roll Slip Cancelled / Removed ⚠️',
                    "Your examination roll slip #{$rollNo} has been cancelled or removed by administration.",
                    'roll_slip',
                    route('student.page', 'slips'),
                    'bi-x-octagon-fill',
                    session('user_id')
                );
            }
        }

        AppNotification::sendToClassIncharge(
            $cName,
            'Roll Slip Removed ⚠️',
            "Roll slip for {$stName} (Class {$cName} {$sName}) was removed.",
            'roll_slip',
            route('roll-slips.index'),
            'bi-trash-fill',
            session('user_id'),
            $sName
        );

        return redirect()->route('roll-slips.index')->with('success', 'Roll-number slip deleted successfully.');
    }
    public function show(RollSlip $rollSlip)
    {
        $this->ensureAuthenticated();
        $this->assertRollSlipAccess($rollSlip);

        return view('roll-slip-document', compact('rollSlip'));
    }

    public function download(RollSlip $rollSlip)
    {
        $this->ensureAuthenticated();
        $this->assertRollSlipAccess($rollSlip);

        $filename = 'roll-slip-'.str($rollSlip->student_name)->slug().'-'.$rollSlip->roll_no.'.pdf';

        $pdf = Pdf::loadView('roll-slip-pdf', [
            'rollSlip' => $rollSlip,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    private function assertRollSlipAccess(RollSlip $rollSlip): void
    {
        $student = InchargeAccess::studentRecord();
        if ($student) {
            abort_unless($rollSlip->student_id === $student->id, 403);

            return;
        }

        $assignment = InchargeAccess::assignment();
        if ($assignment) {
            InchargeAccess::assertClassSection($rollSlip->class_name, $rollSlip->section_name);
        }
    }

    private function getOrCreateClassRollSlips(string $className): array
    {
        $students = Student::query()
            ->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($className))])
            ->orderBy('std_id')
            ->orderBy('name')
            ->get();

        $existingClassSlips = RollSlip::with('exam')
            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($className))])
            ->get();

        if ($students->isEmpty() && $existingClassSlips->isEmpty()) {
            return [collect(), null];
        }

        $exam = Exam::with('schedules')->latest('id')->first();
        if (! $exam) {
            $exam = Exam::create([
                'name' => 'Annual Examination',
                'session' => date('Y').'-'.(date('Y') + 1),
                'user_id' => session('user_id'),
            ]);
        }

        $datesheet = [];
        if ($exam->schedules->isNotEmpty()) {
            $datesheet = $exam->schedules->map(fn ($s) => [
                'subject_id' => $s->subject_id,
                'subject_name' => $s->subject_name,
                'exam_date' => $s->exam_date ? $s->exam_date->format('Y-m-d') : now()->format('Y-m-d'),
                'start_time' => $s->start_time ?? '09:00 AM',
                'end_time' => $s->end_time ?? '12:00 PM',
            ])->values()->all();
        } else {
            $sampleSlip = RollSlip::whereNotNull('datesheet')->latest('id')->first();
            if ($sampleSlip && ! empty($sampleSlip->datesheet) && is_array($sampleSlip->datesheet)) {
                $datesheet = $sampleSlip->datesheet;
            } else {
                $subjects = Subject::query()->orderBy('id')->get();
                if ($subjects->isEmpty()) {
                    $defaultSubjects = ['English', 'Mathematics', 'Urdu', 'Science'];
                    foreach ($defaultSubjects as $idx => $dSub) {
                        $datesheet[] = [
                            'subject_id' => $idx + 1,
                            'subject_name' => $dSub,
                            'exam_date' => now()->addDays(7 + ($idx * 2))->format('Y-m-d'),
                            'start_time' => '09:00 AM',
                            'end_time' => '12:00 PM',
                        ];
                    }
                } else {
                    foreach ($subjects as $idx => $subj) {
                        $datesheet[] = [
                            'subject_id' => $subj->id,
                            'subject_name' => $subj->subject_name,
                            'exam_date' => now()->addDays(7 + ($idx * 2))->format('Y-m-d'),
                            'start_time' => '09:00 AM',
                            'end_time' => '12:00 PM',
                        ];
                    }
                }
            }
        }

        if ($students->isNotEmpty()) {
            DB::transaction(function () use ($students, $exam, $datesheet, $className) {
                foreach ($students as $student) {
                    $hasSlip = RollSlip::where('exam_id', $exam->id)
                        ->where(function ($q) use ($student) {
                            $q->where('student_id', $student->id)
                              ->orWhere('roll_no', (string) $student->std_id);
                        })
                        ->exists();

                    if (! $hasSlip) {
                        $existingInOtherExam = RollSlip::where('student_id', $student->id)
                            ->orWhere('roll_no', (string) $student->std_id)
                            ->first();

                        if ($existingInOtherExam) {
                            continue;
                        }

                        $arrangement = Arrangement::where('roll_no', (string) $student->std_id)->first();
                        if (! $arrangement) {
                            $room = 'Room-'.($student->class ?: 'A');
                            $chair = (int) ($student->std_id ?: 1);

                            $classRecord = Classes::whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim((string) $student->class))])
                                ->when($student->section, fn ($q) => $q->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim((string) $student->section))]))
                                ->first() ?? Classes::whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim((string) $student->class))])->first();

                            $classId = $classRecord?->id ?? Classes::first()?->id ?? 1;

                            $arrangement = Arrangement::create([
                                'class_id' => $classId,
                                'room' => $room,
                                'chairs' => $chair,
                                'roll_no' => (string) $student->std_id,
                                'user_id' => session('user_id'),
                            ]);
                        }

                        RollSlip::create([
                            'exam_id' => $exam->id,
                            'student_id' => $student->id,
                            'arrangement_id' => $arrangement->id,
                            'student_name' => $student->name,
                            'roll_no' => (int) $student->std_id,
                            'class_name' => $student->class,
                            'section_name' => $student->section,
                            'room_no' => $arrangement->room ?? ('Class '.$student->class),
                            'chair_no' => $arrangement->chairs ?? (int) $student->std_id,
                            'datesheet' => $datesheet,
                            'issued_by' => session('user_id'),
                        ]);
                    }
                }
            });
        }

        $rollSlips = RollSlip::with('exam')
            ->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($className))])
            ->orderBy('roll_no')
            ->get();

        if ($rollSlips->isEmpty() && $students->isNotEmpty()) {
            $rollSlips = RollSlip::with('exam')
                ->whereIn('student_id', $students->pluck('id'))
                ->orderBy('roll_no')
                ->get();
        }

        return [$rollSlips, $exam];
    }

    public function classDocument(Request $request)
    {
        $this->ensureAuthenticated();

        $className = trim((string) $request->input('class', ''));
        if ($className === '') {
            return redirect()->route('roll-slips.index')->withErrors([
                'class' => 'Please select a class to generate and view roll slips.',
            ]);
        }

        [$rollSlips, $exam] = $this->getOrCreateClassRollSlips($className);

        if ($rollSlips->isEmpty()) {
            return redirect()->route('roll-slips.index')->withErrors([
                'class' => "No students found in class '{$className}' to generate roll slips.",
            ]);
        }

        return view('class-roll-slips-document', compact('rollSlips', 'className', 'exam'));
    }

    public function classDownload(Request $request)
    {
        $this->ensureAuthenticated();

        $className = trim((string) $request->input('class', ''));
        if ($className === '') {
            return redirect()->route('roll-slips.index')->withErrors([
                'class' => 'Please select a class to download roll slips.',
            ]);
        }

        [$rollSlips, $exam] = $this->getOrCreateClassRollSlips($className);

        if ($rollSlips->isEmpty()) {
            return redirect()->route('roll-slips.index')->withErrors([
                'class' => "No roll slips found for class '{$className}'.",
            ]);
        }

        $filename = 'all-roll-slips-class-'.str($className)->slug().'.pdf';

        $pdf = Pdf::loadView('class-roll-slips-pdf', [
            'rollSlips' => $rollSlips,
            'className' => $className,
            'exam' => $exam,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}