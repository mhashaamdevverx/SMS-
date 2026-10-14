<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function list(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect('login');
        }

        $search = trim((string) $request->input('search', ''));
        $usersQuery = User::with('role')->orderBy('id', 'asc');

        if ($search !== '') {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate(10)->withQueryString();

        return view('users', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
                'max:60',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('Students', 'email')->ignore($user->studentRecord?->id),
                Rule::unique('teachers', 'teacher_email')->ignore($user->teacherRecord?->id),
            ],
            'phone' => 'nullable|string|max:30',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);

        if ($validator->fails()) {
            return redirect('/users')->withErrors($validator)->withInput()->with('edit_modal', 'user-'.$user->id);
        }

        $validated = $validator->validated();

        $originalName = $user->name ?: $user->username;
        $user->name = $validated['name'];
        $user->username = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($request->hasFile('image')) {
            $user->image = $request->file('image')->store('upload/users', 'public');
        }

        $user->save();

        if ($student = $user->studentRecord) {
            $student->name = $user->name;
            $student->email = $user->email;
            $student->phone_no = $user->phone ?: $student->phone_no;
            $student->save();
        }

        if ($teacher = $user->teacherRecord) {
            $teacher->teacher_name = $user->name;
            $teacher->teacher_email = $user->email;
            $teacher->teacher_phone = $user->phone ?: $teacher->teacher_phone;
            $teacher->save();
        }

        $temporaryPassword = null;
        if ($user->must_change_password && $originalName !== $user->name) {
            $temporaryPassword = $user->studentRecord ? 'Student@123' : 'Teacher@123';
            $user->update(['password' => Hash::make($temporaryPassword)]);
        }

        $response = redirect('/users')->with('success', 'User updated successfully!');
        if ($temporaryPassword) {
            $response->with('issued_credentials', ['email' => $user->email, 'password' => $temporaryPassword]);
        }

        return $response;
    }

    public function deleteUser(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Administrator accounts cannot be deleted from this screen.');
        $user->delete();

        return redirect('/users')->with('success', 'User deleted successfully!');
    }
}