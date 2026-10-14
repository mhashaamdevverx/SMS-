@extends('layout')

@section('content')

<div class="container">

    {{-- HEADER --}}
    <div class="student-header flex-between-center-mb24" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div class="flex-center-gap12-wrap" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <h1 class="m-0">Arrangements</h1>
            <span class="total-count-pill">
                <i class="bi bi-grid-fill fs-14" aria-hidden="true"></i> Total: {{ $arrangements->total() }}
            </span>
        </div>
        @if($currentUser->hasPermission('arrangements.create'))<a href="{{ route('arrangements.create') }}" class="student-add-btn">+ Add New Arrangement</a>@endif
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div id="successMessage" class="success auto-hide-message">
            {{ session('success') }}
        </div>
    @endif

    {{-- ARRANGEMENTS TABLE --}}
    <div class="unified-table-card">
        <div class="table-responsive-wrapper">
            <table class="student-table unified-table">
                <thead>
                    <tr>
                        <th>Chair No</th>
                        <th>Class No / Room</th>
                        <th>Std. ID</th>
                        <th>Student Class / Section</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($arrangements as $arrangement)
                    <tr>
                        <td><strong>{{ $arrangement->chairs }}</strong></td>
                        <td>{{ $arrangement->room }}</td>
                        <td>{{ $arrangement->roll_no }}</td>
                        <td>
                            @if($arrangement->classRecord)
                                <span class="badge bg-light text-dark border">{{ $arrangement->classRecord->class_name }} - {{ $arrangement->classRecord->section_name }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="table-actions text-center">
                            @if($currentUser->hasPermission('arrangements.update'))<button type="button" class="icon-action edit-action" data-crud-modal-open="arrangement-modal-{{ $arrangement->id }}" title="Edit arrangement" aria-label="Edit arrangement"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>@endif
                            @if($currentUser->hasPermission('arrangements.delete'))
                            <form action="{{ route('arrangements.destroy', $arrangement) }}" method="POST" data-crud-action="delete" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-action delete-action" title="Delete arrangement" aria-label="Delete arrangement">
                                    <i class="bi bi-trash3" aria-hidden="true"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="student-filter-empty text-center py-4 text-muted">No arrangements added yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($arrangements->total() > 0)
        <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
            <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                Showing {{ $arrangements->firstItem() }} to {{ $arrangements->lastItem() }} of {{ $arrangements->total() }} arrangements
            </p>

            @if($arrangements->hasPages())
                <div class="student-pagination">
                    {{ $arrangements->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif

    {{-- EDIT MODALS --}}
    @if($currentUser->hasPermission('arrangements.update'))
    @foreach($arrangements as $arrangement)
        @php
            $isEditing = session('edit_modal') === 'arrangement-'.$arrangement->id;
            $val = fn ($field) => $isEditing ? old($field, $arrangement->$field) : $arrangement->$field;
        @endphp
        <div class="crud-backdrop" id="arrangement-modal-{{ $arrangement->id }}" data-open="{{ $isEditing ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="arrangement-modal-title-{{ $arrangement->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit arrangement dialog">&times;</button>
                <h2 id="arrangement-modal-title-{{ $arrangement->id }}">Edit Arrangement</h2>

                <form action="{{ route('arrangements.update', $arrangement) }}" method="POST" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')

                    <div class="crud-field">
                        <label for="arr-chairs-{{ $arrangement->id }}">Chairs</label>
                        <input id="arr-chairs-{{ $arrangement->id }}" type="number" name="chairs" min="1" max="1000" value="{{ $val('chairs') }}" class="@if($isEditing) @error('chairs') input-error @enderror @endif" required>
                        @if($isEditing) @error('chairs')<span class="crud-field-error">{{ $message }}</span>@enderror @endif
                    </div>

                    <div class="crud-field">
                        <label for="arr-room-{{ $arrangement->id }}">Room</label>
                        <input id="arr-room-{{ $arrangement->id }}" type="text" name="room" maxlength="50" value="{{ $val('room') }}" class="@if($isEditing) @error('room') input-error @enderror @endif" required>
                        @if($isEditing) @error('room')<span class="crud-field-error">{{ $message }}</span>@enderror @endif
                    </div>

                    <div class="crud-field">
                        <label for="arr-roll-{{ $arrangement->id }}">Roll No</label>
                        <input id="arr-roll-{{ $arrangement->id }}" type="text" name="roll_no" maxlength="20" value="{{ $val('roll_no') }}" class="@if($isEditing) @error('roll_no') input-error @enderror @endif" required>
                        @if($isEditing) @error('roll_no')<span class="crud-field-error">{{ $message }}</span>@enderror @endif
                    </div>

                    <div class="crud-field">
                        <label for="arr-class-{{ $arrangement->id }}">Class</label>
                        <select id="arr-class-{{ $arrangement->id }}" name="class_id" class="arrangement-select @if($isEditing) @error('class_id') input-error @enderror @endif" required>
                            @foreach($classOptions as $option)
                                <option value="{{ $option->id }}" @selected((string) $val('class_id') === (string) $option->id)>
                                    {{ $option->class_name }} - {{ $option->section_name }}
                                </option>
                            @endforeach
                        </select>
                        @if($isEditing) @error('class_id')<span class="crud-field-error">{{ $message }}</span>@enderror @endif
                    </div>

                    <button type="submit" class="modal-primary-btn crud-submit">Update Arrangement</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif

</div>


@endsection