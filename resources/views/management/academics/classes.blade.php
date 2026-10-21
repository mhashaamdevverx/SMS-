@extends('admin.layout', ['currentPage' => 'academics', 'pageTitle' => 'Classes & Enrolment'])

@section('content')
<div class="classes-page-wrapper" data-assigned-sections="{{ json_encode($assignedSectionsByClass) }}">
    <div class="classes-container">
        <div class="student-header flex-between-center-mb24">
            <div class="flex-center-gap12-wrap">
                <h1 class="m-0">Class Data</h1>
                <span class="total-count-pill">
                    <i class="bi bi-building-fill fs-14" aria-hidden="true"></i> Total: {{ $classes->count() }}
                </span>
            </div>
            @if($currentUser->hasPermission('classes.create'))<a href="{{ route('classes.create') }}" class="student-add-btn">+ Add New Class</a>@endif
        </div>

        @if(session('success'))
            <div id="successMessage" class="success auto-hide-message">{{ session('success') }}</div>
        @endif

        <div class="unified-table-card classes-table-card">
            <div class="table-responsive-wrapper">
                <table class="student-table unified-table classes-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 15%;">Class No</th>
                            <th class="text-center" style="width: 25%;">Class</th>
                            <th class="text-center" style="width: 20%;">Chairs</th>
                            <th class="text-center" style="width: 25%;">Section Name</th>
                            <th class="text-center" style="width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td class="text-center">
                                <span class="badge badge-info-teal fw-bold px-3 py-2">
                                    {{ $class->class_no }}
                                </span>
                            </td>
                            <td class="text-center"><strong class="text-dark">{{ $class->class_name }}</strong></td>
                            <td class="text-center">{{ $class->chairs ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    {{ $class->section_name }}
                                </span>
                            </td>
                            <td class="table-actions text-center">
                                @if($currentUser->hasPermission('classes.update'))<button type="button" class="icon-action edit-action" data-crud-modal-open="class-modal-{{ $class->id }}" title="Edit class" aria-label="Edit class"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>@endif
                                @if($currentUser->hasPermission('classes.delete'))
                                    <form action="{{ route('classes.destroy', $class) }}" method="POST" data-crud-action="delete" class="d-inline">@csrf @method('DELETE')<button type="submit" class="icon-action delete-action" title="Delete class" aria-label="Delete class"><i class="bi bi-trash3" aria-hidden="true"></i></button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="student-filter-empty text-center py-4">No classes added yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($classes->total() > 0)
            <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
                <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                    Showing {{ $classes->firstItem() }} to {{ $classes->lastItem() }} of {{ $classes->total() }} classes
                </p>

                @if($classes->hasPages())
                    <div class="student-pagination">
                        {{ $classes->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if($currentUser->hasPermission('classes.update'))
    @foreach($classes as $class)
        @php
            $isEditing = session('edit_modal') === 'class-'.$class->id;
            $currentSection = $isEditing ? old('section_name', $class->section_name) : $class->section_name;
            $sectionOptions = $sections->contains($class->section_name) ? $sections : $sections->prepend($class->section_name)->unique()->values();
        @endphp
        <div class="crud-backdrop" id="class-modal-{{ $class->id }}" data-open="{{ $isEditing ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="class-modal-title-{{ $class->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit class dialog">&times;</button>
                <h2 id="class-modal-title-{{ $class->id }}">Edit Class</h2>
                <form action="{{ route('classes.update', $class) }}" method="POST" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    <div class="crud-field">
                        <label for="class-no-{{ $class->id }}">Class No</label>
                        <input id="class-no-{{ $class->id }}" type="number" value="{{ $class->class_no }}" readonly aria-readonly="true">
                    </div>
                    <div class="crud-field">
                        <label for="class-name-{{ $class->id }}">Class</label>
                        <input id="class-name-{{ $class->id }}" type="text" name="class_name" list="class-options-{{ $class->id }}" maxlength="50" value="{{ $isEditing ? old('class_name', $class->class_name) : $class->class_name }}" class="class-name-input @if($isEditing) @error('class_name') input-error @enderror @endif" data-original-class="{{ $class->class_name }}" required>
                        <datalist id="class-options-{{ $class->id }}">
                            @foreach($classOptions as $classOption)
                                <option value="{{ $classOption }}"></option>
                            @endforeach
                        </datalist>
                        @if($isEditing) @error('class_name')<span class="crud-field-error">{{ $message }}</span>@enderror @endif
                    </div>
                    <div class="crud-field">
                        <label for="chairs-{{ $class->id }}">Chairs</label>
                        <input id="chairs-{{ $class->id }}" type="number" name="chairs" min="1" max="10000" value="{{ $isEditing ? old('chairs', $class->chairs) : $class->chairs }}" class="@if($isEditing) @error('chairs') input-error @enderror @endif" required>
                        @if($isEditing) @error('chairs')<span class="crud-field-error">{{ $message }}</span>@enderror @endif
                    </div>
                    <div class="crud-field">
                        <label for="class-section-{{ $class->id }}">Section Name</label>
                        <select id="class-section-{{ $class->id }}" name="section_name" class="class-section-select @if($isEditing) @error('section_name') input-error @enderror @endif" data-original-section="{{ $class->section_name }}" required>
                            @foreach($sectionOptions as $section)
                                <option value="{{ $section }}" @selected($currentSection === $section)>{{ $section }}</option>
                            @endforeach
                        </select>
                        @if($isEditing) @error('section_name')<span class="crud-field-error">{{ $message }}</span>@enderror @endif
                    </div>
                    <button type="submit" class="modal-primary-btn crud-submit">Update Class</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif
</div>
@endsection