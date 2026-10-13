@extends('layout')

@section('content')
<div class="subjects-page-wrapper">
    <div class="subjects-container">
        <div class="student-header flex-between-center-mb24">
            <div class="flex-center-gap12-wrap">
                <h1 class="m-0">Subject Data</h1>
                <span class="total-count-pill">
                    <i class="bi bi-book-fill fs-14" aria-hidden="true"></i> Total: {{ $subjects->count() }}
                </span>
            </div>
            @if($currentUser->hasPermission('subjects.create'))<a href="{{ route('subjects.add', filled($selectedClassId) ? ['class_id' => $selectedClassId] : []) }}" class="student-add-btn">+ Add Subject</a>@endif
        </div>

        @if(session('success'))
            <div class="success auto-hide-message">{{ session('success') }}</div>
        @endif

        {{-- ADVANCED SEARCH & CLASS FILTER BAR --}}
        <div class="subject-filter-card">
            <form action="{{ route('subjects.list') }}" method="GET" class="subject-filter-form-row" id="subject-filter-form">
                <div class="subject-search-wrapper">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search"
                           id="subject-search-input"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search by subject or class..."
                           autocomplete="off"
                           aria-label="Search subjects or class"
                           class="subject-search-input">
                </div>

                <div class="subject-dropdown-wrapper">
                    <i class="bi bi-mortarboard-fill subject-dropdown-icon" aria-hidden="true"></i>
                    <select id="subject-class-select" name="class_id" class="subject-class-select" aria-label="Filter subjects by class">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected((string)$selectedClassId === (string)$class->id)>
                                {{ $class->class_name }} ({{ $class->section_name }})
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down subject-dropdown-arrow" aria-hidden="true"></i>
                </div>

                <a href="{{ route('subjects.list') }}" id="subject-reset-btn" class="student-filter-btn text-decoration-none" title="Reset filters">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    Reset
                </a>
            </form>
        </div>

        @if(filled($selectedClassId) || $search !== '')
            <div class="subject-filter-info">
                <div>
                    @if(filled($selectedClassId))
                        @php
                            $activeClass = $classes->firstWhere('id', (int) $selectedClassId);
                        @endphp
                        @if($activeClass)
                            <span>Showing subjects for: <strong class="subject-active-filter-badge"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i> {{ $activeClass->class_name }} ({{ $activeClass->section_name }})</strong></span>
                        @endif
                    @endif
                    @if($search !== '')
                        <span class="ml-8">Matching search: "<strong>{{ $search }}</strong>"</span>
                    @endif
                </div>
                <div>
                    <span id="subject-count-display"><strong>{{ $subjects->total() }}</strong> subject{{ $subjects->total() === 1 ? '' : 's' }}</span>
                </div>
            </div>
        @endif

        {{-- SUBJECTS TABLE --}}
        <div class="unified-table-card">
            <div class="table-responsive-wrapper">
                <table class="student-table unified-table" id="subjects-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50%;">Subject Name</th>
                            <th class="text-center" style="width: 30%;">Total Marks</th>
                            <th class="text-center" style="width: 20%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr class="subject-row"
                                data-subject-name="{{ strtolower($subject->subject_name) }}"
                                data-class-id="{{ $subject->class_id }}"
                                data-class-label="{{ strtolower($subject->class_label ?? '') }}">
                                <td class="subject-name-cell text-center">
                                    <strong class="text-dark">{{ $subject->subject_name }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info-teal fw-bold px-3 py-2">
                                        {{ $subject->total_marks }} Marks
                                    </span>
                                </td>
                                <td class="table-actions text-center">
                                    @if($currentUser->hasPermission('subjects.update'))<button type="button" class="icon-action edit-action" data-crud-modal-open="subject-modal-{{ $subject->id }}" title="Edit subject" aria-label="Edit subject"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>@endif
                                    @if($currentUser->hasPermission('subjects.delete'))
                                    <form action="{{ route('subjects.destroy', $subject) }}" method="POST" data-crud-action="delete" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        @if(filled($selectedClassId))
                                            <input type="hidden" name="filter_class_id" value="{{ $selectedClassId }}">
                                        @endif
                                        @if($search !== '')
                                            <input type="hidden" name="filter_search" value="{{ $search }}">
                                        @endif
                                        <button type="submit" class="icon-action delete-action" title="Delete subject" aria-label="Delete subject">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="server-empty-row">
                                <td colspan="3" class="student-filter-empty text-center-p36-20">
                                    <div class="flex-col-center-gap8">
                                        <i class="bi bi-journal-x icon-lg-muted" aria-hidden="true"></i>
                                        <strong class="text-navy-16">No subjects found</strong>
                                        <p class="text-sub-13">
                                            @if(filled($selectedClassId))
                                                No subjects registered for this class yet.
                                            @else
                                                No subjects found matching your criteria.
                                            @endif
                                        </p>
                                                     @if($currentUser->hasPermission('subjects.create'))<a href="{{ route('subjects.add', filled($selectedClassId) ? ['class_id' => $selectedClassId] : []) }}"
                                           class="student-filter-btn student-filter-clear mt-10 text-decoration-none">
                                            <i class="bi bi-plus-circle" aria-hidden="true"></i>
                                            Add Subject for this Class
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="client-no-match-row" class="d-none">
                            <td colspan="3" class="student-filter-empty text-center-p32-20">
                                <i class="bi bi-search icon-md-muted-block" aria-hidden="true"></i>
                                <strong class="text-navy-15">No matching subjects found</strong>
                                <p class="text-sub-13-mt4">Try typing a different subject or class name.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($subjects->total() > 0)
            <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
                <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                    Showing {{ $subjects->firstItem() }} to {{ $subjects->lastItem() }} of {{ $subjects->total() }} subjects
                </p>

                @if($subjects->hasPages())
                    <div class="student-pagination">
                        {{ $subjects->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- EDIT SUBJECT MODALS --}}
    @if($currentUser->hasPermission('subjects.update'))
    @foreach($subjects as $subject)
        <div class="crud-backdrop" id="subject-modal-{{ $subject->id }}" data-open="{{ session('edit_modal') === 'subject-'.$subject->id ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="subject-modal-title-{{ $subject->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit subject dialog">&times;</button>
                <h2 id="subject-modal-title-{{ $subject->id }}">Edit Subject</h2>
                <form action="{{ route('subjects.update', $subject) }}" method="POST" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    @if(filled($selectedClassId))
                        <input type="hidden" name="filter_class_id" value="{{ $selectedClassId }}">
                    @endif
                    @if($search !== '')
                        <input type="hidden" name="filter_search" value="{{ $search }}">
                    @endif

                    <div class="crud-field">
                        <label for="subject-class-{{ $subject->id }}">Class &amp; Section</label>
                        <select id="subject-class-{{ $subject->id }}" name="class_id" class="class-section-select @error('class_id') input-error @enderror" required>
                            <option value="" disabled>Select class &amp; section</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" @selected(old('class_id', $subject->class_id) == $c->id)>
                                    {{ $c->class_name }} ({{ $c->section_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-field">
                        <label for="subject-name-{{ $subject->id }}">Subject Name</label>
                        <input id="subject-name-{{ $subject->id }}" type="text" name="subject_name" value="{{ old('subject_name', $subject->subject_name) }}" class="@error('subject_name') input-error @enderror" required>
                        @error('subject_name')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-field">
                        <label for="subject-total-marks-{{ $subject->id }}">Total Marks</label>
                        <input id="subject-total-marks-{{ $subject->id }}" type="number" name="total_marks" min="1" max="100000" value="{{ old('total_marks', $subject->total_marks) }}" class="@error('total_marks') input-error @enderror" required>
                        @error('total_marks')<span class="crud-field-error">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="modal-primary-btn crud-submit">Update Subject</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif
</div>
@endsection