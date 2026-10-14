<?php

namespace Tests\Feature;

use App\Http\Controllers\AuthController;
use App\Http\Middleware\RequireRolePermission;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Support\InchargeAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PortalRoleAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('DROP TEMPORARY TABLE IF EXISTS permission_role, permissions, class_incharges, classes, Students, teachers, users, roles');
        DB::statement('CREATE TEMPORARY TABLE roles (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL UNIQUE, description TEXT NULL, is_system TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
        DB::statement('CREATE TEMPORARY TABLE permissions (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL UNIQUE, module VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
        DB::statement('CREATE TEMPORARY TABLE permission_role (role_id BIGINT UNSIGNED NOT NULL, permission_id BIGINT UNSIGNED NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, PRIMARY KEY (role_id, permission_id))');
        DB::statement('CREATE TEMPORARY TABLE users (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, role_id BIGINT UNSIGNED NULL, username VARCHAR(255) NOT NULL, name VARCHAR(255) NULL, email VARCHAR(255) NOT NULL UNIQUE, phone VARCHAR(30) NULL, image VARCHAR(255) NULL, password VARCHAR(255) NOT NULL, remember_token VARCHAR(100) NULL, must_change_password TINYINT(1) NOT NULL DEFAULT 0, password_prompt_dismissed TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
        DB::statement('CREATE TEMPORARY TABLE password_reset_tokens (email VARCHAR(255) PRIMARY KEY, token VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL)');
        DB::statement('CREATE TEMPORARY TABLE Students (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, login_user_id BIGINT UNSIGNED NULL, name VARCHAR(255) NOT NULL, class VARCHAR(50) NOT NULL, section VARCHAR(100) NOT NULL, std_id BIGINT UNSIGNED NULL, email VARCHAR(255) NULL, phone_no VARCHAR(30) NULL, image VARCHAR(255) NULL)');
        DB::statement('CREATE TEMPORARY TABLE teachers (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, login_user_id BIGINT UNSIGNED NULL, teacher_name VARCHAR(255) NOT NULL, teacher_email VARCHAR(255) NOT NULL, teacher_phone VARCHAR(30) NOT NULL, teacher_image VARCHAR(255) NULL, course VARCHAR(255) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
        DB::statement('CREATE TEMPORARY TABLE classes (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, class_name VARCHAR(50) NOT NULL, section_name VARCHAR(100) NOT NULL)');
        DB::statement('CREATE TEMPORARY TABLE class_incharges (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL UNIQUE, teacher_id BIGINT UNSIGNED NOT NULL UNIQUE, class_id BIGINT UNSIGNED NOT NULL UNIQUE, assigned_by BIGINT UNSIGNED NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)');
    }

    protected function tearDown(): void
    {
        DB::statement('DROP TEMPORARY TABLE IF EXISTS permission_role, permissions, class_incharges, classes, Students, teachers, password_reset_tokens, users, roles');

        parent::tearDown();
    }

    public function test_login_requires_the_selected_account_type_to_match(): void
    {
        $studentRole = $this->makeRole('student');
        $studentUser = $this->makeUser($studentRole, 'learner.one@gmail.com');

        DB::table('Students')->insert([
            'login_user_id' => $studentUser->id,
            'name' => 'Learner One',
            'class' => 'ONE',
            'section' => 'A',
            'std_id' => 1,
            'email' => 'learner.one@gmail.com',
        ]);

        $wrongRoleResponse = app(AuthController::class)->login(Request::create('/login', 'POST', [
            'email' => $studentUser->email,
            'password' => 'temporary-password',
            'account_type' => 'teacher',
        ]));
        $this->assertSame('/login', parse_url($wrongRoleResponse->getTargetUrl(), PHP_URL_PATH));

        $matchingRoleResponse = app(AuthController::class)->login(Request::create('/login', 'POST', [
            'email' => $studentUser->email,
            'password' => 'temporary-password',
            'account_type' => 'student',
        ]));
        $this->assertSame(route('account.password.edit'), $matchingRoleResponse->getTargetUrl());
    }

    public function test_non_admin_cannot_open_role_management(): void
    {
        $teacherRole = $this->makeRole('teacher');
        $teacher = $this->makeUser($teacherRole, 'teacher.lead@gmail.com', false);

        $request = Request::create('/roles', 'GET');
        $request->setLaravelSession(app('session')->driver());
        $request->session()->put('user_id', $teacher->id);

        try {
            app(RequireRolePermission::class)->handle($request, fn () => response('allowed'));
            $this->fail('A Teacher must not reach role management.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_dismissing_password_reminder_allows_portal_access_without_claiming_password_was_changed(): void
    {
        $studentRole = $this->makeRole('student');
        $studentUser = $this->makeUser($studentRole, 'dismiss.user@gmail.com');
        $request = Request::create('/account/password/dismiss', 'POST');
        $request->setLaravelSession(app('session')->driver());
        $request->session()->put('user_id', $studentUser->id);

        $response = app(AuthController::class)->dismissPasswordPrompt($request);
        $studentUser->refresh();

        $this->assertSame(route('dashboard'), $response->getTargetUrl());
        $this->assertEquals(1, $studentUser->password_prompt_dismissed);
        $this->assertEquals(1, $studentUser->must_change_password);
    }

    public function test_password_reset_saves_the_new_hashed_password(): void
    {
        $studentRole = $this->makeRole('student');
        $studentUser = $this->makeUser($studentRole, 'reset.user@gmail.com');
        Notification::fake();
        $this->assertSame(Password::RESET_LINK_SENT, Password::sendResetLink(['email' => $studentUser->email]));
        $token = null;
        Notification::assertSentTo($studentUser, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });
        $this->assertNotNull($token);

        $response = app(AuthController::class)->resetPassword(Request::create('/reset-password', 'POST', [
            'token' => $token,
            'email' => $studentUser->email,
            'password' => 'new-safe-password',
            'password_confirmation' => 'new-safe-password',
        ]));

        $this->assertSame(route('login'), $response->getTargetUrl());
        $studentUser->refresh();
        $this->assertTrue(Hash::check('new-safe-password', $studentUser->password));
        $this->assertEquals(0, $studentUser->must_change_password);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $studentUser->email]);
    }

    public function test_forgot_password_creates_a_reset_token(): void
    {
        config(['mail.default' => 'array']);
        $studentRole = $this->makeRole('student');
        $studentUser = $this->makeUser($studentRole, 'forgot.user@gmail.com', false);

        $status = Password::sendResetLink(['email' => $studentUser->email]);

        $this->assertSame(Password::RESET_LINK_SENT, $status);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $studentUser->email]);
    }

    public function test_student_queries_are_limited_to_the_linked_student_record(): void
    {
        $studentRole = $this->makeRole('student');
        $studentUser = $this->makeUser($studentRole, 'student.one@gmail.com');
        $ownStudentId = DB::table('Students')->insertGetId([
            'login_user_id' => $studentUser->id,
            'name' => 'Own Student',
            'class' => 'ONE',
            'section' => 'A',
            'std_id' => 1,
        ]);
        DB::table('Students')->insert([
            'name' => 'Other Student',
            'class' => 'TWO',
            'section' => 'B',
            'std_id' => 2,
        ]);

        $this->withSession(['user_id' => $studentUser->id]);
        $studentIds = InchargeAccess::constrainStudents(Student::query())->pluck('id')->all();

        $this->assertSame([$ownStudentId], $studentIds);
    }

    public function test_incharge_queries_are_limited_to_the_assigned_class_and_section(): void
    {
        $inchargeRole = $this->makeRole('class-incharge');
        $inchargeUser = $this->makeUser($inchargeRole, 'incharge.one@gmail.com');
        $classId = DB::table('classes')->insertGetId(['class_name' => 'ONE', 'section_name' => 'A']);
        $teacherId = DB::table('teachers')->insertGetId([
            'login_user_id' => $inchargeUser->id,
            'teacher_name' => 'Teacher One',
            'teacher_email' => $inchargeUser->email,
            'teacher_phone' => '03001234567',
            'course' => 'Math',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('class_incharges')->insert([
            'user_id' => $inchargeUser->id,
            'teacher_id' => $teacherId,
            'class_id' => $classId,
            'assigned_by' => $inchargeUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $assignedStudentId = DB::table('Students')->insertGetId([
            'name' => 'Assigned Student',
            'class' => 'ONE',
            'section' => 'A',
            'std_id' => 1,
        ]);
        DB::table('Students')->insert([
            'name' => 'Other Section Student',
            'class' => 'ONE',
            'section' => 'B',
            'std_id' => 2,
        ]);

        $this->withSession(['user_id' => $inchargeUser->id]);
        $studentIds = InchargeAccess::constrainStudents(Student::query())->pluck('id')->all();

        $this->assertSame([$assignedStudentId], $studentIds);
    }

    private function makeRole(string $slug): Role
    {
        return Role::create([
            'name' => ucfirst($slug),
            'slug' => $slug,
            'description' => null,
            'is_system' => true,
        ]);
    }

    private function makeUser(Role $role, string $email, bool $mustChangePassword = true): User
    {
        return User::create([
            'role_id' => $role->id,
            'username' => 'Portal User',
            'name' => 'Portal User',
            'email' => $email,
            'password' => Hash::make('temporary-password'),
            'must_change_password' => $mustChangePassword,
        ]);
    }
}