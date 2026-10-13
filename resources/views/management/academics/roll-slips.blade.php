@extends('layout')
@section('content')
<div class="container">
    <div class="student-header ext-style-108">
        <div class="ext-style-109">
            <h1 class="ext-style-110">Roll Number Slips</h1>
            <span class="ext-style-111">
                <i class="bi bi-file-earmark-text-fill ext-style-112"></i> Total: {{ $rollSlips->total() }}
            </span>
        </div>
        @if($currentUser->hasPermission('roll-slips.create'))
        <div class="roll-slip-top-actions ext-style-113">
            <button type="button" class="student-add-btn ext-style-114" data-crud-modal-open="all-downloads-modal">
                <i class="bi bi-file-earmark-arrow-down-fill" aria-hidden="true"></i> All Downloads
            </button>
            <a href="{{ route('roll-slips.create') }}" class="student-add-btn">+ Issue Roll Slips</a>
        </div>
        @endif
    </div>
    @if(session('success'))<div class="success auto-hide-message">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="field-error has-error auto-hide-message ext-style-115"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> {{ $errors->first() }}</div>@endif
    <div class="unified-table-card"><div class="table-responsive-wrapper"><table class="student-table unified-table">
        <thead><tr><th>Student</th><th>Std. ID / Roll No</th><th>Class / Section</th><th>Room</th><th>Chair No</th><th>Exam</th><th>Action</th></tr></thead>
        <tbody>
        @forelse($rollSlips as $slip)
            <tr>
                <td>{{ $slip->student_name }}</td><td>{{ $slip->roll_no }}</td><td>{{ $slip->class_name }} / {{ $slip->section_name }}</td><td>{{ $slip->room_no }}</td><td>{{ $slip->chair_no }}</td><td>{{ $slip->exam?->name }}</td>
                <td class="table-actions">
                    @if($currentUser->hasPermission('roll-slips.update'))
                        <button type="button" class="icon-action edit-action" data-crud-modal-open="roll-slip-modal-{{ $slip->id }}" title="Edit roll slip" aria-label="Edit roll slip"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                    @endif
                    @if($currentUser->hasPermission('roll-slips.delete'))
                        <form action="{{ route('roll-slips.destroy', $slip) }}" method="POST" data-crud-action="delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-action delete-action" title="Delete roll slip" aria-label="Delete roll slip"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                        </form>
                    @endif
                    <a class="icon-action slip-download-action" href="{{ route('roll-slips.show', $slip) }}" title="Open download and print options" aria-label="Open download and print options"><i class="bi bi-download" aria-hidden="true"></i></a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="student-filter-empty">No roll-number slips issued yet.</td></tr>
        @endforelse
        </tbody>
    </table></div></div>

    @if($rollSlips->total() > 0)
        <div class="ext-style-116">
            <p class="student-pagination-info ext-style-110">
                Showing {{ $rollSlips->firstItem() }} to {{ $rollSlips->lastItem() }} of {{ $rollSlips->total() }} roll-number slips
            </p>
            @if($rollSlips->hasPages())
                <div class="student-pagination">
                    {{ $rollSlips->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif
    @if($currentUser->hasPermission('roll-slips.update'))
    @foreach($rollSlips as $slip)
        @php($isEditing = session('edit_modal') === 'roll-slip-'.$slip->id)
        <div class="crud-backdrop" id="roll-slip-modal-{{ $slip->id }}" data-open="{{ $isEditing ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="roll-slip-modal-title-{{ $slip->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit roll slip dialog">&times;</button>
                <h2 id="roll-slip-modal-title-{{ $slip->id }}">Edit Roll Slip</h2>
                <form action="{{ route('roll-slips.update', $slip) }}" method="POST" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    <div class="crud-field"><label for="slip-student-name-{{ $slip->id }}">Student Name</label><input id="slip-student-name-{{ $slip->id }}" name="student_name" value="{{ $isEditing ? old('student_name', $slip->student_name) : $slip->student_name }}" required></div>
                    <div class="crud-field"><label for="slip-roll-no-{{ $slip->id }}">Std. ID / Roll No</label><input id="slip-roll-no-{{ $slip->id }}" type="number" name="roll_no" min="1" value="{{ $isEditing ? old('roll_no', $slip->roll_no) : $slip->roll_no }}" required></div>
                    <div class="crud-field"><label for="slip-class-name-{{ $slip->id }}">Class</label><input id="slip-class-name-{{ $slip->id }}" name="class_name" value="{{ $isEditing ? old('class_name', $slip->class_name) : $slip->class_name }}" required></div>
                    <div class="crud-field"><label for="slip-section-name-{{ $slip->id }}">Section</label><input id="slip-section-name-{{ $slip->id }}" name="section_name" value="{{ $isEditing ? old('section_name', $slip->section_name) : $slip->section_name }}"></div>
                    <div class="crud-field"><label for="slip-room-no-{{ $slip->id }}">Room No</label><input id="slip-room-no-{{ $slip->id }}" name="room_no" value="{{ $isEditing ? old('room_no', $slip->room_no) : $slip->room_no }}" required></div>
                    <div class="crud-field"><label for="slip-chair-no-{{ $slip->id }}">Chair No</label><input id="slip-chair-no-{{ $slip->id }}" type="number" name="chair_no" min="1" value="{{ $isEditing ? old('chair_no', $slip->chair_no) : $slip->chair_no }}" required></div>
                    <button type="submit" class="modal-primary-btn crud-submit">Update Roll Slip</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif

    {{-- ALL DOWNLOADS BY CLASS MODAL --}}
    @if($currentUser->hasPermission('roll-slips.create'))
    <div class="crud-backdrop" id="all-downloads-modal" hidden>
        <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="all-downloads-modal-title">
            <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>
            <div class="modal-heading">
                <p class="modal-kicker">Bulk Downloads</p>
                <h2 id="all-downloads-modal-title">All Downloads (Class Roll Slips)</h2>
                <p>Select a class to download all students' roll number slips in a single printable PDF document.</p>
            </div>
            <form action="{{ route('roll-slips.class-download') }}" method="GET" class="crud-form" id="bulk-download-form">
                <div class="crud-field">
                    <label for="all-download-class">Which class?</label>
                    <select id="all-download-class" name="class" class="student-section-select" required>
                        <option value="" disabled selected>-- Select a Class --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" id="bulk-download-btn" class="modal-primary-btn crud-submit ext-style-117">
                    <i class="bi bi-file-earmark-arrow-down-fill" aria-hidden="true"></i> Download All Roll No Slips
                </button>
            </form>
        </section>
    </div>
    @endif
</div>



@endsection