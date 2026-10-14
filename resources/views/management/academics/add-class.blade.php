@extends('layout')
@section('content')
<div class="form-container" data-assigned-sections="{{ json_encode($assignedSectionsByClass) }}">
    <div class="form-box">
        <h2>Add New Class</h2>
        <form action="{{ route('classes.store') }}" method="POST">
            @csrf
            <div class="field-group">
                <label for="class-no">Class No</label>
                <div class="input-wrapper">
                    <input id="class-no" type="number" value="{{ $nextClassNo }}" readonly aria-readonly="true">
                    <span class="class-help-text">Automatically assigned as the next room number.</span>
                </div>
            </div>
            <div class="field-group">
                <label for="class-name">Class <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <input id="class-name" type="text" name="class_name" list="class-options" placeholder="Select or enter a class" value="{{ old('class_name') }}" maxlength="50" class="@error('class_name') input-error @enderror" required>
                    <datalist id="class-options">
                        @foreach($classOptions as $classOption)
                            <option value="{{ $classOption }}"></option>
                        @endforeach
                    </datalist>
                    <span class="field-error @error('class_name') has-error @enderror">@error('class_name'){{ $message }}@enderror</span>
                </div>
            </div>
            <div class="field-group">
                <label for="chairs">Chairs <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <input id="chairs" type="number" name="chairs" min="1" max="10000" placeholder="Enter number of chairs" value="{{ old('chairs') }}" class="@error('chairs') input-error @enderror" required>
                    <span class="field-error @error('chairs') has-error @enderror">@error('chairs'){{ $message }}@enderror</span>
                </div>
            </div>
            <div class="field-group">
                <label for="section-name">Section Name <span class="required-star text-danger">*</span></label>
                <div class="input-wrapper">
                    <select id="section-name" name="section_name" class="class-section-select @error('section_name') input-error @enderror" required>
                        <option value="" disabled {{ old('section_name') ? '' : 'selected' }}>Select section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section }}" @selected(old('section_name') === $section)>{{ $section }}</option>
                        @endforeach
                    </select>
                    <span class="field-error @error('section_name') has-error @enderror">@error('section_name'){{ $message }}@enderror</span>
                    @if($sections->isEmpty())
                        <span class="field-error has-error">No sections found. Please add a section first.</span>
                    @endif
                </div>
            </div>
            <div class="field-group field-full" style="display: flex; gap: 12px; align-items: center; margin-top: 15px;">
                <button type="submit" class="add-btn add-student-btn" style="margin-top: 0; flex: 1; min-height: 46px; display: inline-flex; align-items: center; justify-content: center;">Add Class</button>
                <a href="{{ route('classes.index') }}" class="student-filter-clear flex-1-h46">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection