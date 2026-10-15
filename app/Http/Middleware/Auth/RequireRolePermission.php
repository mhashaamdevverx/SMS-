<?php

namespace App\Http\Middleware\Auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRolePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');

        if (in_array($path, ['login', 'signup', 'forgot-password'], true)
            || str_starts_with($path, 'reset-password/')
            || $path === 'reset-password'
            || str_starts_with($path, 'storage/')) {
            return $next($request);
        }

        $userId = $request->session()->get('user_id');
        if (!$userId) {
            return redirect('/login');
        }

        $user = User::with(['role.permissions', 'classIncharge.classRecord'])
            ->find($userId);

        if (!$user) {
            $request->session()->flush();

            return redirect('/login');
        }

        view()->share('currentUser', $user);

        if ($user->role?->slug === 'student') {
            if ($path === '' || $path === 'dashboard') {
                return redirect()->route('student.dashboard');
            }
            if ($request->method() !== 'GET' && !str_starts_with($path, 'student/')) {
                abort(403, 'Students are not permitted to modify or delete school records.');
            }
        }

        if ($user->role?->slug !== 'student' && $user->must_change_password && !$user->password_prompt_dismissed && $path !== 'account/password' && $path !== 'account/password/dismiss' && $path !== 'logout') {
            return redirect()->route('account.password.edit');
        }

        if (str_starts_with($path, 'student/')) {
            abort_unless($user->role?->slug === 'student' && $user->studentRecord, 403);
            return $next($request);
        }

        if (str_starts_with($path, 'teacher/')) {
            $isTeacher = in_array($user->role?->slug, ['teacher', 'class-incharge', 'admin'], true) 
                || $user->teacherRecord !== null 
                || \App\Models\Teacher::where('teacher_email', $user->email)->exists()
                || \App\Models\Teacher::where('user_id', $user->id)->exists()
                || \App\Models\Teacher::where('login_user_id', $user->id)->exists();

            if (!$isTeacher) {
                if ($user->role?->slug === 'student') {
                    return redirect()->route('student.dashboard')->with('error', 'Please sign in with a Teacher account to access the Teacher Portal.');
                }
                return redirect()->route('login')->with('error', 'Teacher access required.');
            }

            return $next($request);
        }

        if (str_starts_with($path, 'roles') || str_starts_with($path, 'teacher-access-requests')) {
            abort_unless($user->isAdmin(), 403);

            return $next($request);
        }

        if (str_starts_with($path, 'admin/') || $path === 'admin') {
            if (!$user->isAdmin()) {
                if ($user->role?->slug === 'student') {
                    return redirect()->route('student.dashboard')->with('error', 'Administrator portal access required.');
                }
                if (in_array($user->role?->slug, ['teacher', 'class-incharge'], true) || $user->teacherRecord !== null) {
                    return redirect()->route('teacher.dashboard')->with('error', 'Admin portal access required. You are currently logged in as a Teacher.');
                }
                return redirect()->route('login')->with('error', 'Administrator portal access required. Please sign in with an Admin account.');
            }

            return $next($request);
        }

        if ($path === 'logout' || $user->isAdmin()) {
            return $next($request);
        }

        $permission = $this->permissionFor($path, $request->method());
        abort_unless($permission && $user->hasPermission($permission), 403);

        if ($user->role?->slug === 'class-incharge') {
            abort_unless($user->classIncharge, 403, 'No class has been assigned to this account.');
            abort_unless(str_ends_with($permission, '.view') || $this->inchargeCanAccess($permission), 403);
        }

        return $next($request);
    }

    private function permissionFor(string $path, string $method): ?string
    {
        if ($path === '') {
            return 'dashboard.view';
        }

        if ($path === 'profile/settings') {
            return 'account.profile.update';
        }

        if (in_array($path, ['account/password', 'account/password/dismiss'], true)) {
            return 'account.password.update';
        }

        if (in_array($path, ['roll-slips/class-document', 'roll-slips/class-download', 'issue-roll-slips'], true)) {
            return 'roll-slips.create';
        }

        if ($path === 'list' || str_starts_with($path, 'students/') || $path === 'add-student' || $path === 'api/student-roll-info') {
            if (preg_match('#^students/[^/]+$#', $path) && $method === 'GET') {
                return 'students.view';
            }

            if (preg_match('#^students/\d+/results/download$#', $path)) {
                return 'results.download';
            }

            if (preg_match('#^students/[^/]+/subjects$#', $path)) {
                return 'students.update';
            }

            if ($path === 'api/student-roll-info' || $path === 'add-student' || $method === 'POST') {
                return 'students.create';
            }

            return match ($method) {
                'PUT', 'PATCH' => 'students.update',
                'DELETE' => 'students.delete',
                default => 'students.view',
            };
        }

        if (str_starts_with($path, 'results') || str_starts_with($path, 'add-result')) {
            return match ($method) {
                'POST' => 'results.create',
                'PUT', 'PATCH' => 'results.update',
                'DELETE' => 'results.delete',
                default => str_starts_with($path, 'add-result') ? 'results.create' : 'results.view',
            };
        }

        if (str_starts_with($path, 'attendance')) {
            return match ($method) {
                'POST' => 'attendance.create',
                'DELETE' => 'attendance.delete',
                default => 'attendance.view',
            };
        }

        $modules = [
            'users' => 'users', 'profile' => 'users', 'teachers' => 'teachers',
            'add-teacher' => 'teachers', 'courses' => 'courses', 'add-course' => 'courses',
            'classes' => 'classes', 'add-class' => 'classes', 'sections' => 'sections',
            'add-section' => 'sections', 'subjects' => 'subjects', 'add-subject' => 'subjects',
            'arrangements' => 'arrangements', 'add-arrangement' => 'arrangements',
            'roll-slips' => 'roll-slips', 'issue-roll-slips' => 'roll-slips',
            'fee-structure' => 'fees', 'logout' => 'users',
        ];

        $createPrefixes = [
            'add-teacher' => 'teachers', 'add-course' => 'courses', 'add-class' => 'classes',
            'add-section' => 'sections', 'add-subject' => 'subjects', 'add-arrangement' => 'arrangements',
        ];
        foreach ($createPrefixes as $prefix => $module) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return $module.'.create';
            }
        }

        foreach ($modules as $prefix => $module) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                $action = match ($method) {
                    'POST' => 'create',
                    'PUT', 'PATCH' => 'update',
                    'DELETE' => 'delete',
                    default => 'view',
                };

                return $module.'.'.$action;
            }
        }

        return null;
    }

    private function inchargeCanAccess(string $permission): bool
    {
        return in_array($permission, [
            'dashboard.view',
            'students.view',
            'students.create',
            'students.update',
            'students.delete',
            'attendance.view',
            'attendance.create',
            'results.view',
            'results.create',
        ], true);
    }
}
