<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

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

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('login_user_id')->nullable()->unique()->after('user_id')->constrained('users')->nullOnDelete();
        });

        Schema::create('class_incharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->unique()->constrained('classes')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('teacher_access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone', 30);
            $table->string('password');
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('roles')->insert([
            ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Full access to school settings and records.', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Class Incharge Teacher', 'slug' => 'class-incharge', 'description' => 'Access limited to the assigned class and section.', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $permissionRows = [];
        foreach ([
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
        ] as $module => $actions) {
            foreach ($actions as $action => $label) {
                $permissionRows[] = [
                    'name' => $module.'.'.$action,
                    'module' => $module,
                    'label' => $label,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('permissions')->insert($permissionRows);
        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        $inchargeRoleId = DB::table('roles')->where('slug', 'class-incharge')->value('id');
        $allPermissions = DB::table('permissions')->pluck('id', 'name');

        foreach ($allPermissions as $permissionId) {
            DB::table('permission_role')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach (['dashboard.view', 'students.view', 'students.create', 'attendance.view', 'attendance.create', 'results.view', 'results.create'] as $permissionName) {
            DB::table('permission_role')->insert([
                'role_id' => $inchargeRoleId,
                'permission_id' => $allPermissions[$permissionName],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('users')->where('id', 4)->update(['role_id' => $adminRoleId]);
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_access_requests');
        Schema::dropIfExists('class_incharges');
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('login_user_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};