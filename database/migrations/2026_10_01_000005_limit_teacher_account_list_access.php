<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $teacherRoleId = DB::table('roles')->where('slug', 'teacher')->value('id');
        $permissionId = DB::table('permissions')->where('name', 'users.view')->value('id');
        if ($teacherRoleId && $permissionId) {
            DB::table('permission_role')
                ->where('role_id', $teacherRoleId)
                ->where('permission_id', $permissionId)
                ->delete();
        }
    }

    public function down(): void
    {
        $teacherRoleId = DB::table('roles')->where('slug', 'teacher')->value('id');
        $permissionId = DB::table('permissions')->where('name', 'users.view')->value('id');
        if ($teacherRoleId && $permissionId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $teacherRoleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};