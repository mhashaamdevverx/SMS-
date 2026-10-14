@extends('layout')
@section('content')
@php
    $oldClassIds = collect(old('class_ids', []))->map(fn ($value) => (int) $value)->all();
    $arrangementClassData = $classOptions->map(fn ($class) => [
        'id' => (int) $class->id,
        'class_no' => $class->class_no,
        'class_name' => $class->class_name,
        'section_name' => $class->section_name,
        'chairs' => (int) $class->chairs,
    ])->values();
@endphp
<div class="form-container" data-arrangement-page data-arrangement-records="{{ json_encode($arrangementClassData) }}">
    <div class="form-box arrangement-builder">
        <h2>Generate Arrangement</h2>
        <p class="arrangement-builder-intro">Select each class-section separately. Its own chairs are used, and only students from that exact class and section are assigned.</p>

        <form action="{{ route('arrangements.store') }}" method="POST">
            @csrf
            <div class="field-group arrangement-field-full">
                <label>Class Sections and Chairs</label>
                <div class="arrangement-multiselect arrangement-section-selector @error('class_ids') selector-error @enderror" data-arrangement-selector>
                    <button type="button" class="arrangement-selector-trigger" data-arrangement-trigger aria-expanded="false" aria-controls="arrangement-section-options">
                        <span data-arrangement-trigger-text>Select class-sections</span><i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="arrangement-selector-menu" id="arrangement-section-options" data-arrangement-menu hidden>
                        <div class="arrangement-selector-search"><i class="bi bi-search" aria-hidden="true"></i><input type="search" placeholder="Search class or section" aria-label="Search class sections" data-arrangement-search></div>
                        <p class="arrangement-selector-menu-title">Choose class-sections</p>
                        <div class="arrangement-selector-options" data-arrangement-options>
                            @forelse($classOptions as $class)
                                <label class="arrangement-selector-option arrangement-section-option" data-arrangement-option data-search="{{ strtolower($class->class_name.' '.$class->section_name.' room '.$class->class_no) }}">
                                    <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" @checked(in_array($class->id, $oldClassIds, true))>
                                    <span><strong>{{ $class->class_name }} / {{ $class->section_name }}</strong><small>Room {{ $class->class_no }} &middot; {{ $class->chairs }} chairs</small></span>
                                </label>
                            @empty
                                <span class="arrangement-picker-empty">No class-sections have been configured yet.</span>
                            @endforelse
                        </div>
                        <p class="arrangement-selector-no-results" data-arrangement-no-results hidden>No matching class-section found.</p>
                    </div>
                </div>
                <span class="class-help-text">Search or select from the compact list. Every selection keeps its own room and chair capacity.</span>
                <span class="field-error @error('class_ids') has-error @enderror">@error('class_ids'){{ $message }}@enderror</span>
            </div>
            <div class="field-group">
                <label for="arrangement-rooms">Class Nos / Rooms</label>
                <div class="input-wrapper"><input id="arrangement-rooms" type="text" readonly value="Select class-sections"></div>
            </div>
            <div class="field-group">
                <label for="arrangement-chairs">Total Chairs</label>
                <div class="input-wrapper"><input id="arrangement-chairs" type="number" readonly value="0"></div>
            </div>
            <div class="arrangement-preview" aria-live="polite">
                <strong id="arrangement-preview-title">No class-section selected</strong>
                <span id="arrangement-preview-detail">Choose class-section cards to calculate rooms and chairs.</span>
            </div>
            <div class="field-group field-full" style="display: flex; gap: 12px; align-items: center; margin-top: 15px;">
                <button type="submit" class="add-btn add-student-btn" style="margin-top: 0; flex: 1; min-height: 46px; display: inline-flex; align-items: center; justify-content: center;" @disabled($classOptions->isEmpty())>Generate Arrangement</button>
                <a href="{{ route('arrangements.index') }}" class="student-filter-clear flex-1-h46">Cancel</a>
            </div>
            @if($classOptions->isEmpty())
                <span class="field-error has-error">No configured class-sections found. Add classes with sections and chairs first.</span>
            @endif
        </form>
    </div>
</div>
@endsection