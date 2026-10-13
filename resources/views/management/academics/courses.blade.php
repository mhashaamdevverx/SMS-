@extends('layout')

@section('content')

<div class="courses-page-wrapper">
    <div class="courses-container">
        <div class="student-header flex-between-center-mb24">
            <div class="flex-center-gap12-wrap">
                <h1 class="m-0">Course Data</h1>
                <span class="total-count-pill">
                    <i class="bi bi-journal-bookmark-fill fs-14" aria-hidden="true"></i> Total: {{ $courses->count() }}
                </span>
            </div>
            @if($currentUser->hasPermission('courses.create'))<a href="/add-course" class="student-add-btn">+ Add Course</a>@endif
        </div>

        @if(session('success'))
            <div class="success auto-hide-message">{{ session('success') }}</div>
        @endif

        <div class="unified-table-card courses-table-card">
            <div class="table-responsive-wrapper">
                <table class="student-table unified-table courses-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 15%;">Course Code</th>
                            <th class="text-center" style="width: 25%;">Course Name</th>
                            <th class="text-center" style="width: 45%;">Course Description</th>
                            <th class="text-center" style="width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td class="text-center">
                                    <span class="badge badge-info-teal fw-bold px-3 py-2">
                                        {{ $course->course_code }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong class="text-dark">{{ $course->course_name }}</strong>
                                </td>
                                <td class="text-center text-secondary">
                                    {{ $course->course_description }}
                                </td>
                                <td class="table-actions text-center">
                                    @if($currentUser->hasPermission('courses.update'))<button type="button" class="icon-action edit-action" data-crud-modal-open="course-modal-{{ $course->id }}" title="Edit course" aria-label="Edit course"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>@endif
                                    @if($currentUser->hasPermission('courses.delete'))
                                        <form action="{{ route('courses.destroy', $course) }}" method="POST" data-crud-action="delete" class="d-inline">@csrf @method('DELETE')<button type="submit" class="icon-action delete-action" title="Delete course" aria-label="Delete course"><i class="bi bi-trash3" aria-hidden="true"></i></button></form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No courses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($courses->total() > 0)
            <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
                <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                    Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} courses
                </p>

                @if($courses->hasPages())
                    <div class="student-pagination">
                        {{ $courses->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if($currentUser->hasPermission('courses.update'))
    @foreach($courses as $course)
        <div class="crud-backdrop" id="course-modal-{{ $course->id }}" data-open="{{ session('edit_modal') === 'course-'.$course->id ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="course-modal-title-{{ $course->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit course dialog">&times;</button>
                <h2 id="course-modal-title-{{ $course->id }}">Edit Course</h2>
                <form action="{{ route('courses.update', $course) }}" method="POST" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    <div class="crud-field"><label for="course-code-{{ $course->id }}">Course Code</label><input id="course-code-{{ $course->id }}" type="text" name="course_code" value="{{ old('course_code', $course->course_code) }}" class="@error('course_code') input-error @enderror" required>@error('course_code')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="course-name-{{ $course->id }}">Course Name</label><input id="course-name-{{ $course->id }}" type="text" name="course_name" value="{{ old('course_name', $course->course_name) }}" class="@error('course_name') input-error @enderror" required>@error('course_name')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field crud-field-full"><label for="course-description-{{ $course->id }}">Course Description</label><textarea id="course-description-{{ $course->id }}" name="course_description" rows="5" class="@error('course_description') input-error @enderror" required>{{ old('course_description', $course->course_description) }}</textarea>@error('course_description')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <button type="submit" class="modal-primary-btn crud-submit">Update Course</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif
</div>

@endsection
