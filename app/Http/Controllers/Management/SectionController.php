<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function list()
    {
        $sections = Section::all();

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
            'section_name' => ['required', 'string', 'max:100'],
        ]);

        Section::create($validated + ['user_id' => session('user_id')]);

        return redirect('/sections')->with('success', 'Section added successfully!');
    }

    public function updateSection(Request $request, Section $section)
    {
        $validator = validator($request->all(), [
            'section_name' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return redirect('/sections')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_modal', 'section-'.$section->id);
        }

        $section->update($validator->validated());

        return redirect('/sections')->with('success', 'Section updated successfully!');
    }

    public function deleteSection(Section $section)
    {
        $section->delete();

        return redirect('/sections')->with('success', 'Section deleted successfully!');
    }
}