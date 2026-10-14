@extends('layout')
@section('content')
<div class="form-container">
    <div class="form-box roll-slip-setup">
        <h2>Issue Roll Number Slips</h2>
        <p class="arrangement-builder-intro">Create the datesheet for arrangement rows that do not already have a stored roll-number slip.</p>
        @if($arrangementCount === 0)<div class="arrangement-preview"><strong>No arrangement found</strong><span>Generate arrangements before issuing slips.</span><a href="{{ route('arrangements.create') }}" class="add-btn">Generate Arrangement</a></div>@elseif($availableArrangementCount === 0)<div class="arrangement-preview"><strong>All roll slips are already issued</strong><span>Delete a roll slip from Roll Number Slips to make that student available again.</span></div>@endif
        <form action="{{ route('roll-slips.store') }}" method="POST">
            @csrf
            <div class="field-group"><label for="exam-name">Exam Name <span class="required-star text-danger">*</span></label><div class="input-wrapper"><input id="exam-name" name="exam_name" value="{{ old('exam_name') }}" placeholder="e.g. Final Examination" required><span class="field-error @error('exam_name') has-error @enderror">@error('exam_name'){{ $message }}@enderror</span></div></div>
            <div class="field-group"><label for="exam-session">Session</label><div class="input-wrapper"><input id="exam-session" name="exam_session" value="{{ old('exam_session') }}" placeholder="e.g. 2026-27"><span class="field-error @error('exam_session') has-error @enderror">@error('exam_session'){{ $message }}@enderror</span></div></div>
            <div class="roll-slip-schedule">
                <h3>Datesheet</h3>
                <div class="roll-slip-schedule-head"><span>Subject</span><span>Paper Date</span></div>
                @forelse($subjects as $index => $subject)
                    <div class="roll-slip-schedule-row">
                        <strong>{{ $subject->subject_name }}</strong><input type="hidden" name="schedules[{{ $index }}][subject_id]" value="{{ $subject->id }}">
                        <input type="date" name="schedules[{{ $index }}][exam_date]" value="{{ old("schedules.$index.exam_date") }}" required>
                    </div>
                @empty
                    <p class="student-filter-empty">Add subjects before creating a datesheet.</p>
                @endforelse
                @error('schedules')<span class="field-error has-error">{{ $message }}</span>@enderror
            </div>
            <div class="field-group field-full" style="display: flex; gap: 12px; align-items: center; margin-top: 15px;">
                <button type="submit" class="add-btn add-student-btn" style="margin-top: 0; flex: 1; min-height: 46px; display: inline-flex; align-items: center; justify-content: center;" @disabled($arrangementCount === 0 || $availableArrangementCount === 0 || $subjects->isEmpty())>Issue & Store Roll Slips</button>
                <a href="{{ route('roll-slips.index') }}" class="student-filter-clear flex-1-h46">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection