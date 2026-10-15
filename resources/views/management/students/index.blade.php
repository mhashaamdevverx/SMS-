@extends('layout')

@section('content')

<div class="container" data-student-page data-saved-subjects="{{ json_encode($subjects->values()) }}" data-class-fee-data="{{ json_encode($classFeeData) }}">

    {{-- STUDENT HEADER --}}
    <div class="student-header flex-between-center-mb24">
        <div class="flex-center-gap12-wrap">
            <h1 class="m-0">Student Data</h1>
            <span class="total-count-pill">
                <i class="bi bi-people-fill fs-14"></i> Total: {{ $students->total() }}
            </span>
        </div>
        @if($currentUser->hasPermission('students.create'))
            <a href="/add-student" class="student-add-btn">+ Add New Student</a>
        @endif
    </div>

    @if(($currentUser->isAdmin() || $currentUser->role?->slug === 'class-incharge') && session('issued_credentials'))
        <div class="success auto-hide-message">
            Temporary sign-in details: <strong>{{ session('issued_credentials.email') }}</strong> · <strong>{{ session('issued_credentials.password') }}</strong>.
            Share privately; password change is required at first sign-in.
        </div>
    @endif


    @if($currentUser->role?->slug !== 'student')
    <form action="{{ url('/list') }}" method="GET" class="student-filter-form">
        <div class="student-search-field">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" name="search" value="{{ $search }}" placeholder="Search student by name" aria-label="Search student by name">
        </div>

        <select name="class" class="student-class-filter" aria-label="Filter students by class">
            <option value="">All Classes</option>
            @foreach($classes as $class)
                <option value="{{ $class }}" @selected($selectedClass === $class)>{{ $class }}</option>
            @endforeach
        </select>

        <select name="section" class="student-class-filter" aria-label="Filter students by section">
            <option value="">All Sections</option>
            @foreach($sections as $section)
                <option value="{{ $section }}" @selected($selectedSection === $section)>{{ $section }}</option>
            @endforeach
        </select>

        <button type="submit" class="student-filter-btn">
            <i class="bi bi-funnel-fill" aria-hidden="true"></i>
            Search
        </button>

        @if($search !== '' || $selectedClass !== '' || $selectedSection !== '')
            <a href="{{ url('/list') }}" class="student-filter-clear">Clear</a>
        @endif
    </form>
    @endif

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div id="successMessage" class="success auto-hide-message">
            {{ session('success') }}
        </div>

    @endif


    {{-- STUDENT TABLE --}}
    <div class="unified-table-card"><div class="table-responsive-wrapper"><table class="student-table unified-table">

        <thead>
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                <th>Image</th>
                <th>Reg. No</th>
                <th>Guardian Phone</th>
                <th>Class</th>
                <th>Section</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        @forelse($students as $student)

        <tr>

            <td class="student-id-cell"><span class="student-id-chip">{{ $student->std_id ?? '?' }}</span></td>
            <td>{{ $student->name }}</td>

            <td class="table-image-cell">

                <img
                    src="{{ $student->image && $student->image !== 'dummy.jpeg' ? asset('storage/'.$student->image) : asset('upload/dummy.jpeg') }}"
                    onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';"
                    alt="Student Image"
                    class="table-avatar"
                >

            </td>

            <td><strong class="reg-no-highlight">{{ $student->registration_no ?? ('REG-2026-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')).'-'.str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT)) }}</strong></td>

            <td>{{ $student->phone_no }}</td>

            <td>{{ $student->class }}</td>

            <td><span class="section-badge section-{{ strtolower($student->section) }}">{{ $student->section }}</span></td>

            <td class="table-actions">
                <a href="{{ route('students.show', $student) }}" class="icon-action view-action" title="View student details" aria-label="View student details">
                    <i class="bi bi-eye-fill" aria-hidden="true"></i>
                </a>
                @if($currentUser->isAdmin() || $currentUser->role?->slug === 'class-incharge')
                    <button type="button" class="icon-action edit-action" data-crud-modal-open="student-modal-{{ $student->id }}" title="Edit student" aria-label="Edit student">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    </button>
                    <form action="{{ route('students.destroy', $student) }}" method="POST" data-crud-action="delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-action delete-action" title="Delete student" aria-label="Delete student">
                            <i class="bi bi-trash3" aria-hidden="true"></i>
                        </button>
                    </form>
                @endif
            </td>

        </tr>

        @empty
        <tr>
            <td colspan="8" class="student-filter-empty">No students found for this search or class or section filter.</td>
        </tr>
        @endforelse
        </tbody>

    </table></div></div>


    {{-- PAGINATION --}}
    @if($students->total() > 0)
        <div class="flex-between-center-mt18-wrap">
            <p class="student-pagination-info m-0">
                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
            </p>

            @if($students->hasPages())
                <div class="student-pagination">
                    {{ $students->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif


    @if($currentUser->isAdmin() || $currentUser->role?->slug === 'class-incharge')
    @foreach($students as $student)
        @php
            $studentSubjects = old('subjects', $student->subject ? array_filter(array_map('trim', explode(',', $student->subject))) : ['']);
            $studentSubjects = count($studentSubjects) ? $studentSubjects : [''];
        @endphp
        <div class="crud-backdrop" id="student-modal-{{ $student->id }}" data-open="{{ session('edit_modal') === 'student-'.$student->id ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="student-modal-title-{{ $student->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit student dialog">&times;</button>
                <h2 id="student-modal-title-{{ $student->id }}">Edit Student</h2>
                <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    <div class="crud-field"><label for="student-name-{{ $student->id }}">Name</label><input id="student-name-{{ $student->id }}" type="text" name="name" value="{{ old('name', $student->name) }}" class="@error('name') input-error @enderror" required>@error('name')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="student-std-id-{{ $student->id }}">Roll No</label><input id="student-std-id-{{ $student->id }}" type="number" name="std_id" min="1" step="1" value="{{ old('std_id', $student->std_id) }}" class="@error('std_id') input-error @enderror" required>@error('std_id')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="student-reg-no-{{ $student->id }}">Registration No</label><input id="student-reg-no-{{ $student->id }}" type="text" name="registration_no" value="{{ old('registration_no', $student->registration_no ?? ('REG-2026-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $student->class ?? 'STD')).'-'.str_pad($student->std_id ?? 1, 3, '0', STR_PAD_LEFT))) }}" readonly class="is-readonly-field"></div>
                    <div class="crud-field"><label for="student-email-{{ $student->id }}">Login Email</label><input id="student-email-{{ $student->id }}" type="email" value="{{ $student->email }}" readonly class="is-readonly-field"></div>
                    <div class="crud-field"><label for="student-phone-{{ $student->id }}">Guardian Phone</label><input id="student-phone-{{ $student->id }}" type="text" name="phone_no" value="{{ old('phone_no', $student->phone_no) }}" class="@error('phone_no') input-error @enderror" required>@error('phone_no')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="student-emergency-phone-{{ $student->id }}">Emergency Phone</label><input id="student-emergency-phone-{{ $student->id }}" type="text" name="emergency_phone" value="{{ old('emergency_phone', $student->emergency_phone) }}" class="@error('emergency_phone') input-error @enderror">@error('emergency_phone')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="student-class-{{ $student->id }}">Class</label><select id="student-class-{{ $student->id }}" name="class" class="student-edit-class-select @error('class') input-error @enderror" data-student-id="{{ $student->id }}" required><option value="" disabled>Select class</option>@foreach($allClasses as $cName)<option value="{{ $cName }}" @selected(old('class', $student->class) === $cName)>{{ $cName }}</option>@endforeach</select>@error('class')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="student-section-{{ $student->id }}">Section</label><select id="student-section-{{ $student->id }}" name="section" class="student-section-select @error('section') input-error @enderror" required>@foreach($sections as $section)<option value="{{ $section }}" @selected(old('section', $student->section) === $section)>{{ $section }}</option>@endforeach</select>@error('section')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    @if($currentUser->isAdmin())
                    <div class="crud-field"><label for="student-monthly-fee-{{ $student->id }}">Monthly Fee (Rs.)</label><input id="student-monthly-fee-{{ $student->id }}" type="number" name="monthly_fee" value="{{ old('monthly_fee', $student->monthly_fee ?: 3500) }}" min="0" data-fee-monthly></div>
                    <div class="crud-field"><label for="student-test-fee-{{ $student->id }}">Test Session Fee (Rs.)</label><input id="student-test-fee-{{ $student->id }}" type="number" name="test_session_fee" value="{{ old('test_session_fee', $student->test_session_fee ?: 500) }}" min="0" data-fee-test></div>
                    <div class="crud-field"><label for="student-admission-fee-{{ $student->id }}">One-Time Admission Fee (Rs.) <small class="text-muted">(One-Time)</small></label><input id="student-admission-fee-{{ $student->id }}" type="number" name="admission_fee" value="{{ old('admission_fee', $student->admission_fee ?: 5000) }}" min="0" data-fee-admission></div>
                    <div class="crud-field"><label for="student-total-fee-{{ $student->id }}">Total Monthly Fee (Rs.) <small class="text-muted">(Calculated)</small></label><input id="student-total-fee-{{ $student->id }}" type="number" readonly class="is-readonly-field fw-bold text-success" value="{{ ($student->monthly_fee ?: 3500) + ($student->test_session_fee ?: 500) }}" data-fee-total></div>
                    @endif
                    <div class="crud-field crud-field-full"><label for="student-image-{{ $student->id }}">Replace Image</label><div class="current-image-row"><img src="{{ $student->image && $student->image !== 'dummy.jpeg' ? asset('storage/'.$student->image) : asset('upload/dummy.jpeg') }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" alt="Current student image"><input id="student-image-{{ $student->id }}" type="file" name="image" accept="image/jpeg,image/png"> </div>@error('image')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field crud-field-full student-subject-picker" data-subject-picker>
                        <div class="student-subject-picker-heading">
                            <div>
                                <label>Subjects</label>
                                <p>Choose a saved subject or type a new one. Add as many as needed.</p>
                            </div>
                            <button type="button" class="student-subject-add" data-add-subject><i class="bi bi-plus-lg" aria-hidden="true"></i> Add subject</button>
                        </div>
                        <div class="student-subject-rows" data-subject-rows>
                            @foreach($studentSubjects as $index => $studentSubject)
                                <div class="student-subject-row">
                                    <div class="student-subject-combobox" data-subject-combobox>
                                        <input id="student-subject-{{ $student->id }}-{{ $index }}" type="text" name="subjects[]" value="{{ $studentSubject }}" placeholder="Select or enter a subject" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" data-subject-input>
                                        <button type="button" class="student-subject-toggle" data-subject-toggle aria-label="Show saved subjects"><i class="bi bi-chevron-down" aria-hidden="true"></i></button>
                                        <div class="student-subject-menu" role="listbox" data-subject-menu hidden></div>
                                    </div>
                                    <button type="button" class="student-subject-remove" data-remove-subject aria-label="Remove subject"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                                </div>
                            @endforeach
                        </div>
                        @error('subjects')<span class="crud-field-error">{{ $message }}</span>@enderror
                        @error('subjects.*')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="modal-primary-btn crud-submit">Update Student</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif


</div>

{{-- PAGINATION STYLES --}}

@endsection