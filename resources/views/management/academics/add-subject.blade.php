@extends('layout')

@section('content')
<div class="form-container">
    <div class="form-box">
        <h2>Add New Subject</h2>

        <form action="{{ route('subjects.store') }}" method="POST">
            @csrf

            <div class="field-group field-full">
                <label for="class-id">Class &amp; Section <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <select id="class-id" name="class_id" class="class-section-select @error('class_id') input-error @enderror" required>
                        <option value="" disabled {{ old('class_id', $selectedClassId) ? '' : 'selected' }}>Select class &amp; section</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected((string) old('class_id', $selectedClassId) === (string) $class->id)>
                                {{ $class->class_name }} ({{ $class->section_name }})
                            </option>
                        @endforeach
                    </select>
                    <span class="field-error @error('class_id') has-error @enderror">@error('class_id'){{ $message }}@enderror</span>
                </div>
            </div>

            <div class="field-group field-full">
                <label for="subject-name">Subject Name <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <input id="subject-name" type="text" name="subject_name" placeholder="Enter subject name" value="{{ old('subject_name') }}" class="@error('subject_name') input-error @enderror" required>
                    <span class="field-error @error('subject_name') has-error @enderror">@error('subject_name'){{ $message }}@enderror</span>
                </div>
            </div>

            <div class="field-group field-full">
                <label for="total-marks">Total Marks <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <input id="total-marks" type="number" name="total_marks" min="1" max="100000" placeholder="Enter total marks" value="{{ old('total_marks') }}" class="@error('total_marks') input-error @enderror" required>
                    <span class="field-error @error('total_marks') has-error @enderror">@error('total_marks'){{ $message }}@enderror</span>
                </div>
            </div>

            <div class="field-group field-full ext-style-192">
                <button type="submit" class="add-btn ext-style-193">Submit</button>
                <a href="{{ route('subjects.list', $selectedClassId ? ['class_id' => $selectedClassId] : []) }}" class="student-filter-clear ext-style-194">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection