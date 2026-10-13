<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\AppNotification;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function resolveCurrentUserIds()
    {
        $userIds = [];

        $sessionUserId = session('user_id') ?: (auth()->check() ? auth()->id() : null);
        if ($sessionUserId) {
            $userIds[] = (int)$sessionUserId;
        }

        $studentId = session('student_id');
        if ($studentId) {
            $student = Student::find($studentId);
            if ($student) {
                if ($student->user_id) $userIds[] = (int)$student->user_id;
                if ($student->login_user_id) $userIds[] = (int)$student->login_user_id;
                if ($student->email) {
                    $u = User::where('email', $student->email)->pluck('id')->toArray();
                    $userIds = array_merge($userIds, $u);
                }
                if ($student->registration_no) {
                    $u = User::where('username', $student->registration_no)->pluck('id')->toArray();
                    $userIds = array_merge($userIds, $u);
                }
            }
        }

        $teacherId = session('teacher_id');
        if ($teacherId) {
            $teacher = Teacher::find($teacherId);
            if ($teacher) {
                if ($teacher->user_id) $userIds[] = (int)$teacher->user_id;
                if ($teacher->login_user_id) $userIds[] = (int)$teacher->login_user_id;
                if ($teacher->teacher_email) {
                    $u = User::where('email', $teacher->teacher_email)->pluck('id')->toArray();
                    $userIds = array_merge($userIds, $u);
                }
            }
        }

        $userIds = array_unique(array_filter($userIds));
        $finalIds = $userIds;

        foreach ($userIds as $uid) {
            $user = User::find($uid);
            if ($user) {
                if (!empty($user->email)) {
                    $related = User::where('email', $user->email)->pluck('id')->toArray();
                    $finalIds = array_merge($finalIds, $related);
                }
                if (!empty($user->username)) {
                    $related = User::where('username', $user->username)->pluck('id')->toArray();
                    $finalIds = array_merge($finalIds, $related);
                }

                // If user is a student, include any user account matching their student profile
                if ($user->role?->slug === 'student' || $user->studentRecord) {
                    $st = $user->studentRecord ?: Student::where('login_user_id', $user->id)->orWhere('email', $user->email)->first();
                    if ($st) {
                        if ($st->login_user_id) $finalIds[] = (int)$st->login_user_id;
                        if (!empty($st->email)) {
                            $uEmails = User::where('email', $st->email)->pluck('id')->toArray();
                            $finalIds = array_merge($finalIds, $uEmails);
                        }
                        if (!empty($st->registration_no)) {
                            $uReg = User::where('username', $st->registration_no)->pluck('id')->toArray();
                            $finalIds = array_merge($finalIds, $uReg);
                        }
                    }
                }

                // If user is a teacher, include any user account matching their teacher profile
                if (in_array($user->role?->slug, ['teacher', 'class-incharge']) || $user->teacherRecord) {
                    $tc = $user->teacherRecord ?: Teacher::where('login_user_id', $user->id)->orWhere('teacher_email', $user->email)->first();
                    if ($tc) {
                        if ($tc->login_user_id) $finalIds[] = (int)$tc->login_user_id;
                        if (!empty($tc->teacher_email)) {
                            $uEmails = User::where('email', $tc->teacher_email)->pluck('id')->toArray();
                            $finalIds = array_merge($finalIds, $uEmails);
                        }
                    }
                }
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $finalIds))));
    }

    public function getNotifications(Request $request)
    {
        $userIds = $this->resolveCurrentUserIds();

        if (empty($userIds)) {
            return response()->json(['unread' => 0, 'notifications' => []]);
        }

        $notifications = AppNotification::whereIn('user_id', $userIds)
            ->latest('id')
            ->take(15)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'icon' => $n->icon ?: 'bi-bell-fill',
                    'action_url' => $n->action_url ?: '#',
                    'is_read' => (bool)$n->is_read,
                    'time_ago' => $n->created_at ? $n->created_at->diffForHumans() : 'Just now',
                ];
            });

        $unreadCount = AppNotification::whereIn('user_id', $userIds)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request)
    {
        $userIds = $this->resolveCurrentUserIds();

        if (empty($userIds)) {
            return response()->json(['success' => false]);
        }

        $id = $request->input('id');
        if ($id) {
            AppNotification::whereIn('user_id', $userIds)->where('id', $id)->update(['is_read' => true]);
        } else {
            AppNotification::whereIn('user_id', $userIds)->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function clearAll(Request $request)
    {
        $userIds = $this->resolveCurrentUserIds();

        if (!empty($userIds)) {
            AppNotification::whereIn('user_id', $userIds)->delete();
        }

        return response()->json(['success' => true]);
    }
}
