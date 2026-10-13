<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\ClassIncharge;
use App\Models\Classes;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\TeacherAccessRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->withCount('users')->orderByDesc('is_system')->orderBy('name')->paginate(10, ['*'], 'role_page')->withQueryString();
        $allRoles = Role::orderBy('name')->get();
        $permissionGroups = Permission::query()->orderBy('label')->get()->groupBy('module');
        $pendingRequests = TeacherAccessRequest::with('teacher')->where('status', 'pending')->latest()->paginate(10, ['*'], 'request_page')->withQueryString();
        $classes = Classes::query()->orderBy('class_no')->orderBy('class_name')->orderBy('section_name')->get();
        $teachers = Teacher::with('loginAccount')->orderBy('teacher_name')->get();
        $inchargeOptions = ClassIncharge::with(['teacher', 'user', 'classRecord'])->get();
        $incharges = ClassIncharge::with(['teacher', 'user', 'classRecord'])->paginate(10, ['*'], 'incharge_page')->withQueryString();
        $users = User::with(['role', 'classIncharge.classRecord'])->orderBy('name')->paginate(10, ['*'], 'account_page')->withQueryString();

        return view('roles', compact('roles', 'allRoles', 'permissionGroups', 'pendingRequests', 'classes', 'teachers', 'incharges', 'inchargeOptions', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $slug = Str::slug($validated['name']);
        if ($slug === '') {
            return back()->withErrors(['name' => 'Enter a role name containing letters or numbers.'])->withInput();
        }

        if (Role::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'A role with this name already exists.'])->withInput();
        }

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->slug === 'admin', 403, 'The Administrator role is protected.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $slug = $role->is_system ? $role->slug : Str::slug($validated['name']);
        if ($slug === '' || Role::where('slug', $slug)->where('id', '!=', $role->id)->exists()) {
            return back()->withErrors(['name' => 'A role with this name already exists.'])->withInput();
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role and permissions updated.');
    }

    public function destroy(Role $role)
    {
        abort_if($role->is_system || $role->users()->exists(), 403, 'This role is protected or assigned to users.');
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }

    public function updateUserRole(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 403, 'Administrator accounts must be managed through a protected workflow.');

        $validated = $request->validate(['role_id' => ['required', 'integer', 'exists:roles,id']]);
        $role = Role::findOrFail($validated['role_id']);
        abort_if(in_array($role->slug, ['admin', 'class-incharge'], true), 422, 'Administrator and Class Incharge roles are assigned through protected workflows.');

        DB::transaction(function () use ($user, $role) {
            $user->update(['role_id' => $role->id]);
            if ($user->classIncharge) {
                $teacher = Teacher::find($user->classIncharge->teacher_id);
                if ($teacher) {
                    $teacher->update(['login_user_id' => null]);
                }
                $user->classIncharge->delete();
            }
        });

        return redirect()->route('roles.index')->with('success', 'User role updated.');
    }

    public function approveRequest(Request $request, TeacherAccessRequest $teacherAccessRequest)
    {
        $validated = $request->validate(['class_id' => ['required', 'integer', 'exists:classes,id']]);
        abort_unless($teacherAccessRequest->status === 'pending', 404);

        $teacher = Teacher::findOrFail($teacherAccessRequest->teacher_id);
        abort_if($teacher->login_user_id || User::where('email', $teacherAccessRequest->email)->exists(), 422, 'This teacher already has an account.');
        abort_if(ClassIncharge::where('class_id', $validated['class_id'])->exists(), 422, 'This class and section already has an incharge.');

        $inchargeRole = Role::where('slug', 'class-incharge')->firstOrFail();

        $temporaryPassword = 'Teacher@123';
        DB::transaction(function () use ($teacherAccessRequest, $teacher, $validated, $inchargeRole, $request, $temporaryPassword) {
            $user = User::create([
            'username' => $teacher->teacher_name,
            'name' => $teacher->teacher_name,
                'email' => $teacherAccessRequest->email,
            'phone' => $teacher->teacher_phone,
            'password' => Hash::make($temporaryPassword),
                'image' => $teacherAccessRequest->image ?: 'dummy.jpeg',
                'role_id' => $inchargeRole->id,
            'must_change_password' => true,
            ]);

            $teacher->update(['login_user_id' => $user->id]);
            ClassIncharge::create([
                'user_id' => $user->id,
                'teacher_id' => $teacher->id,
                'class_id' => $validated['class_id'],
                'assigned_by' => $request->session()->get('user_id'),
            ]);

            $teacherAccessRequest->update([
                'status' => 'approved',
                'reviewed_by' => $request->session()->get('user_id'),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('roles.index')
            ->with('success', 'Teacher account approved and assigned.')
            ->with('issued_credentials', ['email' => $teacherAccessRequest->email, 'password' => $temporaryPassword]);
    }

    public function assignIncharge(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
        ]);

        $teacher = Teacher::with('loginAccount')->findOrFail($validated['teacher_id']);
        $classIncharge = ClassIncharge::where('class_id', $validated['class_id'])->first();
        abort_if($classIncharge && $classIncharge->teacher_id !== $teacher->id, 422, 'This class already has an incharge.');

        $user = $teacher->loginAccount ?: User::where('email', $teacher->teacher_email)->first();
        abort_if($user?->isAdmin(), 422, 'Administrator accounts cannot be assigned as class incharges.');

        $inchargeRole = Role::where('slug', 'class-incharge')->firstOrFail();
        $teacherRole = Role::where('slug', 'teacher')->firstOrFail();
        $temporaryPassword = null;

        DB::transaction(function () use ($teacher, $user, $inchargeRole, $teacherRole, $validated, $request, &$temporaryPassword) {
            if (!$user) {
                $temporaryPassword = 'Teacher@123';
                $user = User::create([
                    'username' => $teacher->teacher_name,
                    'name' => $teacher->teacher_name,
                    'email' => $teacher->teacher_email,
                    'phone' => $teacher->teacher_phone,
                    'image' => $teacher->teacher_image ?: 'dummy.jpeg',
                    'password' => Hash::make($temporaryPassword),
                    'role_id' => $inchargeRole->id,
                    'must_change_password' => true,
                ]);
            } else {
                $user->update(['role_id' => $inchargeRole->id]);
            }

            $teacher->update(['login_user_id' => $user->id]);
            $existingIncharge = ClassIncharge::where('teacher_id', $teacher->id)->first();
            if ($existingIncharge) {
                $existingIncharge->update([
                    'class_id' => $validated['class_id'],
                    'assigned_by' => $request->session()->get('user_id'),
                ]);
            } else {
                ClassIncharge::create([
                    'user_id' => $user->id,
                    'teacher_id' => $teacher->id,
                    'class_id' => $validated['class_id'],
                    'assigned_by' => $request->session()->get('user_id'),
                ]);
            }
        });

        $response = redirect()->route('roles.index')->with('success', 'Class incharge assignment saved.');
        if ($temporaryPassword) {
            $response->with('issued_credentials', ['email' => $teacher->teacher_email, 'password' => $temporaryPassword]);
        }

        return $response;
    }

    public function removeIncharge(ClassIncharge $classIncharge)
    {
        $user = $classIncharge->user;
        $teacherRole = Role::where('slug', 'teacher')->firstOrFail();
        $user->update(['role_id' => $teacherRole->id]);
        $classIncharge->delete();

        return redirect()->route('roles.index')->with('success', 'Class incharge role removed.');
    }

    public function rejectRequest(Request $request, TeacherAccessRequest $teacherAccessRequest)
    {
        abort_unless($teacherAccessRequest->status === 'pending', 404);
        $teacherAccessRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->session()->get('user_id'),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('roles.index')->with('success', 'Teacher account request rejected.');
    }
}