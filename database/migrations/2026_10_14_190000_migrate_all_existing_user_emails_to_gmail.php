<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $cleanName = function (?string $rawName, ?string $fallback = null): string {
                $name = trim((string) $rawName);
                if ($name === '' && $fallback) {
                    $name = trim($fallback);
                }
                // Strip honorific titles / prefixes
                $name = preg_replace('/^(Sir|Ma[\'’]am|Mr\.?|Mrs\.?|Ms\.?|Miss|Madam)\s+/i', '', $name);
                $name = preg_replace('/\s+/', ' ', trim($name));
                return $name;
            };

            $nameToEmailBase = function (string $name): string {
                $normalized = strtolower($name);
                $normalized = preg_replace('/[\'’]/', '', $normalized);
                $normalized = preg_replace('/[^a-z0-9]+/i', '.', $normalized);
                $normalized = trim($normalized, '.');
                return $normalized ?: 'user';
            };

            $assignedEmails = []; // newEmail => true
            $userEmailMap = [];   // userId => newEmail

            $roles = DB::table('roles')->get()->keyBy('id');
            $studentRoleId = DB::table('roles')->where('slug', 'student')->value('id');
            $teacherRoleId = DB::table('roles')->where('slug', 'teacher')->value('id');
            $inchargeRoleId = DB::table('roles')->where('slug', 'class-incharge')->value('id');
            $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');

            // 1. Process all Users with priority: Admin -> Class Incharge -> Teacher -> Student -> Other
            $users = DB::table('users')->orderBy('id')->get();
            $sortedUsers = $users->sort(function ($a, $b) use ($adminRoleId, $inchargeRoleId, $teacherRoleId, $studentRoleId) {
                $priority = [
                    $adminRoleId => 1,
                    $inchargeRoleId => 2,
                    $teacherRoleId => 3,
                    $studentRoleId => 4,
                ];
                $pA = $priority[$a->role_id] ?? 99;
                $pB = $priority[$b->role_id] ?? 99;
                if ($pA !== $pB) {
                    return $pA <=> $pB;
                }
                return $a->id <=> $b->id;
            });

            foreach ($sortedUsers as $u) {
                $rawName = $u->name;
                if ($u->id === 1 && (empty($rawName) || strtolower($u->username) === 'hashaam')) {
                    $rawName = 'Hashaam Ahmad';
                }
                $name = $cleanName($rawName, $u->username);
                $base = $nameToEmailBase($name);

                $suggested = $base . '@gmail.com';
                $counter = 1;
                while (isset($assignedEmails[$suggested])) {
                    $counter++;
                    $suggested = $base . $counter . '@gmail.com';
                }

                $assignedEmails[$suggested] = true;
                $userEmailMap[$u->id] = $suggested;

                $updateData = [
                    'email' => $suggested,
                    'updated_at' => now(),
                ];
                if (empty($u->name) || preg_match('/^(Sir|Ma[\'’]am|Mr\.?|Mrs\.?|Ms\.?|Miss|Madam)\s+/i', (string)$u->name)) {
                    $updateData['name'] = $name;
                }

                DB::table('users')->where('id', $u->id)->update($updateData);
            }

            // 2. Synchronize / Update Teachers table
            $teachers = DB::table('teachers')->orderBy('id')->get();
            foreach ($teachers as $t) {
                $name = $cleanName($t->teacher_name);
                if ($t->login_user_id && isset($userEmailMap[$t->login_user_id])) {
                    $email = $userEmailMap[$t->login_user_id];
                } else {
                    $base = $nameToEmailBase($name);
                    $email = $base . '@gmail.com';
                    $counter = 1;
                    while (isset($assignedEmails[$email])) {
                        $counter++;
                        $email = $base . $counter . '@gmail.com';
                    }
                    $assignedEmails[$email] = true;
                }

                DB::table('teachers')->where('id', $t->id)->update([
                    'teacher_name' => $name,
                    'teacher_email' => $email,
                    'updated_at' => now(),
                ]);
            }

            // Ensure Teacher profile for User #2 (Abdullah Azam) if missing
            $user2Teacher = DB::table('teachers')->where('login_user_id', 2)->first();
            if (!$user2Teacher) {
                $u2 = DB::table('users')->where('id', 2)->first();
                if ($u2) {
                    DB::table('teachers')->insert([
                        'user_id' => $u2->id,
                        'login_user_id' => $u2->id,
                        'teacher_name' => $u2->name ?: 'Abdullah Azam',
                        'teacher_email' => $u2->email,
                        'teacher_phone' => $u2->phone ?: '03034129159',
                        'course' => 'General Subjects',
                        'teacher_image' => $u2->image,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 3. Synchronize / Update Students table
            $students = DB::table('Students')->orderBy('id')->get();
            $defaultStudentPasswordHash = Hash::make('Student@123');

            foreach ($students as $s) {
                $name = $cleanName($s->name);
                if ($s->login_user_id && isset($userEmailMap[$s->login_user_id])) {
                    $email = $userEmailMap[$s->login_user_id];
                } else {
                    $base = $nameToEmailBase($name);
                    $email = $base . '@gmail.com';
                    $counter = 1;
                    while (isset($assignedEmails[$email])) {
                        $counter++;
                        $email = $base . $counter . '@gmail.com';
                    }
                    $assignedEmails[$email] = true;

                    // Create login user account if missing
                    $newUserId = DB::table('users')->insertGetId([
                        'username' => $name,
                        'name' => $name,
                        'email' => $email,
                        'phone' => $s->phone_no,
                        'image' => $s->image ?: 'dummy.jpeg',
                        'password' => $defaultStudentPasswordHash,
                        'role_id' => $studentRoleId,
                        'must_change_password' => false,
                        'password_prompt_dismissed' => false,
                        'password_changed' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('Students')->where('id', $s->id)->update([
                        'login_user_id' => $newUserId,
                    ]);
                    $userEmailMap[$newUserId] = $email;
                }

                DB::table('Students')->where('id', $s->id)->update([
                    'name' => $name,
                    'email' => $email,
                ]);
            }

            // Ensure Student record for User #1 (Hashaam Ahmad)
            $user1Student = DB::table('Students')->where('login_user_id', 1)->first();
            if (!$user1Student) {
                $u1 = DB::table('users')->where('id', 1)->first();
                if ($u1) {
                    DB::table('Students')->insert([
                        'std_id' => 1,
                        'registration_no' => 'REG-10-TEN-001',
                        'name' => $u1->name ?: 'Hashaam Ahmad',
                        'first_name' => 'Hashaam',
                        'last_name' => 'Ahmad',
                        'email' => $u1->email,
                        'phone_no' => $u1->phone ?: '0300-1111111',
                        'emergency_phone' => '0321-1111111',
                        'class' => 'TEN',
                        'section' => 'Red',
                        'user_id' => 4, // Admin user id
                        'login_user_id' => 1,
                        'monthly_fee' => 0.00,
                        'admission_fee' => 0.00,
                        'test_session_fee' => 0.00,
                    ]);
                }
            }

            // 4. Update Teacher Access Requests
            if (DB::getSchemaBuilder()->hasTable('teacher_access_requests')) {
                $requests = DB::table('teacher_access_requests')->get();
                foreach ($requests as $req) {
                    $teacher = DB::table('teachers')->where('id', $req->teacher_id)->first();
                    if ($teacher) {
                        DB::table('teacher_access_requests')->where('id', $req->id)->update([
                            'email' => $teacher->teacher_email,
                            'name' => $teacher->teacher_name,
                        ]);
                    }
                }
            }

            // 5. Update Password Reset Requests
            if (DB::getSchemaBuilder()->hasTable('password_reset_requests')) {
                $resets = DB::table('password_reset_requests')->get();
                foreach ($resets as $pr) {
                    $user = DB::table('users')->where('id', $pr->user_id)->first();
                    if ($user) {
                        DB::table('password_reset_requests')->where('id', $pr->id)->update([
                            'email' => $user->email,
                        ]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Recovery point is stored in database backup tables:
        // users_backup_20261014, teachers_backup_20261014, students_backup_20261014
    }
};
