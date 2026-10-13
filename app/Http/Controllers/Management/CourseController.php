<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    // Show all courses
    public function list()
    {
        $courses = Course::orderByDesc('id')->paginate(10)->withQueryString();

        return view('courses', [
            'courses' => $courses
        ]);
    }


    // Show Add Course form
    public function addCourseForm()
    {
        return view('add-course');
    }


    // Store Course
    public function addCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'course_code' => [
                'required',
                'string',
                'regex:/^[A-Z]{3}-[0-9]{3}$/',
                Rule::unique('courses', 'course_code'),
            ],

            'course_name' => [
                'required',
                'string',
                'max:100'
            ],

            'course_description' => [
                'required',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {

                    $wordCount = str_word_count(strip_tags($value));

                    if ($wordCount > 100) {
                        $fail('Course description must not exceed 100 words.');
                    }
                },
            ],

        ], [

            'course_code.required' => 'Course code is required.',

            'course_code.regex' =>
                'Course code must be in this format: LAR-101.',

            'course_code.unique' =>
                'This course code already exists.',

            'course_name.required' =>
                'Course name is required.',

            'course_description.required' =>
                'Course description is required.',

        ]);

        if ($validator->fails()) {
            return redirect('/add-course')->withErrors($validator)->withInput();
        }


        $course = new Course();

        $course->course_code = $request->course_code;
        $course->course_name = $request->course_name;
        $course->course_description = $request->course_description;
        $course->user_id = session('user_id');

        $course->save();


        return redirect('/courses')
            ->with('success', 'Course added successfully!');
    }

    public function updateCourse(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'course_code' => [
                'required', 'string', 'regex:/^[A-Z]{3}-[0-9]{3}$/',
                Rule::unique('courses', 'course_code')->ignore($course->id),
            ],
            'course_name' => ['required', 'string', 'max:100'],
            'course_description' => [
                'required', 'string', 'max:1000',
                function ($attribute, $value, $fail) {
                    if (str_word_count(strip_tags($value)) > 100) {
                        $fail('Course description must not exceed 100 words.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect('/courses')->withErrors($validator)->withInput()->with('edit_modal', 'course-'.$course->id);
        }

        $course->update($request->only('course_code', 'course_name', 'course_description'));

        return redirect('/courses')->with('success', 'Course updated successfully!');
    }

    public function deleteCourse(Course $course)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }
        
        $course->delete();

        return redirect('/courses')->with('success', 'Course deleted successfully!');
    }
}
