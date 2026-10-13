@extends('layout')

@section('content')

<div class="form-container">

    <div class="form-box">

        <h2>Add New Teacher</h2>

        <form action="/add-teacher" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- TEACHER NAME --}}
            <div class="field-group">
                <label>Teacher Name</label>
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
                <label>Teacher Email</label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        name="teacher_email"
                        placeholder="teacher@gmail.com"
                        pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                        value="{{ old('teacher_email') }}"
                        class="@error('teacher_email') input-error @enderror"
                        required
                    >
                    <span class="field-error @error('teacher_email') has-error @enderror">@error('teacher_email'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- TEACHER PHONE --}}
            <div class="field-group">
                <label>Teacher Phone</label>
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

            {{-- ADD BUTTON --}}
            <button type="submit" class="add-btn">
                Add Teacher
            </button>

        </form>

    </div>

</div>

@endsection