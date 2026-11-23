<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $studentRoleId = DB::table('roles')->where('slug', 'student')->value('id');
        $teacherRoleId = DB::table('roles')->where('slug', 'teacher')->value('id');
        $inchargeRoleId = DB::table('roles')->where('slug', 'class-incharge')->value('id');

        $studentPasswordHash = Hash::make('Student@123');
        $teacherPasswordHash = Hash::make('Teacher@123');

        // 1. Update Teachers & their Login Accounts
        $teachers = DB::table('teachers')->get();
        foreach ($teachers as $teacher) {
            $email = trim((string) $teacher->teacher_email);
            if (!str_ends_with(strtolower($email), '@gmail.com')) {
                $usernamePart = explode('@', $email)[0];
                $usernamePart = Str::slug($usernamePart, '.');
                if (empty($usernamePart)) {
                    $usernamePart = Str::slug($teacher->teacher_name, '.');
                }
                if (empty($usernamePart)) {
                    $usernamePart = 'teacher' . $teacher->id;
                }

                $newEmail = $usernamePart . '@gmail.com';
                $counter = 1;
                while (
                    DB::table('teachers')->where('teacher_email', $newEmail)->where('id', '!=', $teacher->id)->exists() ||
                    DB::table('users')->where('email', $newEmail)->where('id', '!=', $teacher->login_user_id)->exists()
                ) {
                    $counter++;
                    $newEmail = $usernamePart . $counter . '@gmail.com';
                }
                $email = $newEmail;

                DB::table('teachers')->where('id', $teacher->id)->update(['teacher_email' => $email]);
            }

            if ($teacher->login_user_id) {
                DB::table('users')->where('id', $teacher->login_user_id)->update([
                    'email' => $email,
                    'password' => $teacherPasswordHash,
                    'updated_at' => now(),
                ]);
            } else {
                $userId = DB::table('users')->where('email', $email)->value('id');
                if ($userId) {
                    DB::table('teachers')->where('id', $teacher->id)->update(['login_user_id' => $userId]);
                    DB::table('users')->where('id', $userId)->update([
                        'email' => $email,
                        'password' => $teacherPasswordHash,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Update Students & their Login Accounts
        $students = DB::table('Students')->get();
        foreach ($students as $student) {
            $email = trim((string) $student->email);
            if (!str_ends_with(strtolower($email), '@gmail.com')) {
                $usernamePart = explode('@', $email)[0];
                $usernamePart = Str::slug($usernamePart, '.');
                if (empty($usernamePart)) {
                    $usernamePart = Str::slug($student->name, '.');
                }
                if (empty($usernamePart)) {
                    $usernamePart = 'student' . $student->id;
                }

                $newEmail = $usernamePart . '@gmail.com';
                $counter = 1;
                while (
                    DB::table('Students')->where('email', $newEmail)->where('id', '!=', $student->id)->exists() ||
                    DB::table('users')->where('email', $newEmail)->where('id', '!=', $student->login_user_id)->exists()
                ) {
                    $counter++;
                    $newEmail = $usernamePart . '.' . ($student->std_id ?: $student->id) . ($counter > 1 ? '.' . $counter : '') . '@gmail.com';
                }
                $email = $newEmail;

                DB::table('Students')->where('id', $student->id)->update(['email' => $email]);
            }

            if ($student->login_user_id) {
                DB::table('users')->where('id', $student->login_user_id)->update([
                    'email' => $email,
                    'password' => $studentPasswordHash,
                    'updated_at' => now(),
                ]);
            } else {
                $userId = DB::table('users')->where('email', $email)->value('id');
                if ($userId) {
                    DB::table('Students')->where('id', $student->id)->update(['login_user_id' => $userId]);
                    DB::table('users')->where('id', $userId)->update([
                        'email' => $email,
                        'password' => $studentPasswordHash,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. Update remaining users by role
        if ($studentRoleId) {
            $studentUsers = DB::table('users')->where('role_id', $studentRoleId)->get();
            foreach ($studentUsers as $su) {
                $email = trim((string) $su->email);
                if (!str_ends_with(strtolower($email), '@gmail.com')) {
                    $usernamePart = Str::slug(explode('@', $email)[0], '.');
                    $usernamePart = $usernamePart !== '' ? $usernamePart : 'student' . $su->id;
                    $newEmail = $usernamePart . '@gmail.com';
                    $counter = 1;
                    while (DB::table('users')->where('email', $newEmail)->where('id', '!=', $su->id)->exists()) {
                        $counter++;
                        $newEmail = $usernamePart . $counter . '@gmail.com';
                    }
                    $email = $newEmail;
                }
                DB::table('users')->where('id', $su->id)->update([
                    'email' => $email,
                    'password' => $studentPasswordHash,
                    'updated_at' => now(),
                ]);
            }
        }

        $teacherRoleIds = array_filter([$teacherRoleId, $inchargeRoleId]);
        if (!empty($teacherRoleIds)) {
            $teacherUsers = DB::table('users')->whereIn('role_id', $teacherRoleIds)->get();
            foreach ($teacherUsers as $tu) {
                $email = trim((string) $tu->email);
                if (!str_ends_with(strtolower($email), '@gmail.com')) {
                    $usernamePart = Str::slug(explode('@', $email)[0], '.');
                    $usernamePart = $usernamePart !== '' ? $usernamePart : 'teacher' . $tu->id;
                    $newEmail = $usernamePart . '@gmail.com';
                    $counter = 1;
                    while (DB::table('users')->where('email', $newEmail)->where('id', '!=', $tu->id)->exists()) {
                        $counter++;
                        $newEmail = $usernamePart . $counter . '@gmail.com';
                    }
                    $email = $newEmail;
                }
                DB::table('users')->where('id', $tu->id)->update([
                    'email' => $email,
                    'password' => $teacherPasswordHash,
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. Update teacher access requests emails
        $requests = DB::table('teacher_access_requests')->get();
        foreach ($requests as $req) {
            $email = trim((string) $req->email);
            if (!str_ends_with(strtolower($email), '@gmail.com')) {
                $usernamePart = Str::slug(explode('@', $email)[0], '.');
                $newEmail = ($usernamePart ?: 'teacher' . $req->id) . '@gmail.com';
                DB::table('teacher_access_requests')->where('id', $req->id)->update(['email' => $newEmail]);
            }
        }
    }

    public function down(): void
    {
    }
};
