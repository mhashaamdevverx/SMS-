<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Course;
use App\Models\FeeStructure;
use App\Models\FeeCollection;
use App\Models\TeacherSalary;
use App\Models\TeacherLeave;
use App\Models\StudentLeave;
use App\Models\Attendance;
use App\Models\TeacherAttendance;
use App\Models\Result;
use App\Models\RollSlip;
use App\Models\Arrangement;
use App\Models\Role;
use App\Models\ClassIncharge;
use App\Models\TeacherAccessRequest;
use App\Models\TeacherAssignment;
use App\Models\Permission;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminPortalController extends Controller
{
    /**
     * Get the authenticated admin user or fail gracefully.
     */
    private function getAdminUser(): User
    {
        $userId = session('user_id');
        if (!$userId) {
            redirect()->route('login')->send();
            exit;
        }

        $user = User::with(['role', 'classIncharge.classRecord', 'teacherRecord', 'studentRecord'])->find($userId);
        if (!$user) {
            redirect()->route('login')->send();
            exit;
        }

        $roleSlug = strtolower($user->role?->slug ?? '');
        $isAdmin = in_array($roleSlug, ['admin', 'super-admin', 'head', 'principal'], true) 
                   || (int)$user->role_id === 1 
                   || $user->isAdmin();

        if (!$isAdmin) {
            if ($roleSlug === 'student') {
                redirect()->route('student.dashboard')->with('error', 'Admin portal access required.')->send();
                exit;
            }
            if (in_array($roleSlug, ['teacher', 'class-incharge'], true)) {
                redirect()->route('teacher.dashboard')->with('error', 'Admin portal access required.')->send();
                exit;
            }
            redirect()->route('login')->with('error', 'Admin portal access required. Please log in with an administrator account.')->send();
            exit;
        }

        return $user;
    }

    /**
     * Display the Admin Portal Dashboard.
     */
    public function dashboard(Request $request)
    {
        return $this->page($request, 'dashboard');
    }

    /**
     * Handle page rendering for all Admin Portal pages.
     */
    public function page(Request $request, string $page = 'dashboard')
    {
        $currentUser = $this->getAdminUser();
        $today = now()->toDateString();

        // Common notification / pending counters for header & sidebar badges
        $pendingStudentLeavesCount = StudentLeave::whereIn('status', ['Pending', 'pending'])->count();
        $pendingTeacherLeavesCount = TeacherLeave::whereIn('status', ['Pending', 'pending'])->count();
        $pendingLeavesCount = $pendingStudentLeavesCount + $pendingTeacherLeavesCount;
        $pendingAccessRequestsCount = TeacherAccessRequest::where('status', 'pending')->count();

        $data = [
            'currentUser' => $currentUser,
            'currentPage' => $page,
            'pendingStudentLeavesCount' => $pendingStudentLeavesCount,
            'pendingTeacherLeavesCount' => $pendingTeacherLeavesCount,
            'pendingLeavesCount' => $pendingLeavesCount,
            'pendingAccessRequestsCount' => $pendingAccessRequestsCount,
        ];

        switch ($page) {
            case 'dashboard':
                $data['totalStudents'] = Student::count();
                $data['totalTeachers'] = Teacher::count();
                $data['totalClasses'] = Classes::count();
                $data['totalUsers'] = User::count();

                // Financial metrics
                $data['collectedFees'] = (float) FeeCollection::where('status', 'Paid')->sum('net_amount');
                $data['outstandingFees'] = (float) FeeCollection::whereIn('status', ['Pending', 'Overdue'])->sum('net_amount');
                $data['salariesPaid'] = (float) TeacherSalary::where('status', 'Paid')->sum('net_salary');
                $data['salariesPending'] = (float) TeacherSalary::where('status', 'Pending')->sum('net_salary');

                // Attendance overview today
                $stdAttendanceToday = Attendance::whereDate('date', $today)->get();
                $data['stdPresentToday'] = $stdAttendanceToday->where('status', 'present')->count();
                $data['stdTotalToday'] = $stdAttendanceToday->count();
                $data['stdAttendanceRate'] = $data['stdTotalToday'] > 0 
                    ? (int) round(($data['stdPresentToday'] / $data['stdTotalToday']) * 100) 
                    : 0;

                $tAttendanceToday = TeacherAttendance::whereDate('date', $today)->get();
                $data['tPresentToday'] = $tAttendanceToday->whereIn('status', ['Present', 'P', 'present'])->count();
                $data['tTotalToday'] = $tAttendanceToday->count();

                // Recent leaves for quick action card
                $data['recentStudentLeaves'] = StudentLeave::where('status', 'Pending')->latest()->take(5)->get();
                $data['recentTeacherLeaves'] = TeacherLeave::where('status', 'Pending')->latest()->take(5)->get();
                $data['recentTeachers'] = Teacher::latest()->take(5)->get();
                $data['recentStudents'] = Student::latest()->take(5)->get();
                break;

            case 'leaves':
                // Student & Teacher Leaves with search & filters
                $filterStatus = $request->input('status', 'all');
                $searchQuery = $request->input('search');

                $sLeaveQuery = StudentLeave::query()->latest('leave_date');
                $tLeaveQuery = TeacherLeave::query()->latest('leave_date');

                if ($filterStatus !== 'all') {
                    $sLeaveQuery->where('status', ucfirst($filterStatus));
                    $tLeaveQuery->where('status', ucfirst($filterStatus));
                }

                if ($searchQuery) {
                    $sLeaveQuery->where(function($q) use ($searchQuery) {
                        $q->where('student_name', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('student_class', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('reason', 'LIKE', "%{$searchQuery}%");
                    });

                    $tLeaveQuery->where(function($q) use ($searchQuery) {
                        $q->where('teacher_name', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('reason', 'LIKE', "%{$searchQuery}%");
                    });
                }

                $data['studentLeaves'] = $sLeaveQuery->paginate(10, ['*'], 'student_page')->withQueryString();
                $data['teacherLeaves'] = $tLeaveQuery->paginate(10, ['*'], 'teacher_page')->withQueryString();
                $data['filterStatus'] = $filterStatus;
                $data['searchQuery'] = $searchQuery;
                break;

            case 'salaries':
                // Salary management for Teachers
                $selectedMonth = $request->input('month', now()->format('Y-m'));
                $searchTeacher = $request->input('search');

                $teachers = Teacher::with('user')->orderBy('teacher_name')->get();
                $salariesQuery = TeacherSalary::with('teacher')->latest('id');

                if ($selectedMonth) {
                    $salariesQuery->where('month', 'LIKE', "%{$selectedMonth}%");
                }
                if ($searchTeacher) {
                    $salariesQuery->whereHas('teacher', function($q) use ($searchTeacher) {
                        $q->where('teacher_name', 'LIKE', "%{$searchTeacher}%");
                    });
                }

                $data['teachers'] = $teachers;
                // Summary calculations for salaries on entire filtered set
                $data['totalDeclaredSalaries'] = (float) (clone $salariesQuery)->sum('net_salary');
                $data['totalPaidSalaries'] = (float) (clone $salariesQuery)->where('status', 'Paid')->sum('net_salary');
                $data['totalPendingSalaries'] = (float) (clone $salariesQuery)->where('status', 'Pending')->sum('net_salary');
                $data['salaries'] = $salariesQuery->paginate(10, ['*'], 'salary_page')->withQueryString();
                $data['selectedMonth'] = $selectedMonth;
                $data['searchTeacher'] = $searchTeacher;
                break;

            case 'students':
                // Complete Student Directory & Profile Viewer
                $selectedClass = $request->input('class');
                $selectedSection = $request->input('section');
                $searchQuery = $request->input('search');

                $studentsQuery = Student::with(['user', 'feeCollections'])->orderBy('class')->orderBy('std_id');

                if ($selectedClass) {
                    $studentsQuery->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($selectedClass))]);
                }
                if ($selectedSection) {
                    $studentsQuery->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($selectedSection))]);
                }
                if ($searchQuery) {
                    $studentsQuery->where(function($q) use ($searchQuery) {
                        $q->where('name', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('std_id', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('father_name', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('email', 'LIKE', "%{$searchQuery}%");
                    });
                }

                $data['students'] = $studentsQuery->paginate(10, ['*'], 'student_page')->withQueryString();
                $data['classes'] = Classes::orderBy('class_name')->get();
                $data['allClasses'] = $data['classes'];
                $data['sections'] = Section::orderBy('section_name')->get();
                $data['allSections'] = $data['sections'];
                $data['feeStructures'] = \App\Models\FeeStructure::all();
                $data['selectedClass'] = $selectedClass;
                $data['selectedSection'] = $selectedSection;
                $data['searchQuery'] = $searchQuery;
                break;

            case 'teachers':
                // Complete Teacher Directory & Profile Viewer
                $selectedClass = $request->input('class');
                $selectedSection = $request->input('section');
                $selectedSubject = $request->input('subject');
                $searchQuery = $request->input('search');

                $teachersQuery = Teacher::with(['user', 'assignments', 'salaries' => function($q) {
                    $q->latest('id');
                }])->orderBy('teacher_name');

                if ($selectedClass) {
                    $teachersQuery->where(function($q) use ($selectedClass) {
                        $q->whereHas('assignments', function($sq) use ($selectedClass) {
                            $sq->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($selectedClass))]);
                        })->orWhereRaw('LOWER(TRIM(course)) LIKE ?', ['%' . strtolower(trim($selectedClass)) . '%']);
                    });
                }
                if ($selectedSection) {
                    $teachersQuery->whereHas('assignments', function($sq) use ($selectedSection) {
                        $sq->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($selectedSection))]);
                    });
                }
                if ($selectedSubject) {
                    $teachersQuery->where(function($q) use ($selectedSubject) {
                        $q->where('course', 'LIKE', "%{$selectedSubject}%")
                          ->orWhereHas('assignments', function($sq) use ($selectedSubject) {
                              $sq->whereRaw('LOWER(TRIM(subject_or_course)) = ?', [strtolower(trim($selectedSubject))]);
                          });
                    });
                }
                if ($searchQuery) {
                    $teachersQuery->where(function($q) use ($searchQuery) {
                        $q->where('teacher_name', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('teacher_email', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('teacher_phone', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('course', 'LIKE', "%{$searchQuery}%");
                    });
                }

                $data['teachers'] = $teachersQuery->paginate(10, ['*'], 'teacher_page')->withQueryString();
                $data['allClasses'] = Classes::orderBy('class_name')->get();
                $data['allSections'] = Section::orderBy('section_name')->get();
                $data['allSubjects'] = Subject::orderBy('subject_name')->get();
                $data['selectedClass'] = $selectedClass;
                $data['selectedSection'] = $selectedSection;
                $data['selectedSubject'] = $selectedSubject;
                $data['searchQuery'] = $searchQuery;
                break;

            case 'fees':
                // Student Fees Management
                $selectedClass = $request->input('class');
                $selectedSection = $request->input('section');
                $searchQuery = $request->input('search');

                $data['feeStructures'] = FeeStructure::latest('id')->get();
                
                $feeCollectionsQuery = FeeCollection::with('student')->latest('id');
                if ($selectedClass) {
                    $feeCollectionsQuery->whereHas('student', function($q) use ($selectedClass) {
                        $q->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($selectedClass))]);
                    });
                }
                if ($selectedSection) {
                    $feeCollectionsQuery->whereHas('student', function($q) use ($selectedSection) {
                        $q->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($selectedSection))]);
                    });
                }
                if ($searchQuery) {
                    $feeCollectionsQuery->where(function($q) use ($searchQuery) {
                        $q->where('voucher_no', 'LIKE', "%{$searchQuery}%")
                          ->orWhere('month', 'LIKE', "%{$searchQuery}%")
                          ->orWhereHas('student', function($sq) use ($searchQuery) {
                              $sq->where('name', 'LIKE', "%{$searchQuery}%")
                                ->orWhere('std_id', 'LIKE', "%{$searchQuery}%");
                          });
                    });
                }

                $data['feeCollections'] = $feeCollectionsQuery->paginate(10, ['*'], 'fee_page')->withQueryString();
                $data['classes'] = Classes::orderBy('class_name')->get();
                $data['sections'] = Section::orderBy('section_name')->get();
                $data['studentsList'] = Student::orderBy('name')->get();
                $data['selectedClass'] = $selectedClass;
                $data['selectedSection'] = $selectedSection;
                $data['searchQuery'] = $searchQuery;
                break;

            case 'attendance':
                // Consolidated Attendance Viewer
                $selectedDate = $request->input('date', $today);
                $selectedClass = $request->input('class');
                $selectedSection = $request->input('section');

                $studentAttendanceQuery = Attendance::whereDate('date', $selectedDate);
                if ($selectedClass) {
                    $studentAttendanceQuery->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($selectedClass))]);
                }
                if ($selectedSection) {
                    $studentAttendanceQuery->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($selectedSection))]);
                }

                $data['studentAttendance'] = $studentAttendanceQuery->get();
                $data['teacherAttendance'] = TeacherAttendance::with('teacher')
                    ->whereDate('date', $selectedDate)
                    ->get();
                
                $data['teachersWithoutAttendance'] = Teacher::whereNotIn('id', $data['teacherAttendance']->pluck('teacher_id'))->get();
                $data['classes'] = Classes::orderBy('class_name')->get();
                $data['sections'] = Section::orderBy('section_name')->get();
                $data['selectedDate'] = $selectedDate;
                $data['selectedClass'] = $selectedClass;
                $data['selectedSection'] = $selectedSection;
                break;

            case 'academics':
                // Academics hub: Classes, Sections, Subjects, Courses, Results, Roll Slips, Arrangements
                $data['classes'] = Classes::get();
                $data['sections'] = Section::get();
                $data['subjects'] = Subject::get();
                $data['courses'] = Course::get();
                $data['totalResults'] = Result::count();
                $data['totalRollSlips'] = RollSlip::count();
                $data['totalArrangements'] = Arrangement::count();
                break;

            case 'roles':
                // Roles, Incharges, Access requests, and User Role Assignment
                $data['roles'] = Role::with(['permissions', 'users'])->withCount('users')->orderByDesc('is_system')->orderBy('name')->get();
                $data['classIncharges'] = ClassIncharge::with(['user', 'teacher', 'classRecord'])->paginate(10, ['*'], 'incharges_page')->withQueryString();
                $data['accessRequests'] = TeacherAccessRequest::with(['user', 'teacher'])->latest()->paginate(10, ['*'], 'request_page')->withQueryString();
                $data['teachers'] = Teacher::orderBy('teacher_name')->get();
                $data['classes'] = Classes::orderBy('class_no')->orderBy('class_name')->get();
                $data['allUsers'] = User::with(['role', 'classIncharge.classRecord'])->orderBy('name')->paginate(10, ['*'], 'users_page')->withQueryString();
                $data['totalUsersCount'] = User::count();
                $data['permissionGroups'] = Permission::orderBy('label')->get()->groupBy('module');
                break;

            case 'users':
                // System Users management
                $data['users'] = User::with('role')->latest('id')->paginate(10)->withQueryString();
                $data['roles'] = Role::get();
                break;
        }

        return view('admin.portal', $data);
    }

    // ==========================================
    // LEAVE APPROVAL HANDLERS
    // ==========================================

    public function approveStudentLeave(Request $request, StudentLeave $studentLeave)
    {
        $admin = $this->getAdminUser();

        $studentLeave->update([
            'status' => 'Approved',
            'approved_by' => 'Admin / Head Panel (' . $admin->name . ')',
        ]);

        // Sync to daily Attendance
        $statusStr = match (strtoupper($studentLeave->leave_type ?? '')) {
            'SICK LEAVE', 'SL' => 'SL',
            'CASUAL LEAVE', 'CL' => 'CL',
            default => 'L',
        };

        $leaveDate = $studentLeave->leave_date->format('Y-m-d');
        Attendance::updateOrCreate(
            [
                'student_id' => $studentLeave->student_id,
                'date' => $leaveDate,
            ],
            [
                'status' => $statusStr,
                'class_name' => $studentLeave->student_class,
                'section_name' => $studentLeave->student_section,
                'remarks' => "Approved Leave ({$studentLeave->leave_type}) by Admin {$admin->name}",
            ]
        );

        // Send Notification to student and responsible In-Charge Teacher
        AppNotification::notifyStudentLeaveDecision($studentLeave, 'Approved', null, $admin);

        return back()->with('success', "Student leave application for {$studentLeave->student_name} approved successfully!");
    }

    public function rejectStudentLeave(Request $request, StudentLeave $studentLeave)
    {
        $admin = $this->getAdminUser();

        $studentLeave->update([
            'status' => 'Rejected',
            'approved_by' => 'Admin / Head Panel (' . $admin->name . ')',
        ]);

        // Send Notification to student and responsible In-Charge Teacher
        AppNotification::notifyStudentLeaveDecision($studentLeave, 'Rejected', $request->input('remarks'), $admin);

        return back()->with('success', "Student leave application for {$studentLeave->student_name} rejected.");
    }

    public function approveTeacherLeave(Request $request, TeacherLeave $teacherLeave)
    {
        $admin = $this->getAdminUser();

        $teacherLeave->update([
            'status' => 'Approved',
            'approved_by' => 'Admin / Head Panel (' . $admin->name . ')',
        ]);

        // Sync to Teacher Attendance
        $leaveDate = $teacherLeave->leave_date ? Carbon::parse($teacherLeave->leave_date)->format('Y-m-d') : now()->toDateString();
        TeacherAttendance::updateOrCreate(
            [
                'teacher_id' => $teacherLeave->teacher_id,
                'date' => $leaveDate,
            ],
            [
                'status' => 'Approved Leave',
                'remarks' => "Approved Leave ({$teacherLeave->leave_type}) by Admin {$admin->name}",
            ]
        );

        // Send notification to teacher
        AppNotification::notifyTeacherLeaveDecision($teacherLeave, 'Approved', null, $admin);

        return back()->with('success', "Teacher leave application for {$teacherLeave->teacher_name} approved successfully!");
    }

    public function rejectTeacherLeave(Request $request, TeacherLeave $teacherLeave)
    {
        $admin = $this->getAdminUser();

        $teacherLeave->update([
            'status' => 'Rejected',
            'approved_by' => 'Admin / Head Panel (' . $admin->name . ')',
        ]);

        // Send notification to teacher
        AppNotification::notifyTeacherLeaveDecision($teacherLeave, 'Rejected', $request->input('remarks'), $admin);

        return back()->with('success', "Teacher leave application for {$teacherLeave->teacher_name} rejected.");
    }

    // ==========================================
    // TEACHER SALARY DECLARATION & MANAGEMENT
    // ==========================================

    public function storeSalary(Request $request)
    {
        $this->getAdminUser();

        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'month' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'required|in:Paid,Pending,Unpaid',
            'paid_date' => 'nullable|date',
        ]);

        $basic = (float) $validated['basic_salary'];
        $allowances = (float) ($validated['allowances'] ?? 0);
        $deductions = (float) ($validated['deductions'] ?? 0);
        $netSalary = max(0, $basic + $allowances - $deductions);

        $salary = TeacherSalary::updateOrCreate(
            [
                'teacher_id' => $validated['teacher_id'],
                'month' => $validated['month'],
            ],
            [
                'basic_salary' => $basic,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'net_salary' => $netSalary,
                'status' => $validated['status'],
                'paid_date' => $validated['status'] === 'Paid' ? ($validated['paid_date'] ?? now()->toDateString()) : null,
            ]
        );

        $teacher = Teacher::find($validated['teacher_id']);
        if ($teacher) {
            $targetUserId = $teacher->login_user_id ?: $teacher->user_id;
            if ($targetUserId) {
                AppNotification::sendToUser(
                    $targetUserId,
                    "Salary Declared for {$validated['month']} 💵",
                    "Your salary for {$validated['month']} has been set to Rs " . number_format($netSalary, 2) . " (Status: {$validated['status']}).",
                    'info',
                    '/teacher/salary',
                    'bi-cash-coin'
                );
            }
        }

        return back()->with('success', "Salary declared/updated successfully for {$teacher->teacher_name} ({$validated['month']}). Net Salary: Rs " . number_format($netSalary, 2));
    }

    public function updateSalary(Request $request, TeacherSalary $salary)
    {
        $this->getAdminUser();

        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'required|in:Paid,Pending,Unpaid',
            'paid_date' => 'nullable|date',
        ]);

        $basic = (float) $validated['basic_salary'];
        $allowances = (float) ($validated['allowances'] ?? 0);
        $deductions = (float) ($validated['deductions'] ?? 0);
        $netSalary = max(0, $basic + $allowances - $deductions);

        $salary->update([
            'basic_salary' => $basic,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'status' => $validated['status'],
            'paid_date' => $validated['status'] === 'Paid' ? ($validated['paid_date'] ?? now()->toDateString()) : null,
        ]);

        return back()->with('success', "Salary record #{$salary->id} updated successfully!");
    }

    public function destroySalary(TeacherSalary $salary)
    {
        $this->getAdminUser();
        $salary->delete();

        return back()->with('success', "Salary record deleted successfully!");
    }

    public function downloadPayslip($id)
    {
        $this->getAdminUser();
        $salary = TeacherSalary::with('teacher')->findOrFail($id);

        return view('teacher.payslip-print', [
            'salary' => $salary,
            'teacher' => $salary->teacher,
        ]);
    }

    // ==========================================
    // STUDENT FEES MANAGEMENT
    // ==========================================

    public function storeFee(Request $request)
    {
        $this->getAdminUser();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'month' => 'required|string',
            'voucher_no' => 'nullable|string|max:50',
            'father_name' => 'nullable|string|max:100',
            'mobile_no' => 'nullable|string|max:30',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'tuition_fee' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'arrears' => 'nullable|numeric|min:0',
            'admission_fee' => 'nullable|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:Paid,Pending,Overdue',
            'paid_date' => 'nullable|date',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $tuition = (float) ($validated['tuition_fee'] ?? $validated['amount'] ?? 0);
        $arrears = (float) ($validated['arrears'] ?? 0);
        $admission = (float) ($validated['admission_fee'] ?? 0);
        $lateFee = (float) ($validated['late_fee'] ?? 500);
        $discount = (float) ($validated['discount_amount'] ?? 0);
        $gross = $tuition + $arrears + $admission;
        $net = max(0, $gross - $discount);

        $defaultVoucherNo = 'V-' . ($student->std_id ?: $student->id) . '-' . date('my') . '-' . strtoupper(substr(uniqid(), -4));
        $voucherNo = !empty($validated['voucher_no']) ? trim($validated['voucher_no']) : $defaultVoucherNo;

        $fatherName = !empty($validated['father_name']) ? trim($validated['father_name']) : ($student->father_name ?? '');
        $mobileNo = !empty($validated['mobile_no']) ? trim($validated['mobile_no']) : ($student->phone_no ?: ($student->emergency_phone ?? ''));
        $issueDate = !empty($validated['issue_date']) ? $validated['issue_date'] : date('Y-m-d');
        $dueDate = !empty($validated['due_date']) ? $validated['due_date'] : date('Y-m-10');

        if (!empty($fatherName) && empty($student->father_name)) {
            $student->update(['father_name' => $fatherName]);
        }

        $feeCollection = FeeCollection::updateOrCreate(
            [
                'student_id' => $student->id,
                'month' => $validated['month'],
            ],
            [
                'user_id' => session('user_id'),
                'voucher_no' => $voucherNo,
                'student_name' => $student->name,
                'father_name' => $fatherName,
                'std_id' => $student->std_id,
                'student_class' => $student->class,
                'student_section' => $student->section,
                'mobile_no' => $mobileNo,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'tuition_fee' => $tuition,
                'arrears' => $arrears,
                'admission_fee' => $admission,
                'late_fee' => $lateFee,
                'gross_amount' => $gross,
                'discount' => $discount,
                'net_amount' => $net,
                'status' => $validated['status'],
                'payment_date' => $validated['status'] === 'Paid' ? ($validated['paid_date'] ?? date('Y-m-d')) : null,
                'payment_method' => $validated['status'] === 'Paid' ? 'Cash' : null,
            ]
        );

        $targetUserId = $student->login_user_id ?: $student->user_id;
        if ($targetUserId) {
            AppNotification::sendToUser(
                $targetUserId,
                "Fee Voucher Generated 💳",
                "Fee voucher for month {$validated['month']} is issued. Net Amount: Rs " . number_format($net, 2) . " (Status: {$validated['status']}).",
                'info',
                '/student/fees',
                'bi-receipt'
            );
        }

        return back()->with('success', "Fee voucher {$voucherNo} generated successfully for {$student->name} ({$validated['month']}). Total: Rs " . number_format($net, 2));
    }

    public function updateFeeStatus(Request $request, FeeCollection $feeCollection)
    {
        $this->getAdminUser();

        $validated = $request->validate([
            'status' => 'required|in:Paid,Pending,Overdue',
            'paid_date' => 'nullable|date',
        ]);

        $feeCollection->update([
            'status' => $validated['status'],
            'paid_date' => $validated['status'] === 'Paid' ? ($validated['paid_date'] ?? now()->toDateString()) : null,
        ]);

        return back()->with('success', "Fee voucher {$feeCollection->voucher_no} status updated to {$validated['status']}.");
    }
}
