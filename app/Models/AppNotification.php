<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'sender_id',
        'title',
        'message',
        'type',
        'icon',
        'action_url',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Normalize any class string input (e.g. "TEN", "10", "Class 10", "10 (Red)", "Pre-KG - Red")
     * Returns: [$canonicalClassName, $extractedSection, $searchVariants]
     */
    public static function normalizeClass($className, $sectionName = null)
    {
        $raw = trim((string)$className);
        $extractedSec = null;

        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $raw, $m)) {
            $raw = trim($m[1]);
            $extractedSec = trim($m[2]);
        } elseif (preg_match('/^(.*?)\s*[-–—]\s*(Red|Green|Blue|Yellow|White|A|B|C|D)$/i', $raw, $m)) {
            $raw = trim($m[1]);
            $extractedSec = trim($m[2]);
        }

        $clean = strtolower(trim(preg_replace('/^(class|grade)\s+/i', '', $raw)));

        $map = [
            'pre-kg' => 'Pre-KG', 'pre kg' => 'Pre-KG', 'prekg' => 'Pre-KG', 'nursery' => 'Pre-KG', 'playgroup' => 'Pre-KG',
            'kg' => 'KG', 'kindergarten' => 'KG',
            '1' => 'ONE', '1st' => 'ONE', 'one' => 'ONE', 'i' => 'ONE',
            '2' => 'TWO', '2nd' => 'TWO', 'two' => 'TWO', 'ii' => 'TWO',
            '3' => 'THREE', '3rd' => 'THREE', 'three' => 'THREE', 'iii' => 'THREE',
            '4' => 'FOUR', '4th' => 'FOUR', 'four' => 'FOUR', 'iv' => 'FOUR',
            '5' => 'FIVE', '5th' => 'FIVE', 'five' => 'FIVE', 'v' => 'FIVE',
            '6' => 'SIX', '6th' => 'SIX', 'six' => 'SIX', 'vi' => 'SIX',
            '7' => 'SEVEN', '7th' => 'SEVEN', 'seven' => 'SEVEN', 'vii' => 'SEVEN',
            '8' => 'EIGHT', '8th' => 'EIGHT', 'eight' => 'EIGHT', 'viii' => 'EIGHT',
            '9' => 'NINE', '9th' => 'NINE', 'nine' => 'NINE', 'ix' => 'NINE',
            '10' => 'TEN', '10th' => 'TEN', 'ten' => 'TEN', 'x' => 'TEN',
            'a' => 'A',
        ];

        $canonical = $map[$clean] ?? $raw;

        $variants = [$canonical, $raw, $clean];
        foreach ($map as $alias => $std) {
            if (strtolower($std) === strtolower($canonical)) {
                $variants[] = $alias;
                $variants[] = "Class " . $alias;
                $variants[] = "Class " . $std;
                $variants[] = "Grade " . $alias;
            }
        }
        $variants = array_values(array_unique(array_filter(array_map('trim', $variants))));

        $finalSection = $sectionName ? trim((string)$sectionName) : $extractedSec;
        return [$canonical, $finalSection, $variants];
    }

    /**
     * Resolve all User IDs for Students in a class/section
     */
    public static function resolveStudentsForClass($className, $sectionName = null)
    {
        [$canonical, $sec, $variants] = self::normalizeClass($className, $sectionName);

        $query = Student::query()->where(function($q) use ($variants) {
            foreach ($variants as $v) {
                $q->orWhereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($v))]);
            }
        });

        if ($sec && strtolower($sec) !== 'all' && strtolower($sec) !== 'all sections') {
            $query->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($sec))]);
        }

        $students = $query->get();
        $userIds = [];

        foreach ($students as $student) {
            if (!empty($student->login_user_id)) {
                $userIds[] = (int)$student->login_user_id;
            }
            if (!empty($student->email)) {
                $u = User::where('email', $student->email)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $u);
            }
            if (!empty($student->registration_no)) {
                $u = User::where('username', $student->registration_no)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $u);
            }
            if (!empty($student->user_id) && !in_array((int)$student->user_id, [1, 2])) {
                $userIds[] = (int)$student->user_id;
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $userIds))));
    }

    /**
     * Resolve all User IDs for In-Charge Teachers of a class/section
     */
    public static function resolveInchargeForClass($className, $sectionName = null)
    {
        [$canonical, $sec, $variants] = self::normalizeClass($className, $sectionName);

        $query = ClassIncharge::with(['teacher', 'user', 'classRecord'])
            ->whereHas('classRecord', function($q) use ($variants, $sec) {
                $q->where(function($sub) use ($variants) {
                    foreach ($variants as $v) {
                        $sub->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($v))]);
                    }
                });
                if ($sec && strtolower($sec) !== 'all' && strtolower($sec) !== 'all sections') {
                    $q->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($sec))]);
                }
            });

        $incharges = $query->get();

        // Fallback: if section was specified but no specific section incharge row exists, match class incharge
        if ($incharges->isEmpty() && $sec) {
            $incharges = ClassIncharge::with(['teacher', 'user', 'classRecord'])
                ->whereHas('classRecord', function($q) use ($variants) {
                    $q->where(function($sub) use ($variants) {
                        foreach ($variants as $v) {
                            $sub->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($v))]);
                        }
                    });
                })->get();
        }

        $userIds = [];
        foreach ($incharges as $ic) {
            if (!empty($ic->teacher?->login_user_id)) {
                $userIds[] = (int)$ic->teacher->login_user_id;
            }
            if (!empty($ic->teacher?->user_id) && !in_array((int)$ic->teacher->user_id, [1, 2])) {
                $userIds[] = (int)$ic->teacher->user_id;
            }
            if (!empty($ic->user_id) && !in_array((int)$ic->user_id, [1, 2])) {
                $userIds[] = (int)$ic->user_id;
            }
            if (!empty($ic->teacher?->teacher_email)) {
                $u = User::where('email', $ic->teacher->teacher_email)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $u);
            }
            if (!empty($ic->teacher?->teacher_name)) {
                $uNames = User::where('name', $ic->teacher->teacher_name)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $uNames);
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $userIds))));
    }

    /**
     * Resolve all User IDs for Teachers assigned to teach a class/section/subject
     */
    public static function resolveTeachersForClassSubject($className, $sectionName = null, $subjectName = null)
    {
        [$canonical, $sec, $variants] = self::normalizeClass($className, $sectionName);

        $query = TeacherAssignment::with('teacher')->where(function($q) use ($variants) {
            foreach ($variants as $v) {
                $q->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($v))]);
            }
        });

        if ($sec && strtolower($sec) !== 'all' && strtolower($sec) !== 'all sections') {
            $query->where(function($q) use ($sec) {
                $q->whereRaw('LOWER(TRIM(section_name)) = ?', [strtolower(trim($sec))])
                  ->orWhereNull('section_name')
                  ->orWhere('section_name', '');
            });
        }

        if ($subjectName) {
            $query->whereRaw('LOWER(TRIM(subject_or_course)) = ?', [strtolower(trim($subjectName))]);
        }

        $assignments = $query->get();
        $userIds = [];

        foreach ($assignments as $assign) {
            if (!empty($assign->teacher?->login_user_id)) {
                $userIds[] = (int)$assign->teacher->login_user_id;
            }
            if (!empty($assign->teacher?->user_id) && !in_array((int)$assign->teacher->user_id, [1, 2])) {
                $userIds[] = (int)$assign->teacher->user_id;
            }
            if (!empty($assign->teacher?->teacher_email)) {
                $u = User::where('email', $assign->teacher->teacher_email)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $u);
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $userIds))));
    }

    /**
     * Resolve all Administrator User IDs
     */
    public static function resolveAdmins()
    {
        $adminUserIds = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['admin', 'head', 'principal', 'super-admin']);
        })->orWhere('role_id', 1)->pluck('id')->toArray();

        return array_values(array_unique(array_filter(array_map('intval', $adminUserIds))));
    }

    /**
     * Send notification to a specific user_id and all matching alias accounts for that user
     */
    public static function sendToUser($userId, $title, $message, $type = 'general', $actionUrl = null, $icon = null, $senderId = null)
    {
        if (!$userId) return null;

        $targetUser = User::find($userId);
        $userIds = [(int)$userId];

        if ($targetUser) {
            $related = User::where(function($q) use ($targetUser) {
                if (!empty($targetUser->email)) $q->orWhere('email', $targetUser->email);
                if (!empty($targetUser->username)) $q->orWhere('username', $targetUser->username);
            })->pluck('id')->toArray();

            $userIds = array_unique(array_merge($userIds, array_map('intval', $related)));
        }

        $created = null;
        $activeSenderId = $senderId !== null ? (int)$senderId : (session('user_id') ? (int)session('user_id') : (auth()->check() ? (int)auth()->id() : null));

        foreach ($userIds as $uid) {
            // Do not send notification to oneself
            if ($activeSenderId && (int)$uid === (int)$activeSenderId) {
                continue;
            }

            // Deduplication: prevent duplicate notification within 60 seconds
            $exists = self::where('user_id', $uid)
                ->where('title', $title)
                ->where('message', $message)
                ->where('created_at', '>=', now()->subSeconds(60))
                ->first();

            if (!$exists) {
                $created = self::create([
                    'user_id' => $uid,
                    'sender_id' => $activeSenderId,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'icon' => $icon ?: 'bi-bell-fill',
                    'action_url' => $actionUrl,
                    'is_read' => false,
                ]);
            }
        }
        return $created;
    }

    /**
     * Send notification to all students in a class / section
     */
    public static function sendToClassStudents($className, $title, $message, $type = 'general', $actionUrl = null, $icon = null, $senderId = null, $sectionName = null)
    {
        $targetUserIds = self::resolveStudentsForClass($className, $sectionName);
        foreach ($targetUserIds as $uid) {
            self::sendToUser($uid, $title, $message, $type, $actionUrl, $icon, $senderId);
        }
    }

    /**
     * Send notification to designated Class In-Charge teacher(s)
     */
    public static function sendToClassIncharge($className, $title, $message, $type = 'general', $actionUrl = null, $icon = null, $senderId = null, $sectionName = null)
    {
        $inchargeUserIds = self::resolveInchargeForClass($className, $sectionName);
        foreach ($inchargeUserIds as $uid) {
            self::sendToUser($uid, $title, $message, $type, $actionUrl, $icon, $senderId);
        }
    }

    /**
     * Send notification to all Administrators and Heads
     */
    public static function sendToAdmins($title, $message, $type = 'general', $actionUrl = null, $icon = null, $senderId = null)
    {
        $adminUserIds = self::resolveAdmins();
        $activeSenderId = $senderId !== null ? (int)$senderId : (session('user_id') ? (int)session('user_id') : null);

        foreach ($adminUserIds as $uid) {
            if ($activeSenderId && (int)$uid === (int)$activeSenderId) {
                continue;
            }
            self::sendToUser(
                $uid,
                $title,
                $message,
                $type,
                $actionUrl ?: route('admin.page', 'leaves'),
                $icon ?: 'bi-shield-check',
                $senderId
            );
        }
    }

    /**
     * Send notification to the student applicant when their leave is approved or rejected
     */
    public static function sendToStudentLeaveApplicant($studentLeave, $title, $message, $type = 'leave', $actionUrl = null, $icon = null, $senderId = null)
    {
        if (!$studentLeave) return;

        $targetUserIds = [];
        $activeSenderId = $senderId !== null ? (int)$senderId : (session('user_id') ? (int)session('user_id') : null);

        if (!empty($studentLeave->user_id)) {
            $targetUserIds[] = (int)$studentLeave->user_id;
        }

        $student = $studentLeave->student ?: Student::find($studentLeave->student_id);
        if (!$student && !empty($studentLeave->std_id)) {
            $student = Student::where('std_id', $studentLeave->std_id)->first();
        }
        if (!$student && !empty($studentLeave->student_name)) {
            $student = Student::where('name', $studentLeave->student_name)->first();
        }

        if ($student) {
            if (!empty($student->login_user_id)) {
                $targetUserIds[] = (int)$student->login_user_id;
            }
            if (!empty($student->email)) {
                $u = User::where('email', $student->email)->pluck('id')->toArray();
                $targetUserIds = array_merge($targetUserIds, $u);
            }
            if (!empty($student->registration_no)) {
                $uReg = User::where('username', $student->registration_no)->pluck('id')->toArray();
                $targetUserIds = array_merge($targetUserIds, $uReg);
            }
            if (!empty($student->name)) {
                $uNames = User::where('name', $student->name)->pluck('id')->toArray();
                $targetUserIds = array_merge($targetUserIds, $uNames);
            }
            if (!empty($student->user_id) && (!$activeSenderId || (int)$student->user_id !== (int)$activeSenderId)) {
                $targetUserIds[] = (int)$student->user_id;
            }
        }

        $targetUserIds = array_values(array_unique(array_filter(array_map('intval', $targetUserIds))));
        foreach ($targetUserIds as $uid) {
            if ($activeSenderId && (int)$uid === (int)$activeSenderId) {
                continue;
            }
            self::sendToUser(
                $uid,
                $title,
                $message,
                $type,
                $actionUrl ?: route('student.page', 'leaves'),
                $icon ?: 'bi-check-circle-fill',
                $senderId
            );
        }
    }

    /**
     * Send notification to the teacher applicant when their leave is approved or rejected
     */
    public static function sendToTeacherLeaveApplicant($teacherLeave, $title, $message, $type = 'leave', $actionUrl = null, $icon = null, $senderId = null)
    {
        if (!$teacherLeave) return;

        $targetUserIds = [];
        $activeSenderId = $senderId !== null ? (int)$senderId : (session('user_id') ? (int)session('user_id') : null);

        if (!empty($teacherLeave->user_id)) {
            $targetUserIds[] = (int)$teacherLeave->user_id;
        }

        $teacher = $teacherLeave->teacher ?: Teacher::find($teacherLeave->teacher_id);
        if (!$teacher && !empty($teacherLeave->teacher_name)) {
            $teacher = Teacher::where('teacher_name', $teacherLeave->teacher_name)->first();
        }

        if ($teacher) {
            if (!empty($teacher->login_user_id)) {
                $targetUserIds[] = (int)$teacher->login_user_id;
            }
            if (!empty($teacher->teacher_email)) {
                $u = User::where('email', $teacher->teacher_email)->pluck('id')->toArray();
                $targetUserIds = array_merge($targetUserIds, $u);
            }
            if (!empty($teacher->teacher_name)) {
                $uNames = User::where('name', $teacher->teacher_name)->pluck('id')->toArray();
                $targetUserIds = array_merge($targetUserIds, $uNames);
            }
            if (!empty($teacher->user_id) && (!$activeSenderId || (int)$teacher->user_id !== (int)$activeSenderId)) {
                $targetUserIds[] = (int)$teacher->user_id;
            }
        }

        $targetUserIds = array_values(array_unique(array_filter(array_map('intval', $targetUserIds))));
        foreach ($targetUserIds as $uid) {
            if ($activeSenderId && (int)$uid === (int)$activeSenderId) {
                continue;
            }
            self::sendToUser(
                $uid,
                $title,
                $message,
                $type,
                $actionUrl ?: route('teacher.page', 'attendance'),
                $icon ?: 'bi-check-circle-fill',
                $senderId
            );
        }
    }

    // =========================================================================
    // HIGH-LEVEL CENTRALIZED WORKFLOW METHODS
    // =========================================================================

    /**
     * Workflow 5.A: Student submits leave request
     * Notifies: In-Charge Teacher of that class/section + School Admin
     */
    public static function notifyStudentLeaveSubmitted(StudentLeave $leave)
    {
        $leaveDate = Carbon::parse($leave->leave_date)->format('d M Y');
        $title = 'New Student Leave Request ✉️';
        $msg = "Student {$leave->student_name} (Roll #{$leave->std_id}, Class {$leave->student_class} {$leave->student_section}) applied for {$leave->leave_type} on {$leaveDate}. Status: Pending.";

        // 1. Notify responsible In-Charge Teacher
        self::sendToClassIncharge(
            $leave->student_class,
            $title,
            $msg,
            'leave',
            route('teacher.page', ['page' => 'stdleaves', 'class' => $leave->student_class]),
            'bi-envelope-paper-fill',
            $leave->user_id,
            $leave->student_section
        );

        // 2. Notify School Administration
        self::sendToAdmins(
            $title,
            $msg,
            'leave',
            route('admin.page', 'leaves'),
            'bi-envelope-paper-fill',
            $leave->user_id
        );
    }

    /**
     * Workflow 5.C: Decision made on student leave request
     * Notifies: Student applicant + Admin (if decided by Teacher) + In-Charge (if decided by Admin)
     */
    public static function notifyStudentLeaveDecision(StudentLeave $leave, $decision, $remarks = null, $actorUser = null)
    {
        $actorName = $actorUser ? ($actorUser->name ?: $actorUser->username) : 'Faculty';
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $leaveDate = Carbon::parse($leave->leave_date)->format('d M Y');
        $isApproved = strtolower($decision) === 'approved';

        $studentTitle = $isApproved ? 'Your Leave Application Has Been Approved ✅' : 'Your Leave Application Has Been Rejected ❌';
        $studentMsg = $isApproved
            ? "Your leave application for {$leaveDate} has been approved by {$actorName}."
            : "Your leave application for {$leaveDate} has been rejected by {$actorName}." . ($remarks ? " Reason: {$remarks}" : '');

        // 1. Notify the student applicant
        self::sendToStudentLeaveApplicant(
            $leave,
            $studentTitle,
            $studentMsg,
            'leave',
            route('student.page', 'leaves'),
            $isApproved ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
            $actorId
        );

        // 2. If decided by teacher, notify Admin for school monitoring
        $actorIsAdmin = $actorUser && ($actorUser->role?->slug === 'admin' || (int)$actorUser->role_id === 1);
        if (!$actorIsAdmin) {
            $adminTitle = "Student Leave {$decision}";
            $adminMsg = "Student {$leave->student_name}'s leave for {$leaveDate} was {$decision} by Class In-Charge ({$actorName}).";
            self::sendToAdmins(
                $adminTitle,
                $adminMsg,
                'leave',
                route('admin.page', 'leaves'),
                $isApproved ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
                $actorId
            );
        } else {
            // If decided by Admin, keep responsible In-Charge informed
            $inchargeTitle = "Student Leave {$decision}";
            $inchargeMsg = "Student {$leave->student_name}'s leave for {$leaveDate} was {$decision} by Admin ({$actorName}).";
            self::sendToClassIncharge(
                $leave->student_class,
                $inchargeTitle,
                $inchargeMsg,
                'leave',
                route('teacher.page', ['page' => 'stdleaves', 'class' => $leave->student_class]),
                $isApproved ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
                $actorId,
                $leave->student_section
            );
        }
    }

    /**
     * Workflow 6.A: Teacher submits leave application
     * Notifies: Admin
     */
    public static function notifyTeacherLeaveSubmitted(TeacherLeave $leave)
    {
        $leaveDate = Carbon::parse($leave->leave_date)->format('d M Y');
        $title = 'New Teacher Leave Request ✉️';
        $msg = "{$leave->teacher_name} has submitted a leave application for {$leaveDate} ({$leave->leave_type}). Approval is pending.";

        self::sendToAdmins(
            $title,
            $msg,
            'leave',
            route('admin.page', 'leaves'),
            'bi-envelope-paper-fill',
            $leave->user_id
        );
    }

    /**
     * Workflow 6.B: Admin approves or rejects teacher leave application
     * Notifies: Teacher applicant
     */
    public static function notifyTeacherLeaveDecision(TeacherLeave $leave, $decision, $remarks = null, $actorUser = null)
    {
        $actorName = $actorUser ? ($actorUser->name ?: $actorUser->username) : 'School Administration';
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $leaveDate = Carbon::parse($leave->leave_date)->format('d M Y');
        $isApproved = strtolower($decision) === 'approved';

        $title = $isApproved ? 'Teacher Leave Approved ✅' : 'Teacher Leave Rejected ❌';
        $msg = $isApproved
            ? "Your leave application for {$leaveDate} has been approved by {$actorName}."
            : "Your leave application for {$leaveDate} has been rejected by {$actorName}." . ($remarks ? " Reason: {$remarks}" : '');

        self::sendToTeacherLeaveApplicant(
            $leave,
            $title,
            $msg,
            'leave',
            route('teacher.page', 'attendance'),
            $isApproved ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
            $actorId
        );
    }

    /**
     * Workflow 8: Examination, Test, or Paper scheduled
     * Notifies: Affected Students, responsible In-Charge Teacher, Admin
     */
    public static function notifyExamScheduled($examTitle, $className, $sectionName = null, $subject = null, $date = null, $time = null, $actorUser = null)
    {
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $title = 'New Examination Scheduled 🎫';
        $secText = $sectionName ? " Section {$sectionName}" : '';
        $subText = $subject ? " ({$subject})" : '';
        $dateText = $date ? " on {$date}" : '';
        $timeText = $time ? " at {$time}" : '';
        $msg = "A new examination '{$examTitle}'{$subText} has been scheduled for Class {$className}{$secText}{$dateText}{$timeText}. Check your schedule in Student Portal.";

        // 1. Notify all eligible students in the class/section
        self::sendToClassStudents(
            $className,
            $title,
            $msg,
            'exam',
            route('student.page', 'slips'),
            'bi-card-heading',
            $actorId,
            $sectionName
        );

        // 2. Notify Class In-Charge
        self::sendToClassIncharge(
            $className,
            "Examination Scheduled: {$examTitle}",
            "Examination '{$examTitle}'{$subText} scheduled for Class {$className}{$secText}{$dateText}.",
            'exam',
            route('roll-slips.index'),
            'bi-calendar-event',
            $actorId,
            $sectionName
        );

        // 3. Notify Admin for school monitoring
        self::sendToAdmins(
            "Examination Scheduled: {$examTitle}",
            "Examination '{$examTitle}' scheduled for Class {$className}{$secText}{$dateText}.",
            'exam',
            route('roll-slips.index'),
            'bi-calendar-event',
            $actorId
        );
    }

    /**
     * Workflow 8: Examination schedule updated
     */
    public static function notifyExamChanged($examTitle, $className, $sectionName = null, $changeDetails = '', $actorUser = null)
    {
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $title = 'Examination Schedule Updated ⚠️';
        $secText = $sectionName ? " Section {$sectionName}" : '';
        $msg = "The schedule for '{$examTitle}' (Class {$className}{$secText}) has been updated. " . ($changeDetails ?: 'Please review the revised timetable.');

        self::sendToClassStudents($className, $title, $msg, 'exam', route('student.page', 'slips'), 'bi-clock-history', $actorId, $sectionName);
        self::sendToClassIncharge($className, $title, $msg, 'exam', route('roll-slips.index'), 'bi-clock-history', $actorId, $sectionName);
        self::sendToAdmins($title, $msg, 'exam', route('roll-slips.index'), 'bi-clock-history', $actorId);
    }

    /**
     * Workflow 8: Examination cancelled
     */
    public static function notifyExamCancelled($examTitle, $className, $sectionName = null, $reason = '', $actorUser = null)
    {
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $title = 'Examination Cancelled ❌';
        $secText = $sectionName ? " Section {$sectionName}" : '';
        $msg = "The scheduled examination '{$examTitle}' for Class {$className}{$secText} has been cancelled." . ($reason ? " Reason: {$reason}" : '');

        self::sendToClassStudents($className, $title, $msg, 'exam', route('student.page', 'slips'), 'bi-x-octagon-fill', $actorId, $sectionName);
        self::sendToClassIncharge($className, $title, $msg, 'exam', route('roll-slips.index'), 'bi-x-octagon-fill', $actorId, $sectionName);
        self::sendToAdmins($title, $msg, 'exam', route('roll-slips.index'), 'bi-x-octagon-fill', $actorId);
    }

    /**
     * Workflow 9: Results published
     * Notifies: Affected students, responsible In-Charge Teacher, Admin
     */
    public static function notifyResultsPublished($className, $sectionName = null, $term = 'First Term', $subject = null, $studentIds = [], $actorUser = null)
    {
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $subText = $subject ? ", subject {$subject}" : '';
        $secText = $sectionName ? " ({$sectionName})" : '';

        // If specific student IDs passed, notify them individually
        if (!empty($studentIds)) {
            foreach ($studentIds as $stId) {
                $st = Student::find($stId);
                if ($st) {
                    $uIds = [];
                    if ($st->login_user_id) $uIds[] = (int)$st->login_user_id;
                    if ($st->email) {
                        $u = User::where('email', $st->email)->pluck('id')->toArray();
                        $uIds = array_merge($uIds, $u);
                    }
                    if ($st->registration_no) {
                        $u = User::where('username', $st->registration_no)->pluck('id')->toArray();
                        $uIds = array_merge($uIds, $u);
                    }
                    foreach (array_unique(array_filter($uIds)) as $uid) {
                        self::sendToUser(
                            $uid,
                            'Examination Result Published 🏆',
                            "Your result for {$term}{$subText}, Class {$className}{$secText}, has been published. Log in to your Student Portal to view it.",
                            'result',
                            route('student.page', 'results'),
                            'bi-award-fill',
                            $actorId
                        );
                    }
                }
            }
        } else {
            // Notify whole class students
            self::sendToClassStudents(
                $className,
                'Examination Result Published 🏆',
                "Your result for {$term}{$subText}, Class {$className}{$secText}, has been published. Log in to your Student Portal to view it.",
                'result',
                route('student.page', 'results'),
                'bi-award-fill',
                $actorId,
                $sectionName
            );
        }

        // Notify In-Charge Teacher
        self::sendToClassIncharge(
            $className,
            'Class Results Published 📊',
            "Results for Class {$className}{$secText} ({$term}{$subText}) have been successfully published.",
            'result',
            route('results.list'),
            'bi-clipboard2-data-fill',
            $actorId,
            $sectionName
        );

        // Notify Admin for administrative monitoring
        self::sendToAdmins(
            'Class Results Published 📊',
            "Results for Class {$className}{$secText} ({$term}{$subText}) have been successfully published.",
            'result',
            route('admin.page', 'academics'),
            'bi-clipboard2-data-fill',
            $actorId
        );
    }

    /**
     * Workflow 9: Result revised / updated for a single student
     */
    public static function notifyResultUpdated(Result $result, $actorUser = null)
    {
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $student = Student::where('name', $result->student_name)
            ->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($result->student_class))])
            ->first();

        $subText = $result->subject ? " ({$result->subject})" : '';
        $title = 'Examination Result Updated 🏆';
        $msg = "Your result for {$result->term}{$subText} has been updated. Please review your latest marks in your Student Portal.";

        if ($student) {
            $uIds = [];
            if ($student->login_user_id) $uIds[] = (int)$student->login_user_id;
            if ($student->email) {
                $u = User::where('email', $student->email)->pluck('id')->toArray();
                $uIds = array_merge($uIds, $u);
            }
            if ($student->registration_no) {
                $u = User::where('username', $student->registration_no)->pluck('id')->toArray();
                $uIds = array_merge($uIds, $u);
            }
            foreach (array_unique(array_filter($uIds)) as $uid) {
                self::sendToUser($uid, $title, $msg, 'result', route('student.page', 'results'), 'bi-award-fill', $actorId);
            }
        }
    }

    /**
     * Workflow 10: Attendance submitted or finalized
     * Notifies: In-Charge Teacher (if submitted by another user), Admin, affected students
     */
    public static function notifyAttendanceSubmitted($className, $sectionName, $date, $records = [], $actorUser = null)
    {
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $actorName = $actorUser ? ($actorUser->name ?: $actorUser->username) : 'Teacher';
        $formattedDate = Carbon::parse($date)->format('d M Y');
        $secText = $sectionName ? " Section {$sectionName}" : '';

        // 1. Notify Admin for attendance monitoring
        self::sendToAdmins(
            'Class Attendance Submitted 📋',
            "Attendance for Class {$className}{$secText} has been submitted by {$actorName} for {$formattedDate}.",
            'attendance',
            route('admin.page', 'attendance'),
            'bi-calendar-check-fill',
            $actorId
        );

        // 2. Notify Class In-Charge teacher
        self::sendToClassIncharge(
            $className,
            'Class Attendance Submitted 📋',
            "Attendance for Class {$className}{$secText} has been recorded by {$actorName} for {$formattedDate}.",
            'attendance',
            route('attendance.index', ['class' => $className, 'section' => $sectionName, 'date' => $date]),
            'bi-calendar-check-fill',
            $actorId,
            $sectionName
        );

        // 3. Notify individual students about their attendance status
        foreach ($records as $studentId => $item) {
            $student = Student::find($studentId);
            if ($student) {
                $statusFormatted = ucfirst($item['status'] ?? 'marked');
                $uIds = [];
                if ($student->login_user_id) $uIds[] = (int)$student->login_user_id;
                if ($student->email) {
                    $u = User::where('email', $student->email)->pluck('id')->toArray();
                    $uIds = array_merge($uIds, $u);
                }
                if ($student->registration_no) {
                    $u = User::where('username', $student->registration_no)->pluck('id')->toArray();
                    $uIds = array_merge($uIds, $u);
                }
                foreach (array_unique(array_filter($uIds)) as $uid) {
                    self::sendToUser(
                        $uid,
                        'Attendance Updated 📋',
                        "Your attendance for {$formattedDate} has been recorded as {$statusFormatted}. Contact your In-Charge Teacher if you have questions.",
                        'attendance',
                        route('student.page', 'attendance'),
                        'bi-calendar-check-fill',
                        $actorId
                    );
                }
            }
        }
    }

    /**
     * Workflow 7: School-wide or Group Announcements
     */
    public static function notifyAnnouncement($title, $message, $audienceType = 'all', $targetClass = null, $targetSection = null, $actorUser = null)
    {
        $actorId = $actorUser ? $actorUser->id : session('user_id');
        $notifTitle = "Announcement: {$title} 📢";

        switch (strtolower(trim($audienceType))) {
            case 'students':
                $studentUserIds = User::whereHas('role', fn($q) => $q->where('slug', 'student'))
                    ->orWhereNotNull('login_user_id')
                    ->pluck('id')->toArray();
                foreach (array_unique($studentUserIds) as $uid) {
                    self::sendToUser($uid, $notifTitle, $message, 'announcement', route('student.dashboard'), 'bi-megaphone-fill', $actorId);
                }
                break;

            case 'teachers':
                $teacherUserIds = User::whereHas('role', fn($q) => $q->whereIn('slug', ['teacher', 'class-incharge']))
                    ->pluck('id')->toArray();
                foreach (array_unique($teacherUserIds) as $uid) {
                    self::sendToUser($uid, $notifTitle, $message, 'announcement', route('teacher.dashboard'), 'bi-megaphone-fill', $actorId);
                }
                break;

            case 'incharges':
                $inchargeRecs = ClassIncharge::with('teacher', 'user')->get();
                $inchargeIds = [];
                foreach ($inchargeRecs as $ic) {
                    if ($ic->user_id) $inchargeIds[] = $ic->user_id;
                    if ($ic->teacher?->login_user_id) $inchargeIds[] = $ic->teacher->login_user_id;
                }
                foreach (array_unique(array_filter($inchargeIds)) as $uid) {
                    self::sendToUser($uid, $notifTitle, $message, 'announcement', route('teacher.dashboard'), 'bi-megaphone-fill', $actorId);
                }
                break;

            case 'class':
                if ($targetClass) {
                    self::sendToClassStudents($targetClass, $notifTitle, $message, 'announcement', route('student.dashboard'), 'bi-megaphone-fill', $actorId, $targetSection);
                    self::sendToClassIncharge($targetClass, $notifTitle, $message, 'announcement', route('teacher.dashboard'), 'bi-megaphone-fill', $actorId, $targetSection);
                }
                break;

            case 'all':
            default:
                // Entire school: all active students, teachers, admins
                $allUsers = User::all();
                foreach ($allUsers as $u) {
                    $url = '#';
                    if ($u->role?->slug === 'student') $url = route('student.dashboard');
                    elseif (in_array($u->role?->slug, ['teacher', 'class-incharge'])) $url = route('teacher.dashboard');
                    elseif ($u->role?->slug === 'admin') $url = route('admin.dashboard');

                    self::sendToUser($u->id, $notifTitle, $message, 'announcement', $url, 'bi-megaphone-fill', $actorId);
                }
                break;
        }
    }
}
