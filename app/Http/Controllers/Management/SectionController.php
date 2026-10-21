<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function list()
    {
        $sections = Section::orderBy('id', 'asc')->paginate(10)->withQueryString();

        return view('sections', [
            'sections' => $sections,
        ]);
    }

    public function addSectionForm()
    {
        return view('add-section');
    }

    public function addSection(Request $request)
    {
        $validated = $request->validate([
            'section_name' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\s\-]+$/', 'unique:sections,section_name'],
        ], [
            'section_name.required' => 'The section name is required.',
            'section_name.unique' => 'A section with this name already exists.',
            'section_name.regex' => 'The section name may only contain letters, numbers, spaces, and hyphens.',
        ]);

        Section::create([
            'section_name' => trim($validated['section_name']),
            'user_id' => session('user_id') ?? auth()->id() ?? \App\Models\User::where('role_id', 1)->value('id') ?? 1,
        ]);

        return redirect('/sections')->with('success', 'Section added successfully!');
    }

    public function updateSection(Request $request, Section $section)
    {
        $validator = Validator::make($request->all(), [
            'section_name' => [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\s\-]+$/',
                Rule::unique('sections', 'section_name')->ignore($section->id),
            ],
        ], [
            'section_name.required' => 'The section name is required.',
            'section_name.unique' => 'A section with this name already exists.',
            'section_name.regex' => 'The section name may only contain letters, numbers, spaces, and hyphens.',
        ]);

        if ($validator->fails()) {
            return redirect('/sections')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_modal', 'section-'.$section->id);
        }

        $section->update([
            'section_name' => trim($request->input('section_name')),
        ]);

        return redirect('/sections')->with('success', 'Section updated successfully!');
    }

    public function deleteSection(Section $section)
    {
        $section->delete();

        return redirect('/sections')->with('success', 'Section deleted successfully!');
    }
}