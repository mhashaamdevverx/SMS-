@extends('layout')
@section('content')
<div class="form-container">
    <div class="form-box">
        <h2>Add New Arrangement</h2>

        <form action="{{ route('arrangements.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label>Chairs</label>
                <div class="input-wrapper">
                    <input type="number" name="chairs" min="1" max="1000" placeholder="Enter chair number" value="{{ old('chairs') }}" class="@error('chairs') input-error @enderror">
                    <span class="field-error @error('chairs') has-error @enderror">@error('chairs'){{ $message }}@enderror</span>
                </div>
            </div>

            <div class="field-group">
                <label>Room</label>
                <div class="input-wrapper">
                    <input type="text" name="room" maxlength="50" placeholder="Enter room (e.g. Room 1)" value="{{ old('room') }}" class="@error('room') input-error @enderror">
                    <span class="field-error @error('room') has-error @enderror">@error('room'){{ $message }}@enderror</span>
                </div>
            </div>

            <div class="field-group">
                <label>Roll No</label>
                <div class="input-wrapper">
                    <input type="text" name="roll_no" maxlength="20" placeholder="Enter roll number" value="{{ old('roll_no') }}" class="@error('roll_no') input-error @enderror">
                    <span class="field-error @error('roll_no') has-error @enderror">@error('roll_no'){{ $message }}@enderror</span>
                </div>
            </div>

            <div class="field-group">
                <label>Class</label>
                <div class="input-wrapper">
                    <select name="class_id" class="arrangement-select @error('class_id') input-error @enderror">
                        <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Select class</option>
                        @foreach($classOptions as $option)
                            <option value="{{ $option->id }}" @selected((string) old('class_id') === (string) $option->id)>
                                {{ $option->class_name }} - {{ $option->section_name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="field-error @error('class_id') has-error @enderror">@error('class_id'){{ $message }}@enderror</span>
                    @if($classOptions->isEmpty())
                        <span class="field-error has-error">No classes found. Please add a class first.</span>
                    @endif
                </div>
            </div>

            <button type="submit" class="add-btn add-student-btn">Add</button>
        </form>
    </div>
</div>

@endsection