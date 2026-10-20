@extends('layout')

@section('content')

<div class="form-container">

    <div class="form-box">

        <h2>Add New Teacher</h2>

        <form action="/add-teacher" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- TEACHER NAME --}}
            <div class="field-group">
                <label>Teacher Name <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        name="teacher_name"
                        placeholder="Enter teacher name"
                        value="{{ old('teacher_name') }}"
                        class="@error('teacher_name') input-error @enderror"
                        required
                    >
                    <span class="field-error @error('teacher_name') has-error @enderror">@error('teacher_name'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- TEACHER EMAIL --}}
            <div class="field-group">
                <label>Teacher Email <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        id="teacher-email"
                        name="teacher_email"
                        placeholder="ali.khan@gmail.com"
                        pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                        title="Must be a valid @gmail.com address (e.g. ali.khan@gmail.com)"
                        value="{{ old('teacher_email') }}"
                        class="@error('teacher_email') input-error @enderror"
                        required
                    >
                    <small class="class-help-text" style="display:block;margin-top:3px;color:var(--muted);font-size:11.5px;">Must end with @gmail.com. Auto-suggested from teacher's name.</small>
                    <span class="field-error @error('teacher_email') has-error @enderror">@error('teacher_email'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- TEACHER PHONE --}}
            <div class="field-group">
                <label>Teacher Phone <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        name="teacher_phone"
                        placeholder="Enter 11 digit phone (03xxxxxxxxx)"
                        value="{{ old('teacher_phone') }}"
                        class="@error('teacher_phone') input-error @enderror"
                        required
                    >
                    <span class="field-error @error('teacher_phone') has-error @enderror">@error('teacher_phone'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- ASSIGNED CLASS --}}
            <div class="field-group">
                <label>Assigned Class</label>
                <div class="input-wrapper">
                    <select name="assigned_class" class="form-control @error('assigned_class') input-error @enderror">
                        <option value="">Select Class</option>
                        @if(isset($classesList))
                            @foreach($classesList as $cls)
                                @php $val = $cls->class_name . ($cls->section_name ? '|' . $cls->section_name : ''); @endphp
                                <option value="{{ $val }}" {{ old('assigned_class') == $val ? 'selected' : '' }}>
                                    {{ $cls->class_name }} {{ $cls->section_name ? '('.$cls->section_name.')' : '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <span class="field-error @error('assigned_class') has-error @enderror">@error('assigned_class'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- COURSE / SUBJECT DROPDOWN --}}
            <div class="field-group">
                <label>Course / Subject</label>
                <div class="input-wrapper">
                    <select name="subject_course_selected" class="form-control @error('subject_course_selected') input-error @enderror">
                        <option value="">Select Course / Subject</option>
                        @if(isset($subjectCourseOptions))
                            @foreach($subjectCourseOptions as $opt)
                                <option value="{{ $opt }}" {{ old('subject_course_selected') == $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <span class="field-error @error('subject_course_selected') has-error @enderror">@error('subject_course_selected'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- MANUAL COURSE / SUBJECT OVERRIDE --}}
            <div class="field-group">
                <label>Other Course / Subject</label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        name="course"
                        placeholder="Or type custom Course/Subject"
                        value="{{ old('course') }}"
                        class="@error('course') input-error @enderror"
                    >
                    <span class="field-error @error('course') has-error @enderror">@error('course'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- TEACHER IMAGE --}}
            <div class="field-group">
                <label>Teacher Image</label>
                <div class="input-wrapper">
                    <input
                        type="file"
                        name="teacher_image"
                        class="@error('teacher_image') input-error @enderror"
                        accept="image/jpeg,image/png,image/webp"
                    >
                    <span class="field-error @error('teacher_image') has-error @enderror">@error('teacher_image'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- SALARY DECLARATION SECTION --}}
            <div class="field-group field-full" style="background: linear-gradient(145deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #86EFAC; border-radius: 12px; padding: 16px; margin: 15px 0;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px solid rgba(22, 101, 52, 0.15); padding-bottom: 8px;">
                    <div>
                        <strong style="color: #166534; font-size: 14px; display: block;">
                            <i class="bi bi-cash-stack"></i> Salary Structure & Initial Declaration
                        </strong>
                        <small style="color: #15803D; font-size: 12px;">Reflects automatically on Admin Salaries and Teacher's Portal.</small>
                    </div>
                    <span style="background: #BBF7D0; color: #166534; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px;">Linked</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: #166534;">Basic Salary (PKR) <span class="required-star text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="basic_salary" value="{{ old('basic_salary', 50000) }}" placeholder="50000" style="width: 100%; border: 1px solid #86EFAC; border-radius: 8px; padding: 8px 12px; background: #fff;" required>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: #166534;">Allowances (PKR)</label>
                        <input type="number" step="0.01" min="0" name="allowances" value="{{ old('allowances', 0) }}" placeholder="0" style="width: 100%; border: 1px solid #86EFAC; border-radius: 8px; padding: 8px 12px; background: #fff;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: #166534;">Deductions (PKR)</label>
                        <input type="number" step="0.01" min="0" name="deductions" value="{{ old('deductions', 0) }}" placeholder="0" style="width: 100%; border: 1px solid #86EFAC; border-radius: 8px; padding: 8px 12px; background: #fff;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: #166534;">Salary Month</label>
                        <input type="text" name="salary_month" value="{{ old('salary_month', date('F Y')) }}" placeholder="e.g. May 2026" style="width: 100%; border: 1px solid #86EFAC; border-radius: 8px; padding: 8px 12px; background: #fff;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: #166534;">Payment Status</label>
                        <select name="salary_status" style="width: 100%; border: 1px solid #86EFAC; border-radius: 8px; padding: 8px 12px; background: #fff;">
                            <option value="Paid" {{ old('salary_status', 'Paid') === 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Pending" {{ old('salary_status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- FORM ACTIONS --}}
            <div class="field-group field-full" style="display: flex; gap: 12px; align-items: center; margin-top: 15px;">
                <button type="submit" class="add-btn" style="margin-top: 0; flex: 1; min-height: 46px; display: inline-flex; align-items: center; justify-content: center;">
                    Add Teacher
                </button>
                <a href="{{ url('/teachers') }}" class="student-filter-clear flex-1-h46">Cancel</a>
            </div>

        </form>

    </div>

</div>

@endsection