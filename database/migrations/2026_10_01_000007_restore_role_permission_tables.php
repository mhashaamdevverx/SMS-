<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('module');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('class_incharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->unique()->constrained('classes')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        $modules = [
            'dashboard' => ['view' => 'View dashboard'],
            'students' => ['view' => 'View students', 'create' => 'Add students', 'update' => 'Edit students', 'delete' => 'Delete students'],
            'teachers' => ['view' => 'View teachers', 'create' => 'Add teachers', 'update' => 'Edit teachers', 'delete' => 'Delete teachers'],
            'courses' => ['view' => 'View courses', 'create' => 'Add courses', 'update' => 'Edit courses', 'delete' => 'Delete courses'],
            'classes' => ['view' => 'View classes', 'create' => 'Add classes', 'update' => 'Edit classes', 'delete' => 'Delete classes'],
            'sections' => ['view' => 'View sections', 'create' => 'Add sections', 'update' => 'Edit sections', 'delete' => 'Delete sections'],
            'subjects' => ['view' => 'View subjects', 'create' => 'Add subjects', 'update' => 'Edit subjects', 'delete' => 'Delete subjects'],
            'attendance' => ['view' => 'View attendance', 'create' => 'Record attendance', 'delete' => 'Delete attendance'],
            'results' => ['view' => 'View results', 'create' => 'Add results', 'update' => 'Edit results', 'delete' => 'Delete results'],
            'fees' => ['view' => 'View fees', 'create' => 'Create fee records', 'update' => 'Update fees', 'delete' => 'Delete fee records'],
            'roll-slips' => ['view' => 'View roll slips', 'create' => 'Issue roll slips', 'update' => 'Edit roll slips', 'delete' => 'Delete roll slips'],
            'arrangements' => ['view' => 'View arrangements', 'create' => 'Create arrangements', 'update' => 'Edit arrangements', 'delete' => 'Delete arrangements'],
            'users' => ['view' => 'View user accounts', 'update' => 'Manage user accounts', 'delete' => 'Delete user accounts'],
            'roles' => ['manage' => 'Manage roles and permissions', 'review-requests' => 'Review teacher account requests'],
            'account' => ['profile.update' => 'Update own profile', 'password.update' => 'Change own password'],
            'results-download' => ['download' => 'Download own results'],
        ];

        $now = now();
        $permissionRows = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action => $label) {
                $name = $module === 'results-download' ? 'results.'.$action : $module.'.'.$action;
                $permissionRows[] = [
                    'name' => $name,
                    'module' => $module === 'results-download' ? 'results' : $module,
                    'label' => $label,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('permissions')->insert($permissionRows);

        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        $rolePermissions = [
            'admin' => array_keys($permissionIds->all()),
            'class-incharge' => [
                'dashboard.view', 'students.view', 'students.create', 'students.update', 'students.delete',
                'attendance.view', 'attendance.create', 'results.view', 'results.create',
                'account.profile.update', 'account.password.update',
            ],
            'teacher' => [
                'dashboard.view', 'account.profile.update', 'account.password.update', 'students.view',
                'teachers.view', 'courses.view', 'classes.view', 'sections.view', 'subjects.view',
                'attendance.view', 'results.view', 'fees.view', 'roll-slips.view', 'arrangements.view',
            ],
            'student' => [
                'dashboard.view', 'account.profile.update', 'account.password.update', 'students.view',
                'results.view', 'results.download', 'roll-slips.view',
            ],
        ];

        foreach ($rolePermissions as $slug => $permissionNames) {
            $roleId = DB::table('roles')->where('slug', $slug)->value('id');
            foreach ($permissionNames as $permissionName) {
                DB::table('permission_role')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionIds[$permissionName],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_incharges');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
    }
};