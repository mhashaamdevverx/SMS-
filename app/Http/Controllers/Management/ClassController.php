<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Classes;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    private function sections()
    {
        return Section::query()
            ->whereNotNull('section_name')
            ->where('section_name', '!=', '')
            ->orderBy('section_name')
            ->pluck('section_name')
            ->map(fn ($value) => trim($value))
            ->unique(fn ($value) => mb_strtolower($value))
            ->values();
    }

    /**
     * Sections assigned to every class; normalized keys make matching case-insensitive.
     */
    private function assignedSectionsByClass(): array
    {
        return Classes::query()
            ->get(['class_name', 'section_name'])
            ->filter(fn ($class) => filled($class->class_name) && filled($class->section_name))
            ->groupBy(fn ($class) => mb_strtolower(trim($class->class_name)))
            ->map(fn ($classes) => $classes
                ->pluck('section_name')
                ->map(fn ($section) => trim($section))
                ->unique(fn ($section) => mb_strtolower($section))
                ->values()
                ->all())
            ->all();
    }

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

    private function nextClassNumber(): int
    {
        return ((int) Classes::query()->max('class_no')) + 1;
    }
    private function rules(?int $ignoreId = null, ?string $className = null): array
    {
        $sections = $this->sections()->all();

        return [
            'class_name' => ['required', 'string', 'min:1', 'max:50'],
            'chairs' => ['required', 'integer', 'min:1', 'max:10000'],
            'section_name' => [
                'required',
                Rule::in($sections),
                Rule::unique('classes', 'section_name')
                    ->where(fn ($query) => $query->where('class_name', $className))
                    ->ignore($ignoreId),
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'chairs.required' => 'Please enter the number of chairs.',
            'chairs.integer' => 'Chairs must be a whole number.',
            'chairs.min' => 'At least one chair is required.',
            'section_name.required' => 'Please select a section.',
            'section_name.in' => 'Please select a valid section from the list.',
            'section_name.unique' => 'This class and section already exist.',
        ];
    }

    public function list()
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        $currentUser = User::find(session('user_id'));
        $classes = Classes::orderBy('class_no')->paginate(10)->withQueryString();
        $sections = $this->sections();
        $classOptions = $this->classOptions();
        $assignedSectionsByClass = $this->assignedSectionsByClass();

        return view('classes', compact('classes', 'sections', 'classOptions', 'assignedSectionsByClass', 'currentUser'));
    }

    public function create()
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        $currentUser = User::find(session('user_id'));
        $sections = $this->sections();
        $classOptions = $this->classOptions();
        $assignedSectionsByClass = $this->assignedSectionsByClass();
        $nextClassNo = $this->nextClassNumber();

        return view('add-class', compact('sections', 'classOptions', 'assignedSectionsByClass', 'currentUser', 'nextClassNo'));
    }

    public function store(Request $request)
    {
        $className = trim((string) $request->class_name);
        $sectionName = trim((string) $request->section_name);
        $request->merge(['class_name' => $className, 'section_name' => $sectionName]);

        $validation = Validator::make($request->all(), $this->rules(null, $className), $this->messages());

        if ($validation->fails()) {
            return redirect('add-class')->withErrors($validation)->withInput();
        }

        Classes::create([
            'class_no' => $this->nextClassNumber(),
            'class_name' => $className,
            'chairs' => $request->chairs,
            'section_name' => $sectionName,
            'user_id' => session('user_id') ?? auth()->id() ?? User::where('role_id', 1)->value('id') ?? 1,
        ]);

        return redirect('/classes')->with('success', 'Class added successfully!');
    }

    public function update(Request $request, Classes $class)
    {
        $className = trim((string) $request->class_name);
        $sectionName = trim((string) $request->section_name);
        $request->merge(['class_name' => $className, 'section_name' => $sectionName]);

        $validation = Validator::make($request->all(), $this->rules($class->id, $className), $this->messages());

        if ($validation->fails()) {
            return redirect('/classes')->withErrors($validation)->withInput()->with('edit_modal', 'class-'.$class->id);
        }

        $class->update([
            'class_name' => $className,
            'chairs' => $request->chairs,
            'section_name' => $sectionName,
        ]);

        return redirect('/classes')->with('success', 'Class updated successfully!');
    }

    public function destroy(Classes $class)
    {
        if (! session()->has('user_id')) {
            return redirect('login');
        }

        $class->delete();

        return redirect('/classes')->with('success', 'Class deleted successfully!');
    }
}