<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('password');
        });

        Schema::table('Students', function (Blueprint $table) {
            $table->foreignId('login_user_id')->nullable()->unique()->after('user_id')->constrained('users')->nullOnDelete();
        });

        $now = now();
        DB::table('roles')->insert([
            ['name' => 'Teacher', 'slug' => 'teacher', 'description' => 'Read access across school records; can edit own profile.', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'Access to own profile, results, and roll slips.', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $permissionRows = [
            ['name' => 'account.password.update', 'module' => 'account', 'label' => 'Change own password'],
            ['name' => 'results.download', 'module' => 'results', 'label' => 'Download own results'],
        ];
        foreach ($permissionRows as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission + ['created_at' => $now, 'updated_at' => $now]
            );
        }

        $teacherRoleId = DB::table('roles')->where('slug', 'teacher')->value('id');
        $studentRoleId = DB::table('roles')->where('slug', 'student')->value('id');
        $inchargeRoleId = DB::table('roles')->where('slug', 'class-incharge')->value('id');
        $permissions = DB::table('permissions')->pluck('id', 'name');

        $teacherPermissions = [
            'dashboard.view', 'account.profile.update', 'account.password.update',
            'students.view', 'teachers.view', 'courses.view', 'classes.view', 'sections.view',
            'subjects.view', 'attendance.view', 'results.view', 'fees.view', 'roll-slips.view',
            'arrangements.view', 'users.view',
        ];
        $studentPermissions = [
            'dashboard.view', 'account.profile.update', 'account.password.update',
            'students.view', 'results.view', 'results.download', 'roll-slips.view',
        ];
        $inchargePermissions = [
            'students.update', 'students.delete', 'account.password.update',
        ];

        foreach ([
            $teacherRoleId => $teacherPermissions,
            $studentRoleId => $studentPermissions,
            $inchargeRoleId => $inchargePermissions,
        ] as $roleId => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissions[$permissionName],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $roleIds = DB::table('roles')->whereIn('slug', ['teacher', 'student'])->pluck('id');
        DB::table('permission_role')->whereIn('role_id', $roleIds)->delete();
        DB::table('roles')->whereIn('slug', ['teacher', 'student'])->delete();
        $permissionIds = DB::table('permissions')->whereIn('name', ['account.password.update', 'results.download'])->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();

        Schema::table('Students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('login_user_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};