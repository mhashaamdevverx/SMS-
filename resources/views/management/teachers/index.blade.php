@extends('layout')

@section('content')

<div class="container">
    <div class="student-header flex-between-center-mb24">
        <div class="flex-center-gap12-wrap">
            <h1 class="m-0">Teacher Data</h1>
            <span class="total-count-pill">
                <i class="bi bi-people-fill fs-14"></i> Total: {{ $teachers->total() }}
            </span>
        </div>
        @if($currentUser->hasPermission('teachers.create'))
            <a href="/add-teacher" class="student-add-btn">+ Add Teacher</a>
        @endif
    </div>

    @if(session('success'))
        <div class="success auto-hide-message">{{ session('success') }}</div>
    @endif
    @if($currentUser->isAdmin() && session('issued_credentials'))
        <div class="success auto-hide-message">
            Temporary teacher sign-in: <strong>{{ session('issued_credentials.email') }}</strong> · <strong>{{ session('issued_credentials.password') }}</strong>.
            Share privately; password change is required at first sign-in.
        </div>
    @endif

    <div class="unified-table-card">
        <div class="table-responsive-wrapper">
            <table class="student-table unified-table">
                <thead>
                    <tr>
                        <th>Teacher Name</th>
                        <th>Image</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Course/Subject</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
            <tr>
                <td><strong>{{ $teacher->teacher_name }}</strong></td>
                <td class="table-image-cell">
                    <img
                        src="{{ $teacher->teacher_image ? asset('storage/'.$teacher->teacher_image) : asset('upload/DPF.jpeg') }}"
                        alt="Teacher Image"
                        class="table-avatar"
                    >
                </td>
                <td>{{ $teacher->teacher_email }}</td>
                <td>{{ $teacher->teacher_phone }}</td>
                <td>
                    <span class="badge badge-info-teal">
                        {{ $teacher->course ?? 'N/A' }}
                    </span>
                </td>
                <td class="table-actions">
                    {{-- VIEW BUTTON (EYE ICON) --}}
                    <button type="button" class="icon-action view-action" data-crud-modal-open="view-teacher-modal-{{ $teacher->id }}" title="View Teacher Details" aria-label="View Teacher Details">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                    {{-- EDIT BUTTON --}}
                    @if($currentUser->hasPermission('teachers.update'))
                        <button type="button" class="icon-action edit-action" data-crud-modal-open="teacher-modal-{{ $teacher->id }}" title="Edit teacher" aria-label="Edit teacher"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                    @endif
                    @if($currentUser->hasPermission('teachers.delete'))
                        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" data-crud-action="delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-action delete-action" title="Delete teacher" aria-label="Delete teacher"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION LINKS (10 PER PAGE) --}}
    @if($teachers->total() > 0)
        <div class="flex-between-center-mt18">
            <p class="student-pagination-info">
                Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }} of {{ $teachers->total() }} teachers
            </p>

            @if($teachers->hasPages())
                <div class="student-pagination">
                    {{ $teachers->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif

    {{-- VIEW & EDIT MODALS FOR EACH TEACHER --}}
    @foreach($teachers as $teacher)

        {{-- VIEW TEACHER DETAIL MODAL --}}
        <div class="crud-backdrop" id="view-teacher-modal-{{ $teacher->id }}" hidden>
            <section class="crud-modal max-w-580" role="dialog" aria-modal="true">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>
                <h2><i class="bi bi-person-badge color-sky"></i> Teacher Details</h2>
                
                <div class="teacher-detail-card">
                    <img src="{{ $teacher->teacher_image ? asset('storage/'.$teacher->teacher_image) : asset('upload/DPF.jpeg') }}" class="teacher-detail-img">
                    <div>
                        <h3 class="teacher-detail-name">{{ $teacher->teacher_name }}</h3>
                        <p class="teacher-detail-email"><i class="bi bi-envelope"></i> {{ $teacher->teacher_email }}</p>
                        <p class="teacher-detail-phone"><i class="bi bi-telephone"></i> {{ $teacher->teacher_phone }}</p>
                        @if($currentUser->isAdmin() && $teacher->loginAccount?->must_change_password)
                            <p class="teacher-detail-phone">Temporary password: Teacher@123</p>
                            <small>Password change is required at first sign-in.</small>
                        @endif
                    </div>
                </div>

                <h4 class="teacher-detail-subhead">Assigned Classes & Subjects/Courses:</h4>
                <table class="teacher-classes-tbl">
                    <thead>
                        <tr class="teacher-tbl-head-row">
                            <th class="teacher-tbl-td">Class & Section</th>
                            <th class="teacher-tbl-td">Subject / Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teacher->assignments as $asg)
                            <tr>
                                <td class="teacher-tbl-td">
                                    <span class="class-badge-pill">
                                        {{ $asg->class_name }} {{ $asg->section_name ? '('.$asg->section_name.')' : '' }}
                                    </span>
                                </td>
                                <td class="teacher-tbl-td-bold">
                                    {{ $asg->subject_or_course }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="teacher-tbl-empty-td">
                                    Primary Course/Subject: <strong>{{ $teacher->course }}</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="text-right">
                    <button type="button" class="modal-secondary-btn" data-crud-modal-close>Close</button>
                </div>
            </section>
        </div>

        @if($currentUser->hasPermission('teachers.update'))
        {{-- EDIT TEACHER MODAL --}}
        <div class="crud-backdrop" id="teacher-modal-{{ $teacher->id }}" data-open="{{ session('edit_modal') === 'teacher-'.$teacher->id ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="teacher-modal-title-{{ $teacher->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit teacher dialog">&times;</button>
                <h2 id="teacher-modal-title-{{ $teacher->id }}">Edit Teacher</h2>
                <form action="{{ route('teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="crud-field">
                        <label for="teacher-name-{{ $teacher->id }}">Teacher Name</label>
                        <input id="teacher-name-{{ $teacher->id }}" type="text" name="teacher_name" value="{{ old('teacher_name', $teacher->teacher_name) }}" class="@error('teacher_name') input-error @enderror" required>
                        @error('teacher_name')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-field">
                        <label for="teacher-email-{{ $teacher->id }}">Teacher Email</label>
                        <input id="teacher-email-{{ $teacher->id }}" type="email" name="teacher_email" value="{{ old('teacher_email', $teacher->teacher_email) }}" placeholder="ali.khan@gmail.com" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Must be a valid @gmail.com address (e.g. ali.khan@gmail.com)" class="@error('teacher_email') input-error @enderror" required>
                        @error('teacher_email')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-field">
                        <label for="teacher-phone-{{ $teacher->id }}">Teacher Phone</label>
                        <input id="teacher-phone-{{ $teacher->id }}" type="text" name="teacher_phone" value="{{ old('teacher_phone', $teacher->teacher_phone) }}" class="@error('teacher_phone') input-error @enderror" required>
                        @error('teacher_phone')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-field">
                        <label for="assigned-class-{{ $teacher->id }}">Assigned Class</label>
                        <select id="assigned-class-{{ $teacher->id }}" name="assigned_class" class="form-control">
                            <option value="">Select Class</option>
                            @if(isset($classesList))
                                @foreach($classesList as $cls)
                                    @php $val = $cls->class_name . ($cls->section_name ? '|' . $cls->section_name : ''); @endphp
                                    <option value="{{ $val }}" {{ ($teacher->assignments->first() && $teacher->assignments->first()->class_name == $cls->class_name) ? 'selected' : '' }}>
                                        {{ $cls->class_name }} {{ $cls->section_name ? '('.$cls->section_name.')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="crud-field crud-field-full">
                        <label for="subject-course-{{ $teacher->id }}">Course / Subject</label>
                        <select id="subject-course-{{ $teacher->id }}" name="subject_course_selected" class="form-control">
                            <option value="">Select Course / Subject</option>
                            @if(isset($subjectCourseOptions))
                                @foreach($subjectCourseOptions as $opt)
                                    @php 
                                        $assignedSubjs = $teacher->assignments->pluck('subject_or_course')->toArray();
                                        $isSelected = in_array($opt, $assignedSubjs) || str_contains($teacher->course ?? '', $opt);
                                    @endphp
                                    <option value="{{ $opt }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $opt }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="crud-field crud-field-full">
                        <label for="course-text-{{ $teacher->id }}">Other Course / Subject</label>
                        <input id="course-text-{{ $teacher->id }}" type="text" name="course" value="{{ old('course', $teacher->course) }}" class="@error('course') input-error @enderror">
                    </div>

                    <div class="crud-field crud-field-full">
                        <label for="teacher-image-{{ $teacher->id }}">Replace Image</label>
                        <div class="current-image-row">
                            <img src="{{ $teacher->teacher_image ? asset('storage/'.$teacher->teacher_image) : asset('upload/DPF.jpeg') }}" alt="Current teacher image">
                            <input id="teacher-image-{{ $teacher->id }}" type="file" name="teacher_image" accept="image/jpeg,image/png,image/webp">
                        </div>
                        @error('teacher_image')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="modal-primary-btn crud-submit">Update Teacher</button>
                </form>
            </section>
        </div>
        @endif
    @endforeach
</div>

@endsection
