@extends('layout')

@section('content')

<div class="sections-page-wrapper">
    <div class="sections-container">
        <div class="student-header flex-between-center-mb24">
            <div class="flex-center-gap12-wrap">
                <h1 class="m-0">Section Data</h1>
                <span class="total-count-pill">
                    <i class="bi bi-grid-3x3-gap-fill fs-14" aria-hidden="true"></i> Total: {{ $sections->total() }}
                </span>
            </div>
            @if($currentUser->hasPermission('sections.create'))<a href="/add-section" class="student-add-btn">+ Add Section</a>@endif
        </div>

        @if(session('success'))
            <div class="success auto-hide-message">{{ session('success') }}</div>
        @endif

        <div class="unified-table-card sections-table-card">
            <div class="table-responsive-wrapper">
                <table class="student-table unified-table sections-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 75%;">Section Name</th>
                            <th class="text-center" style="width: 25%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                            <tr>
                                <td class="text-center">
                                    <span class="badge badge-info-teal fw-bold px-3 py-2">
                                        {{ $section->section_name }}
                                    </span>
                                </td>
                                <td class="table-actions text-center">
                                    @if($currentUser->hasPermission('sections.update'))<button type="button" class="icon-action edit-action" data-crud-modal-open="section-modal-{{ $section->id }}" title="Edit section" aria-label="Edit section"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>@endif
                                    @if($currentUser->hasPermission('sections.delete'))
                                        <form action="{{ route('sections.destroy', $section) }}" method="POST" data-crud-action="delete" class="d-inline">@csrf @method('DELETE')<button type="submit" class="icon-action delete-action" title="Delete section" aria-label="Delete section"><i class="bi bi-trash3" aria-hidden="true"></i></button></form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">No sections found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($sections->total() > 0)
            <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
                <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                    Showing {{ $sections->firstItem() }} to {{ $sections->lastItem() }} of {{ $sections->total() }} sections
                </p>

                @if($sections->hasPages())
                    <div class="student-pagination">
                        {{ $sections->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if($currentUser->hasPermission('sections.update'))
    @foreach($sections as $section)
        <div class="crud-backdrop" id="section-modal-{{ $section->id }}" data-open="{{ session('edit_modal') === 'section-'.$section->id ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="section-modal-title-{{ $section->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit section dialog">&times;</button>
                <h2 id="section-modal-title-{{ $section->id }}">Edit Section</h2>
                <form action="{{ route('sections.update', $section) }}" method="POST" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    <div class="crud-field crud-field-full"><label for="section-name-{{ $section->id }}">Section Name</label><input id="section-name-{{ $section->id }}" type="text" name="section_name" value="{{ old('section_name', $section->section_name) }}" class="@error('section_name') input-error @enderror" required>@error('section_name')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <button type="submit" class="modal-primary-btn crud-submit">Update Section</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif
</div>

@endsection