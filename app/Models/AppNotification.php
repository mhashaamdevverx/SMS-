<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
     * Send notification to a specific user_id and all matching accounts for that user
     */
    public static function sendToUser($userId, $title, $message, $type = 'general', $actionUrl = null, $icon = null, $senderId = null)
    {
        if (!$userId) return null;

        $targetUser = User::find($userId);
        $userIds = [$userId];

        if ($targetUser) {
            $related = User::where(function($q) use ($targetUser) {
                if (!empty($targetUser->email)) $q->orWhere('email', $targetUser->email);
                if (!empty($targetUser->username)) $q->orWhere('username', $targetUser->username);
                if (!empty($targetUser->name)) $q->orWhere('name', $targetUser->name);
            })->pluck('id')->toArray();

            $userIds = array_unique(array_merge($userIds, $related));
        }

        $created = null;
        $activeSenderId = $senderId ?: session('user_id');

        foreach ($userIds as $uid) {
            // Requirement 8 & 11: Do not send notification to oneself
            if ($activeSenderId && (int)$uid === (int)$activeSenderId) {
                continue;
            }

            // Requirement 11: Deduplication - prevent duplicate notification within 60 seconds
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
     * Send notification to all students in a class
     */
    public static function sendToClassStudents($className, $title, $message, $type = 'general', $actionUrl = null, $icon = null, $senderId = null)
    {
        $rawClass = trim($className);
        $cleanClass = trim(preg_replace('/\s*\(.*?\)/', '', $rawClass));
        $targetClassStr = strtolower($cleanClass);

        $students = Student::where(function($q) use ($targetClassStr, $rawClass) {
            $q->whereRaw('LOWER(TRIM(class)) = ?', [strtolower(trim($rawClass))])
              ->orWhereRaw('LOWER(TRIM(class)) = ?', [$targetClassStr])
              ->orWhereRaw('? LIKE CONCAT("%", LOWER(TRIM(class)), "%")', [$targetClassStr]);
        })->get();

        $targetUserIds = [];
        foreach ($students as $student) {
            if ($student->login_user_id) $targetUserIds[] = $student->login_user_id;
            if ($student->user_id && !in_array($student->user_id, [1, 2])) $targetUserIds[] = $student->user_id;
            
            if ($student->email) {
                $u = User::where('email', $student->email)->first();
                if ($u) $targetUserIds[] = $u->id;
            }
            if ($student->registration_no) {
                $u = User::where('username', $student->registration_no)->first();
                if ($u) $targetUserIds[] = $u->id;
            }
            if ($student->name) {
                $uNames = User::where('name', $student->name)->pluck('id')->toArray();
                $targetUserIds = array_merge($targetUserIds, $uNames);
            }
        }

        $targetUserIds = array_unique(array_filter($targetUserIds));
        foreach ($targetUserIds as $uid) {
            self::sendToUser($uid, $title, $message, $type, $actionUrl, $icon, $senderId);
        }
    }

    /**
     * Send notification to designated Class In-Charge, assigned class teachers, and admins
     */
    public static function sendToClassIncharge($className, $title, $message, $type = 'general', $actionUrl = null, $icon = null, $senderId = null)
    {
        $rawClass = trim($className);
        $cleanClass = trim(preg_replace('/\s*\(.*?\)/', '', $rawClass));
        $targetClassStr = strtolower($cleanClass);

        $notifiedUserIds = [];

        // 1. Notify Class In-Charge teachers
        $inchargeRecs = ClassIncharge::with('teacher', 'user')
            ->whereHas('classRecord', function($q) use ($targetClassStr, $rawClass) {
                $q->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($rawClass))])
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [$targetClassStr])
                  ->orWhereRaw('? LIKE CONCAT("%", LOWER(TRIM(class_name)), "%")', [$targetClassStr]);
            })->get();

        foreach ($inchargeRecs as $inchargeRec) {
            if ($inchargeRec->teacher?->login_user_id) $notifiedUserIds[] = $inchargeRec->teacher->login_user_id;
            if ($inchargeRec->teacher?->user_id && !in_array($inchargeRec->teacher->user_id, [1, 2])) $notifiedUserIds[] = $inchargeRec->teacher->user_id;
            if ($inchargeRec->user_id && !in_array($inchargeRec->user_id, [1, 2])) $notifiedUserIds[] = $inchargeRec->user_id;

            $tName = $inchargeRec->teacher?->teacher_name;
            $tEmail = $inchargeRec->teacher?->teacher_email;

            if ($tName) {
                $sameNameUserIds = User::where('name', $tName)->orWhere('username', $tName)->pluck('id')->toArray();
                $notifiedUserIds = array_merge($notifiedUserIds, $sameNameUserIds);
                $sameNameTeachers = Teacher::where('teacher_name', $tName)->get();
                foreach ($sameNameTeachers as $st) {
                    if ($st->login_user_id) $notifiedUserIds[] = $st->login_user_id;
                    if ($st->user_id && !in_array($st->user_id, [1, 2])) $notifiedUserIds[] = $st->user_id;
                }
            }
            if ($tEmail) {
                $sameEmailUserIds = User::where('email', $tEmail)->pluck('id')->toArray();
                $notifiedUserIds = array_merge($notifiedUserIds, $sameEmailUserIds);
            }
        }

        // 2. Notify all Teachers assigned to teach subjects in this class
        $assignments = TeacherAssignment::with('teacher')
            ->where(function($q) use ($targetClassStr, $rawClass) {
                $q->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($rawClass))])
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [$targetClassStr]);
            })->get();

        foreach ($assignments as $assign) {
            if ($assign->teacher?->login_user_id) $notifiedUserIds[] = $assign->teacher->login_user_id;
            if ($assign->teacher?->user_id && !in_array($assign->teacher->user_id, [1, 2])) $notifiedUserIds[] = $assign->teacher->user_id;

            $atName = $assign->teacher?->teacher_name;
            if ($atName) {
                $sameNameUserIds = User::where('name', $atName)->orWhere('username', $atName)->pluck('id')->toArray();
                $notifiedUserIds = array_merge($notifiedUserIds, $sameNameUserIds);
            }
        }

        // 3. Notify Admins only (Role ID 1 or slug 'admin')
        $adminUserIds = User::whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->orWhere('role_id', 1)->pluck('id')->toArray();

        $notifiedUserIds = array_unique(array_merge($notifiedUserIds, $adminUserIds));
        $notifiedUserIds = array_filter($notifiedUserIds);

        // Send to all identified teacher/admin user IDs
        foreach ($notifiedUserIds as $uid) {
            self::sendToUser($uid, $title, $message, $type, $actionUrl, $icon, $senderId);
        }
    }

    /**
     * Send notification to all Administrators and Heads
     */
    public static function sendToAdmins($title, $message, $type = 'leave', $actionUrl = null, $icon = null, $senderId = null)
    {
        $adminUserIds = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['admin', 'head', 'principal', 'super-admin']);
        })->orWhere('role_id', 1)->pluck('id')->toArray();

        $activeSenderId = $senderId ?: session('user_id');
        $adminUserIds = array_unique(array_filter(array_map('intval', $adminUserIds)));

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
                $icon ?: 'bi-envelope-paper-fill',
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
        $activeSenderId = $senderId ?: session('user_id');

        // 1. Direct user_id on the StudentLeave record (the applicant's login user ID)
        if (!empty($studentLeave->user_id)) {
            $targetUserIds[] = (int)$studentLeave->user_id;
        }

        // 2. Resolve Student model
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

        if (!empty($studentLeave->student_name)) {
            $uByName = User::where('name', $studentLeave->student_name)->pluck('id')->toArray();
            $targetUserIds = array_merge($targetUserIds, $uByName);
        }

        $targetUserIds = array_unique(array_filter(array_map('intval', $targetUserIds)));
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
        $activeSenderId = $senderId ?: session('user_id');

        // 1. Direct user_id on TeacherLeave record (the applicant teacher's user ID)
        if (!empty($teacherLeave->user_id)) {
            $targetUserIds[] = (int)$teacherLeave->user_id;
        }

        // 2. Resolve Teacher model
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
                $uNames = User::where('name', $teacher->teacher_name)->orWhere('username', $teacher->teacher_name)->pluck('id')->toArray();
                $targetUserIds = array_merge($targetUserIds, $uNames);
            }
            if (!empty($teacher->user_id) && (!$activeSenderId || (int)$teacher->user_id !== (int)$activeSenderId)) {
                $targetUserIds[] = (int)$teacher->user_id;
            }
        }

        if (!empty($teacherLeave->teacher_name)) {
            $uByName = User::where('name', $teacherLeave->teacher_name)->orWhere('username', $teacherLeave->teacher_name)->pluck('id')->toArray();
            $targetUserIds = array_merge($targetUserIds, $uByName);
        }

        $targetUserIds = array_unique(array_filter(array_map('intval', $targetUserIds)));
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
}

