<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\TeacherAccessRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function signupForm()
    {
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i', 'max:50', 'unique:users,email'],
            'phone' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];

        $validation = Validator::make($request->all(), $rules, [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. teacher@gmail.com).',
        ]);

        $validation->after(function ($validation) use ($request) {
            if (!$this->validPhone($request->phone)) {
                $validation->errors()->add('phone', 'Please enter a valid phone number.');
            }
        });

        if ($validation->fails()) {
            return redirect('signup')
                ->withErrors($validation)
                ->withInput();
        } else {
            $teacher = Teacher::where('teacher_email', $request->email)->first();

            if (!$teacher) {
                return redirect('signup')
                    ->withErrors(['email' => 'Use the email address registered in your teacher profile.'])
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            if ($teacher->login_user_id || User::where('email', $request->email)->exists()) {
                return redirect('signup')
                    ->withErrors(['email' => 'This teacher already has an account.'])
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            if (TeacherAccessRequest::where('email', $request->email)->where('status', 'pending')->exists()) {
                return redirect('signup')
                    ->withErrors(['email' => 'A teacher account request is already waiting for approval.'])
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            TeacherAccessRequest::create([
                'teacher_id' => $teacher->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make(Str::random(40)),
                'image' => $request->hasFile('image')
                    ? $request->file('image')->store('upload/users', 'public')
                    : 'dummy.jpeg',
                'status' => 'pending',
            ]);

            return redirect('login')->with('success', 'Your teacher account request was sent to the administrator for approval.');
        }
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'],
        ], [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);
        $email = strtolower(trim($validated['email']));

        if (User::where('email', $email)->exists()) {
            DB::table('password_reset_requests')->updateOrInsert(
                ['email' => $email],
                ['status' => 'pending', 'requested_at' => now(), 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return back()->with('status', 'Your password-reset request has been recorded. Please contact the school administrator to complete verification.');
    }

    public function resetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
        ], [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'must_change_password' => false,
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function login(Request $request)
    {
        $rules = [
            'email'    => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'],
            'password' => 'required|min:6',
            'account_type' => 'required|in:student,teacher,admin',
        ];

        $validation = Validator::make($request->all(), $rules, [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);


        if ($validation->fails()) {
            return redirect('login')->withErrors($validation)->withInput();
        } else {
            $user = User::with(['role', 'studentRecord', 'teacherRecord'])
                ->where('email', $request->email)
                ->first();
            $roleMatches = match ($request->account_type) {
                'student' => $user?->role?->slug === 'student' && $user->studentRecord !== null,
                'teacher' => in_array($user?->role?->slug, ['teacher', 'class-incharge'], true),
                'admin' => $user?->role?->slug === 'admin',
            };

            if ($user && $roleMatches && Hash::check($request->password, $user->password)) {
                if ($request->account_type === 'teacher' && $user->teacherRecord === null) {
                    $teacher = Teacher::where('teacher_email', $user->email)->first();
                    if ($teacher) {
                        $teacher->update(['login_user_id' => $user->id]);
                    }
                }

                session([
                    'user_id'    => $user->id,
                    'user_name'  => $user->name ?: $user->username,
                    'user_image' => $user->image ?: 'dummy.jpeg',
                    'account_type' => $request->account_type,
                ]);

                if ($user->must_change_password && !$user->password_prompt_dismissed) {
                    return redirect()->route('account.password.edit');
                }

                if ($user->role?->slug === 'student') {
                    return redirect()->route('student.dashboard')->with('success', 'Signed in successfully. Welcome back!');
                }

                if (in_array($user->role?->slug, ['teacher', 'class-incharge'], true)) {
                    return redirect()->route('teacher.dashboard')->with('success', 'Signed in successfully. Welcome back!');
                }

                return redirect()->route('dashboard')->with('success', 'Signed in successfully.');
            } else {
                return redirect('login')->with('error', 'Email, password, or selected account type is incorrect.')->withInput($request->only('email', 'account_type'));
            }
        }
    }

    public function editPassword()
    {
        return view('auth.account-password');
    }

    public function dismissPasswordPrompt(Request $request)
    {
        $user = User::findOrFail($request->session()->get('user_id'));
        $user->update(['password_prompt_dismissed' => true]);

        return redirect()->route('dashboard');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:10', 'confirmed', 'different:current_password'],
        ]);

        $user = User::findOrFail($request->session()->get('user_id'));
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->password_prompt_dismissed = true;
        $user->save();

        if ($user->role?->slug === 'student') {
            return redirect()->route('student.dashboard')->with('success', 'Password updated successfully.');
        }

        if (in_array($user->role?->slug, ['teacher', 'class-incharge'], true)) {
            return redirect()->route('teacher.dashboard')->with('success', 'Password updated successfully.');
        }

        return redirect()->route('dashboard')->with('success', 'Password updated successfully.');
    }

    public function updateSettings(Request $request)
    {
        $user = User::findOrFail($request->session()->get('user_id'));

        $emailRules = [
            'required',
            'email',
            'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
            'max:60',
            Rule::unique('users', 'email')->ignore($user->id),
        ];
        if ($student = $user->studentRecord) {
            $emailRules[] = Rule::unique('Students', 'email')->ignore($student->id);
        }
        if ($teacher = $user->teacherRecord) {
            $emailRules[] = Rule::unique('teachers', 'teacher_email')->ignore($teacher->id);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => $emailRules,
            'phone' => 'nullable|string|max:30',
            'current_password' => 'nullable|required_with:password,password_confirmation',
            'password' => 'nullable|required_with:current_password,password_confirmation|min:6|same:password_confirmation',
            'password_confirmation' => 'nullable|required_with:current_password,password|same:password',
        ], [
            'email.regex' => 'The email address must be a valid @gmail.com address (e.g. name@gmail.com).',
        ]);


        if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The current password is incorrect.'])
                ->withInput();
        }

        $user->name = $validated['name'];
        $user->username = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($student = $user->studentRecord) {
            $student->name = $user->name;
            $student->email = $user->email;
            $student->phone_no = $user->phone ?: $student->phone_no;
            $student->save();
        }

        if ($teacher = $user->teacherRecord) {
            $teacher->teacher_name = $user->name;
            $teacher->teacher_email = $user->email;
            $teacher->teacher_phone = $user->phone ?: $teacher->teacher_phone;
            $teacher->save();
        }

        $request->session()->put('user_name', $user->name);

        return redirect()->back()->with('success', 'Profile settings updated successfully.');
    }

    private function validPhone($phone)
    {
        $phone = trim($phone ?? '');

        if ($phone === '') {
            return false;
        }

        if (substr($phone, 0, 1) === '+') {
            $phone = substr($phone, 1);
        } elseif (substr($phone, 0, 2) === '00') {
            $phone = substr($phone, 2);
        }

        $digits = '';

        for ($index = 0; $index < strlen($phone); $index++) {
            if ($phone[$index] !== ' ' && $phone[$index] !== '-' && !ctype_digit($phone[$index])) {
                return false;
            }

            if (ctype_digit($phone[$index])) {
                $digits .= $phone[$index];
            }
        }

        if (strlen($digits) === 11 && substr($digits, 0, 2) === '03') {
            return true;
        }

        return strlen($digits) >= 8 && strlen($digits) <= 15;
    }
}
