<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Classes;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    private function getSubjectCourseOptions()
    {
        $subjects = Subject::distinct()->pluck('subject_name')->toArray();
        $courses = Course::distinct()->pluck('course_name')->toArray();
        $options = array_values(array_unique(array_filter(array_merge($subjects, $courses))));
        sort($options);
        return $options;
    }

    private function getClassesList()
    {
        return Classes::orderBy('class_no')->get();
    }

    // Show all teachers
    public function list()
    {
        $user = User::with('role')->find(session('user_id'));
        if ($user && in_array($user->role?->slug, ['teacher', 'class-incharge'], true)) {
            return redirect()->route('teacher.dashboard');
        }

        $teachers = Teacher::with(['assignments', 'loginAccount'])->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $subjectCourseOptions = $this->getSubjectCourseOptions();
        $classesList = $this->getClassesList();

        return view('teachers', [
            'teachers' => $teachers,
            'subjectCourseOptions' => $subjectCourseOptions,
            'classesList' => $classesList
        ]);
    }

    // Show Add Teacher form
    public function addTeacherForm()
    {
        $subjectCourseOptions = $this->getSubjectCourseOptions();
        $classesList = $this->getClassesList();

        return view('add-teacher', [
            'subjectCourseOptions' => $subjectCourseOptions,
            'classesList' => $classesList
        ]);
    }

    // Store Teacher
    public function addTeacher(Request $request)
    {
        $request->validate([
            'teacher_name' => ['required', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'teacher_email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i', 'unique:teachers,teacher_email', 'unique:users,email'],
            'teacher_phone' => ['required', 'digits:11', 'regex:/^03[0-9]{9}$/'],
            'course' => ['nullable', 'string', 'max:255'],
            'subject_course_selected' => ['nullable', 'array'],
            'assigned_class' => ['nullable', 'string', 'max:50'],
            'teacher_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'teacher_email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);

        $teacher = new Teacher();
        $teacher->teacher_name = $request->teacher_name;
        $teacher->teacher_email = $request->teacher_email;
        $teacher->teacher_phone = $request->teacher_phone;
        $teacher->user_id = session('user_id', 2);

        if ($request->hasFile('teacher_image')) {
            $teacher->teacher_image = $request->file('teacher_image')->store('teachers', 'public');
        }

        // Selected subjects or courses
        $selectedItems = $request->input('subject_course_selected', []);
        if (empty($selectedItems) && $request->filled('course')) {
            $selectedItems = array_map('trim', explode(',', $request->course));
        }

        $teacher->course = implode(', ', array_unique(array_filter($selectedItems)));
        if (empty($teacher->course)) {
            $teacher->course = $request->input('course', 'N/A');
        }

        $teacher->save();
        $temporaryPassword = 'Teacher@123';
        $teacherUser = User::create([
            'username' => $teacher->teacher_name,
            'name' => $teacher->teacher_name,
            'email' => $teacher->teacher_email,
            'phone' => $teacher->teacher_phone,
            'image' => $teacher->teacher_image ?: 'dummy.jpeg',
            'password' => Hash::make($temporaryPassword),
            'role_id' => Role::where('slug', 'teacher')->value('id'),
            'must_change_password' => true,
        ]);
        $teacher->update(['login_user_id' => $teacherUser->id]);

        // Create assignments
        $assignedClass = $request->input('assigned_class');
        $className = 'N/A';
        $sectionName = null;

        if ($assignedClass) {
            $parts = explode('|', $assignedClass);
            $className = $parts[0] ?? 'N/A';
            $sectionName = $parts[1] ?? null;
        }

        if (!empty($selectedItems)) {
            foreach ($selectedItems as $item) {
                TeacherAssignment::create([
                    'teacher_id' => $teacher->id,
                    'class_name' => $className,
                    'section_name' => $sectionName,
                    'subject_or_course' => $item
                ]);
            }
        } else {
            TeacherAssignment::create([
                'teacher_id' => $teacher->id,
                'class_name' => $className,
                'section_name' => $sectionName,
                'subject_or_course' => $teacher->course
            ]);
        }

        return redirect('/teachers')
            ->with('success', 'Teacher and sign-in account added successfully.')
            ->with('issued_credentials', ['email' => $teacher->teacher_email, 'password' => $temporaryPassword]);
    }

    public function updateTeacher(Request $request, Teacher $teacher)
    {
        $validator = Validator::make($request->all(), [
            'teacher_name' => ['required', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'teacher_email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
                Rule::unique('teachers', 'teacher_email')->ignore($teacher->id),
                Rule::unique('users', 'email')->ignore($teacher->login_user_id),
            ],
            'teacher_phone' => ['required', 'digits:11', 'regex:/^03[0-9]{9}$/'],
            'course' => ['nullable', 'string', 'max:255'],
            'subject_course_selected' => ['nullable', 'array'],
            'assigned_class' => ['nullable', 'string', 'max:50'],
            'teacher_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'teacher_email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);

        if ($validator->fails()) {
            return redirect('/teachers')->withErrors($validator)->withInput()->with('edit_modal', 'teacher-' . $teacher->id);
        }

        $validated = $validator->validated();

        $originalTeacherName = $teacher->teacher_name;
        $teacher->teacher_name = $validated['teacher_name'];
        $teacher->teacher_email = $validated['teacher_email'];
        $teacher->teacher_phone = $validated['teacher_phone'];

        if ($request->hasFile('teacher_image')) {
            $teacher->teacher_image = $request->file('teacher_image')->store('teachers', 'public');
        }

        $selectedItems = $request->input('subject_course_selected', []);
        if (empty($selectedItems) && !empty($validated['course'])) {
            $selectedItems = array_map('trim', explode(',', $validated['course']));
        }

        if (!empty($selectedItems)) {
            $teacher->course = implode(', ', array_unique(array_filter($selectedItems)));
        } elseif (!empty($validated['course'])) {
            $teacher->course = $validated['course'];
        }

        $teacher->save();
        $temporaryPassword = null;
        if ($teacher->loginAccount) {
            $teacher->loginAccount->update([
                'name' => $teacher->teacher_name,
                'username' => $teacher->teacher_name,
                'email' => $teacher->teacher_email,
                'phone' => $teacher->teacher_phone,
                'image' => $teacher->teacher_image ?: $teacher->loginAccount->image,
            ]);
            if ($teacher->loginAccount->must_change_password && $originalTeacherName !== $teacher->teacher_name) {
                $temporaryPassword = 'Teacher@123';
                $teacher->loginAccount->update(['password' => Hash::make($temporaryPassword)]);
            }
        }

        // Update assignments if new selections provided
        if (!empty($selectedItems) || $request->has('assigned_class')) {
            TeacherAssignment::where('teacher_id', $teacher->id)->delete();

            $assignedClass = $request->input('assigned_class');
            $className = 'N/A';
            $sectionName = null;

            if ($assignedClass) {
                $parts = explode('|', $assignedClass);
                $className = $parts[0] ?? 'N/A';
                $sectionName = $parts[1] ?? null;
            }

            if (!empty($selectedItems)) {
                foreach ($selectedItems as $item) {
                    TeacherAssignment::create([
                        'teacher_id' => $teacher->id,
                        'class_name' => $className,
                        'section_name' => $sectionName,
                        'subject_or_course' => $item
                    ]);
                }
            }
        }

        $response = redirect('/teachers')->with('success', 'Teacher updated successfully!');
        if ($temporaryPassword) {
            $response->with('issued_credentials', ['email' => $teacher->teacher_email, 'password' => $temporaryPassword]);
        }

        return $response;
    }

    public function deleteTeacher(Teacher $teacher)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        TeacherAssignment::where('teacher_id', $teacher->id)->delete();
        $teacher->loginAccount?->delete();
        $teacher->delete();

        return redirect('/teachers')->with('success', 'Teacher deleted successfully!');
    }
}