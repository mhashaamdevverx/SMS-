<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminPortalController;
use App\Http\Controllers\Teacher\TeacherPortalController;
use App\Http\Controllers\Student\StudentPortalController;
use App\Http\Controllers\Management\StudentController;
use App\Http\Controllers\Management\TeacherController;
use App\Http\Controllers\Management\CourseController;
use App\Http\Controllers\Management\SectionController;
use App\Http\Controllers\Management\UserController;
use App\Http\Controllers\Management\SubjectController;
use App\Http\Controllers\Management\ClassController;
use App\Http\Controllers\Management\ArrangementController;
use App\Http\Controllers\Management\ResultController;
use App\Http\Controllers\Management\RollSlipController;
use App\Http\Controllers\Management\FeeController;
use App\Http\Controllers\Management\AttendanceController;
use App\Http\Controllers\Management\DashboardController;
use App\Http\Controllers\Management\RoleController;
use App\Http\Controllers\Management\TestSessionController;
use App\Http\Controllers\Management\NotificationController;
use App\Http\Middleware\RequireAuthentication;

Route::get('signup', [AuthController::class, 'signupForm']);
Route::post('signup', [AuthController::class, 'signup']);

Route::get('login', [AuthController::class, 'loginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
Route::get('reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('support', [AuthController::class, 'supportPage'])->name('support');
Route::post('support', [AuthController::class, 'submitSupportRequest'])->name('support.submit');

Route::middleware(RequireAuthentication::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('student/dashboard', [StudentPortalController::class, 'dashboard'])->name('student.dashboard');
    Route::get('student/test-sessions', [TestSessionController::class, 'index'])->name('student.tests');
    Route::get('student/test-sessions/{month}/download', [TestSessionController::class, 'downloadResult'])->name('student.tests.download');
    Route::get('student/outlines/{id}/download', [StudentPortalController::class, 'downloadOutline'])->name('student.outlines.download');
    Route::get('student/{page}', [StudentPortalController::class, 'page'])->where('page', 'profile|fees|academics|attendance|results|slips|leaves|tests|outlines|teachers')->name('student.page');
    Route::put('student/profile', [StudentPortalController::class, 'updateProfile'])->name('student.profile.update');
    Route::post('student/password', [StudentPortalController::class, 'updatePassword'])->name('student.password.update');
    Route::post('student/pay-fee/{feeCollection}', [StudentPortalController::class, 'payFee'])->name('student.fee.pay');
    Route::get('student/fee-voucher/{feeCollection}/download', [StudentPortalController::class, 'downloadFeeVoucher'])->name('student.fee.download');
    Route::post('student/apply-leave', [StudentPortalController::class, 'applyLeave'])->name('student.leave.apply');
    Route::post('student/leaves/{studentLeave}/approve', [StudentPortalController::class, 'approveLeave'])->name('student.leave.approve');
    Route::post('student/leaves/{studentLeave}/reject', [StudentPortalController::class, 'rejectLeave'])->name('student.leave.reject');

    Route::get('teacher/dashboard', [TeacherPortalController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('teacher/salary-slip/{id}', [TeacherPortalController::class, 'downloadPaySlip'])->name('teacher.salary.download');
    Route::post('teacher/upload-outline', [TeacherPortalController::class, 'uploadOutline'])->name('teacher.outline.upload');
    Route::post('teacher/apply-leave', [TeacherPortalController::class, 'applyLeave'])->name('teacher.leave.apply');
    Route::post('teacher/approve-leave/{teacherLeave}', [TeacherPortalController::class, 'approveLeave'])->name('teacher.leave.approve');
    Route::post('teacher/punch-in', [TeacherPortalController::class, 'punchIn'])->name('teacher.punchIn');
    Route::post('teacher/punch-out', [TeacherPortalController::class, 'punchOut'])->name('teacher.punchOut');
    Route::post('teacher/test-marks/update', [TeacherPortalController::class, 'updateTestMarks'])->name('teacher.test-marks.update');
    Route::post('teacher/student-leaves/{studentLeave}/approve', [TeacherPortalController::class, 'approveStudentLeave'])->name('teacher.student-leave.approve');
    Route::post('teacher/student-leaves/{studentLeave}/reject', [TeacherPortalController::class, 'rejectStudentLeave'])->name('teacher.student-leave.reject');
    Route::get('teacher/{page}', [TeacherPortalController::class, 'page'])->where('page', 'profile|classes|incharge|coteachers|students|results|outlines|salary|attendance|tests|slips|stdleaves|leaves')->name('teacher.page');
    Route::put('teacher/profile', [TeacherPortalController::class, 'updateProfile'])->name('teacher.profile.update');
    Route::post('teacher/password', [TeacherPortalController::class, 'updatePassword'])->name('teacher.password.update');

    // Admin & Head Management Portal Routes
    Route::get('admin/dashboard', [AdminPortalController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('admin/salaries/{id}/payslip', [AdminPortalController::class, 'downloadPayslip'])->name('admin.salaries.payslip');
    Route::post('admin/leaves/student/{studentLeave}/approve', [AdminPortalController::class, 'approveStudentLeave'])->name('admin.student-leave.approve');
    Route::post('admin/leaves/student/{studentLeave}/reject', [AdminPortalController::class, 'rejectStudentLeave'])->name('admin.student-leave.reject');
    Route::post('admin/leaves/teacher/{teacherLeave}/approve', [AdminPortalController::class, 'approveTeacherLeave'])->name('admin.teacher-leave.approve');
    Route::post('admin/leaves/teacher/{teacherLeave}/reject', [AdminPortalController::class, 'rejectTeacherLeave'])->name('admin.teacher-leave.reject');
    Route::post('admin/salaries', [AdminPortalController::class, 'storeSalary'])->name('admin.salary.store');
    Route::put('admin/salaries/{salary}', [AdminPortalController::class, 'updateSalary'])->name('admin.salary.update');
    Route::delete('admin/salaries/{salary}', [AdminPortalController::class, 'destroySalary'])->name('admin.salary.destroy');
    Route::post('admin/fees', [AdminPortalController::class, 'storeFee'])->name('admin.fee.store');
    Route::get('admin/{page}', [AdminPortalController::class, 'page'])->where('page', 'dashboard|leaves|salaries|students|teachers|fees|attendance|academics|roles|users')->name('admin.page');

    Route::get('api/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('api/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('api/notifications/clear', [NotificationController::class, 'clearAll'])->name('notifications.clear');
    Route::post('api/announcements/publish', [NotificationController::class, 'publishAnnouncement'])->name('announcements.publish');

    Route::get('list', [StudentController::class, 'list']);
    Route::get('add-student', [StudentController::class, 'addStudentForm']);
    Route::post('add-student', [StudentController::class, 'addStudent']);
    Route::get('api/student-roll-info', [StudentController::class, 'getRollInfo']);
    Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('students/{student}/results/download', [StudentController::class, 'downloadResults'])->name('students.results.download');
    Route::post('students/{student}/subjects', [StudentController::class, 'updateStudentSubjects'])->name('students.subjects.update');
    Route::put('students/{student}', [StudentController::class, 'updateStudent'])->name('students.update');
    Route::delete('students/{student}', [StudentController::class, 'deleteStudent'])->name('students.destroy');
    Route::post('/logout', function () {session()->flush(); return redirect('/login');});
    Route::post('/profile/settings', [AuthController::class, 'updateSettings']);
    Route::get('/account/password', [AuthController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [AuthController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/account/password/dismiss', [AuthController::class, 'dismissPasswordPrompt'])->name('account.password.dismiss');

    Route::get('/teachers', [TeacherController::class, 'list']);
    Route::get('/add-teacher', [TeacherController::class, 'addTeacherForm']);
    Route::post('/add-teacher', [TeacherController::class, 'addTeacher']);
    Route::put('/teachers/{teacher}', [TeacherController::class, 'updateTeacher'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'deleteTeacher'])->name('teachers.destroy');

    Route::get('/courses', [CourseController::class, 'list']);
    Route::get('/add-course', [CourseController::class, 'addCourseForm']);
    Route::post('/add-course', [CourseController::class, 'addCourse']);
    Route::put('/courses/{course}', [CourseController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'deleteCourse'])->name('courses.destroy');

    Route::get('/sections', [SectionController::class, 'list']);
    Route::get('/add-section', [SectionController::class, 'addSectionForm']);
    Route::post('/add-section', [SectionController::class, 'addSection']);
    Route::put('/sections/{section}', [SectionController::class, 'updateSection'])->name('sections.update');
    Route::delete('/sections/{section}', [SectionController::class, 'deleteSection'])->name('sections.destroy');

    Route::get('/users', [UserController::class, 'list']);
    Route::put('/users/{user}', [UserController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'deleteUser'])->name('users.destroy');


Route::get('/subjects', [SubjectController::class, 'list'])->name('subjects.list');
Route::get('/add-subject', [SubjectController::class, 'addSubjectForm'])->name('subjects.add');
Route::post('/add-subject', [SubjectController::class, 'addSubject'])->name('subjects.store');
Route::delete('/subjects/{subject}', [SubjectController::class, 'deleteSubject'])->name('subjects.destroy');
Route::put('/subjects/{subject}', [SubjectController::class, 'updateSubject'])->name('subjects.update');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/assign-incharge', [RoleController::class, 'assignIncharge'])->name('roles.assign-incharge');
    Route::delete('/roles/incharges/{classIncharge}', [RoleController::class, 'removeIncharge'])->name('roles.incharges.destroy');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::put('/roles/users/{user}', [RoleController::class, 'updateUserRole'])->name('roles.users.update');
    Route::post('/teacher-access-requests/{teacherAccessRequest}/approve', [RoleController::class, 'approveRequest'])->name('teacher-access-requests.approve');
    Route::post('/teacher-access-requests/{teacherAccessRequest}/reject', [RoleController::class, 'rejectRequest'])->name('teacher-access-requests.reject');
});
Route::get('/results', [ResultController::class, 'list'])->name('results.list');
Route::get('/add-result', [ResultController::class, 'addResultForm'])->name('results.add');
Route::post('/add-result', [ResultController::class, 'addResult'])->name('results.store');
Route::put('/results/bulk', [ResultController::class, 'updateStudentResults'])->name('results.bulk-update');
Route::put('/results/{result}', [ResultController::class, 'updateResult'])->name('results.update');
Route::delete('/results/bulk', [ResultController::class, 'deleteStudentResults'])->name('results.bulk-destroy');
Route::delete('/results/{result}', [ResultController::class, 'deleteResult'])->name('results.destroy');


Route::post('/add-result/class', [ResultController::class, 'storeClassResults'])
    ->name('results.class.store');
Route::post('/add-result/student-result', [ResultController::class, 'storeSingleStudentResult'])
    ->name('results.student.store');
Route::get('/add-result/class', [ResultController::class, 'classResults'])
    ->name('results.class');

Route::get('/classes', [ClassController::class, 'list'])->name('classes.index');
Route::get('/add-class', [ClassController::class, 'create'])->name('classes.create');
Route::post('/add-class', [ClassController::class, 'store'])->name('classes.store');
Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');

Route::get('/arrangements', [ArrangementController::class, 'list'])->name('arrangements.index');
Route::get('/add-arrangement', [ArrangementController::class, 'create'])->name('arrangements.create');
Route::post('/add-arrangement', [ArrangementController::class, 'store'])->name('arrangements.store');
Route::put('/arrangements/{arrangement}', [ArrangementController::class, 'update'])->name('arrangements.update');
Route::delete('/arrangements/{arrangement}', [ArrangementController::class, 'destroy'])->name('arrangements.destroy');
Route::get('/roll-slips', [RollSlipController::class, 'list'])->name('roll-slips.index');
Route::get('/issue-roll-slips', [RollSlipController::class, 'create'])->name('roll-slips.create');
Route::post('/issue-roll-slips', [RollSlipController::class, 'store'])->name('roll-slips.store');
Route::get('/roll-slips/class-document', [RollSlipController::class, 'classDocument'])->name('roll-slips.class-document');
Route::get('/roll-slips/class-download', [RollSlipController::class, 'classDownload'])->name('roll-slips.class-download');
Route::get('/roll-slips/{rollSlip}', [RollSlipController::class, 'show'])->name('roll-slips.show');
Route::put('/roll-slips/{rollSlip}', [RollSlipController::class, 'update'])->name('roll-slips.update');
Route::delete('/roll-slips/{rollSlip}', [RollSlipController::class, 'destroy'])->name('roll-slips.destroy');
Route::get('/roll-slips/{rollSlip}/download', [RollSlipController::class, 'download'])->name('roll-slips.download');


Route::get('/fee-structure', [FeeController::class, 'index'])->name('fee-structure.index');
Route::post('/fee-structure/class', [FeeController::class, 'storeClassFee'])->name('fee-structure.class.store');
Route::put('/fee-structure/class/{feeStructure}', [FeeController::class, 'updateClassFee'])->name('fee-structure.class.update');
Route::delete('/fee-structure/class/{feeStructure}', [FeeController::class, 'destroyClassFee'])->name('fee-structure.class.destroy');
Route::put('/fee-structure/collection/{feeCollection}', [FeeController::class, 'updateFeeStatus'])->name('fee-structure.collection.update');
Route::get('/fee-structure/voucher/{feeCollection}', [FeeController::class, 'showVoucher'])->name('fee-structure.voucher.show');

Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::delete('/attendance/date', [AttendanceController::class, 'destroyDate'])->name('attendance.destroyDate');
Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

