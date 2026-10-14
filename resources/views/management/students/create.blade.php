@extends('layout')
@section('content')
<div class="form-container"
     id="add-student-container"
     data-class-sections="{{ json_encode($classSectionsMap) }}"
     data-class-roll-data="{{ json_encode($classRollData) }}"
     data-class-fee-data="{{ json_encode($classFeeData) }}">
    <div class="form-box">
        <h2>Add New Student</h2>

        <form action="/add-student" method="POST" enctype="multipart/form-data" id="add-student-form">
            @csrf

            {{-- First Name --}}
            <div class="field-group">
                <label for="first-name">First Name <span class="required-star">*</span></label>
                <div class="input-wrapper">
                    <input id="first-name" type="text" name="first_name" placeholder="Enter first name" value="{{ old('first_name') }}" class="@error('first_name') input-error @enderror" required>
                    <span class="field-error @error('first_name') has-error @enderror">@error('first_name'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Last Name --}}
            <div class="field-group">
                <label for="last-name">Last Name <span class="required-star">*</span></label>
                <div class="input-wrapper">
                    <input id="last-name" type="text" name="last_name" placeholder="Enter last name" value="{{ old('last_name') }}" class="@error('last_name') input-error @enderror" required>
                    <span class="field-error @error('last_name') has-error @enderror">@error('last_name'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Student login email --}}
            <div class="field-group">
                <label for="student-email">Student Login Email <span class="required-star">*</span></label>
                <div class="input-wrapper">
                    <input id="student-email" type="email" name="email" value="{{ old('email') }}" placeholder="hashaam.ahmad@gmail.com" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Must be a valid @gmail.com address" class="@error('email') input-error @enderror" required>
                    <small class="class-help-text">Must end with @gmail.com (e.g. hashaam.ahmad@gmail.com). Auto-suggested from student's name.</small>
                    <span class="field-error @error('email') has-error @enderror">@error('email'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Guardian Phone No --}}
            <div class="field-group">
                <label for="guardian-phone">Guardian Phone No <span class="required-star">*</span></label>
                <div class="input-wrapper">
                    <input id="guardian-phone" type="text" name="phone_no" placeholder="e.g. 03001234567" value="{{ old('phone_no') }}" class="@error('phone_no') input-error @enderror" required>
                    <span class="field-error @error('phone_no') has-error @enderror">@error('phone_no'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Emergency Phone No --}}
            <div class="field-group">
                <label for="emergency-phone">Emergency Phone No <small class="text-muted">(Optional)</small></label>
                <div class="input-wrapper">
                    <input id="emergency-phone" type="text" name="emergency_phone" placeholder="e.g. 03219876543" value="{{ old('emergency_phone') }}" class="@error('emergency_phone') input-error @enderror">
                    <span class="field-error @error('emergency_phone') has-error @enderror">@error('emergency_phone'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Class Dropdown --}}
            <div class="field-group">
                <label for="student-class">Class <span class="required-star">*</span></label>
                <div class="input-wrapper">
                    <select id="student-class" name="class" class="class-section-select @error('class') input-error @enderror" required>
                        <option value="" disabled {{ old('class') ? '' : 'selected' }}>Select class</option>
                        @foreach($classes as $cName)
                            <option value="{{ $cName }}" @selected(old('class') === $cName)>{{ $cName }}</option>
                        @endforeach
                    </select>
                    <span class="field-error @error('class') has-error @enderror">@error('class'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Section Dropdown --}}
            <div class="field-group">
                <label for="student-section">Section <span class="required-star">*</span></label>
                <div class="input-wrapper">
                    <select id="student-section" name="section" class="class-section-select @error('section') input-error @enderror" required>
                        <option value="" disabled {{ old('section') ? '' : 'selected' }}>Select section</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec }}" @selected(old('section') === $sec)>{{ $sec }}</option>
                        @endforeach
                    </select>
                    <span class="field-error @error('section') has-error @enderror">@error('section'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Roll No (std_id) --}}
            <div class="field-group">
                <label for="std-id">Roll No <span class="required-star">*</span></label>
                <div class="input-wrapper">
                    <input id="std-id"
                           type="number"
                           name="std_id"
                           min="1"
                           step="1"
                           placeholder="Roll No"
                           value="{{ old('std_id') }}"
                           class="@error('std_id') input-error @enderror"
                           required>
                    <small id="roll-no-hint" class="class-help-text">Select a class to auto-generate Roll No.</small>
                    <span class="field-error @error('std_id') has-error @enderror">@error('std_id'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Registration No --}}
            <div class="field-group">
                <label for="registration-no">Registration No</label>
                <div class="input-wrapper">
                    <input id="registration-no"
                           type="text"
                           name="registration_no"
                           placeholder="Auto-generated Registration No"
                           value="{{ old('registration_no') }}"
                           class="@error('registration_no') input-error @enderror"
                           readonly>
                    <small class="class-help-text">Auto-generated format: REG-YYYY-CLASS-00X</small>
                    <span class="field-error @error('registration_no') has-error @enderror">@error('registration_no'){{ $message }}@enderror</span>
                </div>
            </div>

            @if($currentUser->role?->slug !== 'class-incharge')
            {{-- Monthly Fee --}}
            <div class="field-group">
                <label for="monthly-fee">Monthly Tuition Fee (Rs.) <small class="text-muted">(Auto-fetched)</small></label>
                <div class="input-wrapper">
                    <input id="monthly-fee" type="number" name="monthly_fee" placeholder="Monthly Fee" value="{{ old('monthly_fee') }}" min="0" class="@error('monthly_fee') input-error @enderror">
                    <span class="field-error @error('monthly_fee') has-error @enderror">@error('monthly_fee'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Test Session Fee --}}
            <div class="field-group">
                <label for="test-session-fee">Test Session Fee (Rs.) <small class="text-muted">(Auto-fetched)</small></label>
                <div class="input-wrapper">
                    <input id="test-session-fee" type="number" name="test_session_fee" placeholder="Test Session Fee" value="{{ old('test_session_fee') }}" min="0" class="@error('test_session_fee') input-error @enderror">
                    <span class="field-error @error('test_session_fee') has-error @enderror">@error('test_session_fee'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Admission Fee --}}
            <div class="field-group">
                <label for="admission-fee">One-Time Admission Fee (Rs.) <small class="text-muted">(Auto-fetched, One-time)</small></label>
                <div class="input-wrapper">
                    <input id="admission-fee" type="number" name="admission_fee" placeholder="Admission Fee" value="{{ old('admission_fee') }}" min="0" class="@error('admission_fee') input-error @enderror">
                    <span class="field-error @error('admission_fee') has-error @enderror">@error('admission_fee'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Total Monthly Fee --}}
            <div class="field-group">
                <label for="total-monthly-fee">Total Monthly Fee (Rs.) <small class="text-muted">(Calculated)</small></label>
                <div class="input-wrapper">
                    <input id="total-monthly-fee" type="number" placeholder="Total Monthly Fee" readonly class="is-readonly-field fw-bold text-success">
                </div>
            </div>
            @endif

            {{-- Student Image (OPTIONAL) --}}
            <div class="field-group field-full">
                <label for="student-image">Student Image <small class="text-muted">(Optional)</small></label>
                <div class="input-wrapper">
                    <input id="student-image" type="file" name="image" accept="image/*" class="@error('image') input-error @enderror">
                    <span class="field-error @error('image') has-error @enderror">@error('image'){{ $message }}@enderror</span>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="field-group field-full" style="display: flex; gap: 12px; align-items: center; margin-top: 15px;">
                <button type="submit" class="add-btn" style="margin-top: 0; flex: 1; min-height: 46px; display: inline-flex; align-items: center; justify-content: center;">Add Student</button>
                <a href="{{ url('/list') }}" class="student-filter-clear flex-1-h46">Cancel</a>
            </div>
        </form>
    </div>
</div>




@endsection