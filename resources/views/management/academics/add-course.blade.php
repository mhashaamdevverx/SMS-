@extends('layout')

@section('content')

<div class="form-container">

<div class="form-box">

    <h2>Add New Course</h2>

    <form action="/add-course" method="POST">

        @csrf


        {{-- COURSE CODE --}}

        <div class="field-group">

            <label>Course Code</label>

            <div class="input-wrapper">

                <input
                    type="text"
                    name="course_code"
                    placeholder="e.g. LAR-101"
                    value="{{ old('course_code') }}"
                    class="@error('course_code') input-error @enderror"
                >

                <span class="field-error @error('course_code') has-error @enderror">@error('course_code'){{ $message }}@enderror</span>

            </div>

        </div>


        {{-- COURSE NAME --}}

        <div class="field-group">

            <label>Course Name</label>

            <div class="input-wrapper">

                <input
                    type="text"
                    name="course_name"
                    placeholder="Enter course name"
                    value="{{ old('course_name') }}"
                    class="@error('course_name') input-error @enderror"
                >

                <span class="field-error @error('course_name') has-error @enderror">@error('course_name'){{ $message }}@enderror</span>

            </div>

        </div>


        {{-- COURSE DESCRIPTION --}}

        <div class="field-group">

            <label>Course Description</label>

            <div class="input-wrapper">

                <textarea
                    name="course_description"
                    placeholder="Enter course description (maximum 100 words)"
                    rows="6"
                    class="@error('course_description') input-error @enderror"
                >{{ old('course_description') }}</textarea>

                <span class="field-error @error('course_description') has-error @enderror">@error('course_description'){{ $message }}@enderror</span>

            </div>

        </div>


        {{-- ADD BUTTON --}}

        <button type="submit" class="add-btn">
            Add Course
        </button>

    </form>

</div>


</div>

@endsection
