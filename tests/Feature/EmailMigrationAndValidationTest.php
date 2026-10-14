<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailMigrationAndValidationTest extends TestCase
{
    /**
     * Test 1: Verify all active database user emails end in @gmail.com
     */
    public function test_all_users_in_database_have_valid_gmail_addresses(): void
    {
        $nonGmailUsers = DB::table('users')
            ->where('email', 'not like', '%@gmail.com')
            ->count();

        $this->assertSame(0, $nonGmailUsers, 'All users in users table must have an email ending with @gmail.com');

        $nonGmailTeachers = DB::table('teachers')
            ->where('teacher_email', 'not like', '%@gmail.com')
            ->count();

        $this->assertSame(0, $nonGmailTeachers, 'All teachers must have teacher_email ending with @gmail.com');

        $nonGmailStudents = DB::table('Students')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%@gmail.com')
            ->count();

        $this->assertSame(0, $nonGmailStudents, 'All students with emails must have email ending with @gmail.com');
    }

    /**
     * Test 2: Verify zero duplicate emails in users, teachers, and Students tables
     */
    public function test_zero_duplicate_emails_exist_across_tables(): void
    {
        $duplicateUsers = DB::table('users')
            ->select('email', DB::raw('COUNT(*) as total'))
            ->groupBy('email')
            ->having('total', '>', 1)
            ->count();

        $this->assertSame(0, $duplicateUsers, 'There should be no duplicate emails in the users table.');

        $duplicateTeachers = DB::table('teachers')
            ->select('teacher_email', DB::raw('COUNT(*) as total'))
            ->groupBy('teacher_email')
            ->having('total', '>', 1)
            ->count();

        $this->assertSame(0, $duplicateTeachers, 'There should be no duplicate emails in the teachers table.');

        $duplicateStudents = DB::table('Students')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('email', DB::raw('COUNT(*) as total'))
            ->groupBy('email')
            ->having('total', '>', 1)
            ->count();

        $this->assertSame(0, $duplicateStudents, 'There should be no duplicate emails in the Students table.');
    }

    /**
     * Test 3: Verify synchronization between role tables and users table
     */
    public function test_role_table_emails_are_synchronized_with_users_table(): void
    {
        // Teachers sync
        $desyncedTeachers = DB::table('teachers')
            ->join('users', 'teachers.login_user_id', '=', 'users.id')
            ->whereColumn('teachers.teacher_email', '!=', 'users.email')
            ->count();

        $this->assertSame(0, $desyncedTeachers, 'All teacher records with login accounts must match their users.email.');

        // Students sync
        $desyncedStudents = DB::table('Students')
            ->join('users', 'Students.login_user_id', '=', 'users.id')
            ->whereColumn('Students.email', '!=', 'users.email')
            ->count();

        $this->assertSame(0, $desyncedStudents, 'All student records with login accounts must match their users.email.');
    }

    /**
     * Test 4: Verify authentication succeeds with migrated Gmail addresses and existing passwords
     */
    public function test_authentication_succeeds_for_all_four_roles_with_new_gmail_and_existing_passwords(): void
    {
        // 1. Admin (Abdul Moeez)
        $admin = DB::table('users')->where('email', 'abdul.moeez@gmail.com')->first();
        $this->assertNotNull($admin, 'Admin account abdul.moeez@gmail.com must exist.');
        $this->assertTrue(Hash::check('Teacher@123', $admin->password), 'Admin password must match Teacher@123');

        // 2. Teacher In-Charge (Abdullah Azam)
        $incharge = DB::table('users')->where('email', 'abdullah.azam@gmail.com')->first();
        $this->assertNotNull($incharge, 'Incharge account abdullah.azam@gmail.com must exist.');
        $this->assertTrue(Hash::check('Teacher@123', $incharge->password), 'Incharge password must match Teacher@123');

        // 3. Teacher (Ayesha Khan)
        $teacher = DB::table('users')->where('email', 'ayesha.khan@gmail.com')->first();
        $this->assertNotNull($teacher, 'Teacher account ayesha.khan@gmail.com must exist.');
        $this->assertTrue(Hash::check('Teacher@123', $teacher->password), 'Teacher password must match Teacher@123');

        // 4. Student (Hashaam Ahmad)
        $student = DB::table('users')->where('email', 'hashaam.ahmad@gmail.com')->first();
        $this->assertNotNull($student, 'Student account hashaam.ahmad@gmail.com must exist.');
        $this->assertTrue(Hash::check('Student@123', $student->password), 'Student password must match Student@123');
    }

    /**
     * Test 5: Verify login rejects non-Gmail addresses and malformed domains
     */
    public function test_login_validation_rejects_non_gmail_domains(): void
    {
        $response = $this->post('/login', [
            'email' => 'user@yahoo.com',
            'password' => 'somePassword123',
            'account_type' => 'student',
        ]);

        $response->assertSessionHasErrors('email');

        $responseOutlook = $this->post('/login', [
            'email' => 'user@outlook.com',
            'password' => 'somePassword123',
            'account_type' => 'teacher',
        ]);

        $responseOutlook->assertSessionHasErrors('email');
    }

    /**
     * Test 6: Verify old legacy role-based emails can no longer authenticate
     */
    public function test_legacy_role_based_emails_no_longer_authenticate(): void
    {
        $legacyEmails = [
            'admin@gmail.com',
            'incharge@gmail.com',
            'fatima.incharge@gmail.com',
            'teacher@gmail.com',
            'student@gmail.com',
            'student1@gmail.com',
        ];

        foreach ($legacyEmails as $legacyEmail) {
            $response = $this->post('/login', [
                'email' => $legacyEmail,
                'password' => 'Teacher@123',
                'account_type' => 'teacher',
            ]);

            // User with that legacy email must either fail validation or fail credential match
            $userExists = DB::table('users')->where('email', $legacyEmail)->exists();
            $this->assertFalse($userExists, "Legacy role email {$legacyEmail} must not exist in users table.");
        }
    }

    /**
     * Test 7: Verify attendance and academic records data integrity is preserved
     */
    public function test_attendance_and_results_data_integrity_preserved(): void
    {
        $studentAttendanceCount = DB::table('attendances')->count();
        $teacherAttendanceCount = DB::table('teacher_attendances')->count();
        $resultsCount = DB::table('results')->count();

        $this->assertGreaterThanOrEqual(21900, $studentAttendanceCount, 'Student attendance records must be intact.');
        $this->assertGreaterThanOrEqual(1095, $teacherAttendanceCount, 'Teacher attendance records must be intact.');
        $this->assertGreaterThanOrEqual(420, $resultsCount, 'Academic results records must be intact.');
    }
}
