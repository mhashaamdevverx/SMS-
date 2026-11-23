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
        $now = now();

        foreach (DB::table('Students')->whereNull('login_user_id')->orderBy('id')->get() as $student) {
            $name = trim((string) ($student->name ?: 'Student '.$student->id));
            $base = Str::slug($name, '.');
            $base = $base !== '' ? $base : 'student';
            $roll = $student->std_id ?: $student->id;
            $suffix = 0;

            do {
                $suffix++;
                $email = $base.'.'.$roll.($suffix > 1 ? '.'.$suffix : '').'@akm.std';
            } while (
                DB::table('users')->where('email', $email)->exists()
                || DB::table('Students')->where('email', $email)->exists()
            );

            $password = Str::studly($name).'@akm.std';
            $userId = DB::table('users')->insertGetId([
                'username' => $name,
                'name' => $name,
                'email' => $email,
                'phone' => $student->phone_no,
                'image' => $student->image ?: 'dummy.jpeg',
                'password' => Hash::make($password),
                'role_id' => $studentRoleId,
                'must_change_password' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('Students')->where('id', $student->id)->update(['login_user_id' => $userId]);
        }

        foreach (DB::table('teachers')->whereNull('login_user_id')->orderBy('id')->get() as $teacher) {
            $existingUserId = DB::table('users')->where('email', $teacher->teacher_email)->value('id');
            if ($existingUserId) {
                DB::table('teachers')->where('id', $teacher->id)->update(['login_user_id' => $existingUserId]);
                continue;
            }

            $name = trim((string) ($teacher->teacher_name ?: 'Teacher '.$teacher->id));
            $password = Str::studly($name).'@akm.123';
            $userId = DB::table('users')->insertGetId([
                'username' => $name,
                'name' => $name,
                'email' => $teacher->teacher_email,
                'phone' => $teacher->teacher_phone,
                'image' => $teacher->teacher_image ?: 'dummy.jpeg',
                'password' => Hash::make($password),
                'role_id' => $teacherRoleId,
                'must_change_password' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('teachers')->where('id', $teacher->id)->update(['login_user_id' => $userId]);
        }
    }

    public function down(): void
    {
        DB::table('Students')->whereNotNull('login_user_id')->update(['login_user_id' => null]);
        DB::table('teachers')->whereNotNull('login_user_id')->update(['login_user_id' => null]);
        DB::table('users')->whereIn('role_id', DB::table('roles')->whereIn('slug', ['student', 'teacher'])->select('id'))->delete();
    }
};