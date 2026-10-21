<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    private function classesList()
    {
        $presetOrder = [
            'pre-kg' => 1, 'kg' => 2, 'one' => 3, 'two' => 4, 'three' => 5,
            'four' => 6, 'five' => 7, 'six' => 8, 'seven' => 9, 'eight' => 10,
            'nine' => 11, 'ten' => 12,
        ];

        return Classes::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->get()
            ->sortBy(function ($class) use ($presetOrder) {
                $order = $presetOrder[strtolower(trim($class->class_name))] ?? 999;
                return sprintf('%04d-%s-%s', $order, strtolower($class->class_name), strtolower($class->section_name ?? ''));
            })
            ->values();
    }

    public function list(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        $classes = $this->classesList();
        $selectedClassId = $request->query('class_id');
        $search = trim((string) $request->query('search', ''));

        $query = Subject::query()->with('classRecord');

        if (filled($selectedClassId)) {
            $query->where('class_id', $selectedClassId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('class_name', 'like', "%{$search}%")
                  ->orWhere('section_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(class_name, ' (', section_name, ')') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(class_name, ' ', section_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $subjects = $query->orderBy('subject_name')->paginate(10)->withQueryString();

        return view('subjects', compact('subjects', 'classes', 'selectedClassId', 'search'));
    }

    public function addSubjectForm(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        $classes = $this->classesList();
        $selectedClassId = $request->query('class_id');

        return view('add-subject', compact('classes', 'selectedClassId'));
    }

    public function addSubject(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_name' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1|max:100000',
        ], [
            'class_id.required' => 'Please select a class and section.',
            'class_id.exists' => 'The selected class is invalid.',
        ]);

        $classRecord = Classes::find($validated['class_id']);

        Subject::create([
            'class_id' => $classRecord->id,
            'class_name' => $classRecord->class_name,
            'section_name' => $classRecord->section_name,
            'subject_name' => trim($validated['subject_name']),
            'total_marks' => $validated['total_marks'],
            'user_id' => session('user_id') ?? auth()->id() ?? \App\Models\User::where('role_id', 1)->value('id') ?? 1,
        ]);

        return redirect()->route('subjects.list', ['class_id' => $classRecord->id])
            ->with('success', 'Subject added successfully!');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'subject_name' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1|max:100000',
        ], [
            'class_id.required' => 'Please select a class and section.',
            'class_id.exists' => 'The selected class is invalid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('edit_modal', 'subject-' . $subject->id);
        }

        $validated = $validator->validated();
        $classRecord = Classes::find($validated['class_id']);

        $subject->update([
            'class_id' => $classRecord->id,
            'class_name' => $classRecord->class_name,
            'section_name' => $classRecord->section_name,
            'subject_name' => trim($validated['subject_name']),
            'total_marks' => $validated['total_marks'],
        ]);

        $redirectParams = [];
        if ($request->filled('filter_class_id')) {
            $redirectParams['class_id'] = $request->input('filter_class_id');
        }
        if ($request->filled('filter_search')) {
            $redirectParams['search'] = $request->input('filter_search');
        }

        return redirect()->route('subjects.list', $redirectParams)
            ->with('success', 'Subject updated successfully!');
    }

    public function deleteSubject(Request $request, Subject $subject)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        $subject->delete();

        $redirectParams = [];
        if ($request->filled('filter_class_id')) {
            $redirectParams['class_id'] = $request->input('filter_class_id');
        }
        if ($request->filled('filter_search')) {
            $redirectParams['search'] = $request->input('filter_search');
        }

        return redirect()->route('subjects.list', $redirectParams)
            ->with('success', 'Subject deleted successfully!');
    }
}