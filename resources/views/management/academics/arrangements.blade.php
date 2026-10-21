@extends('admin.layout', ['currentPage' => 'academics', 'pageTitle' => 'Seating Arrangements'])

@section('content')
<div class="academic-operations-hub">
    {{-- TOP COMMAND BAR & HEADER (EXACT FIRST IMAGE AESTHETIC) --}}
    <div class="admin-card mb-4 border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
        <div class="admin-card-header d-flex flex-wrap align-items-center justify-content-between p-4 border-bottom" style="gap: 16px;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 11.5px;">
                        <i class="bi bi-mortarboard-fill me-1"></i> Academic Operations
                    </span>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 rounded-pill" style="font-size: 11.5px;">
                        Session {{ now()->format('Y') }} Active
                    </span>
                </div>
                <h3 class="admin-card-title mb-1 fw-bold fs-4 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3-fill" style="color: #8B5CF6;"></i> Examination Seating &amp; Hall Arrangements
                </h3>
                <p class="text-muted small mb-0">Unified administration hub for exam hall desk distributions, classroom quotas, and student seat assignments.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/admin/academics" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Academics
                </a>
                <a href="{{ route('arrangements.create') }}" class="admin-btn admin-btn-primary admin-btn-sm fw-bold" style="background: #8B5CF6; border-color: #8B5CF6;">
                    <i class="bi bi-plus-circle-fill me-1"></i> Generate Arrangement
                </a>
                <a href="/roll-slips" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-file-earmark-text-fill me-1"></i> View Roll Slips
                </a>
            </div>
        </div>
    </div>

    {{-- SUCCESS & ERROR ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3" style="background: #ECFDF5; border-left: 5px solid #10B981 !important;">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <span class="fw-semibold text-dark small">{{ session('success') }}</span>
        </div>
    @endif

    {{-- MAIN ARRANGEMENTS TABLE CARD --}}
    <div class="admin-card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" style="border-top: 4px solid #8B5CF6 !important;">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #F5F3FF; color: #8B5CF6; display: grid; place-items: center; font-size: 22px;">
                    <i class="bi bi-columns-gap"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Active Hall Seating Plans</h5>
                    <p class="text-muted small mb-0">List of allocated desks, assigned rooms, and matched student roll sequences.</p>
                </div>
            </div>
            <span class="badge bg-purple-subtle text-purple fw-bold px-3 py-2 rounded-pill" style="font-size: 12px; background: #EDE9FE; color: #7C3AED;">
                Total Seats: {{ $arrangements->total() }}
            </span>
        </div>

        <div class="admin-table-wrapper border rounded-3 overflow-hidden">
            <table class="admin-table mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 15%;">Chair / Seat No</th>
                        <th class="text-center" style="width: 15%;">Class Room No</th>
                        <th class="text-center" style="width: 15%;">Std. ID</th>
                        <th style="width: 25%;">Student Name</th>
                        <th class="text-center" style="width: 20%;">Student Class / Section</th>
                        <th class="text-end" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arrangements as $arrangement)
                        @php
                            $rollId = (int)$arrangement->roll_no;
                            if (isset($studentsMap) && is_object($studentsMap) && method_exists($studentsMap, 'get')) {
                                $stdName = $studentsMap->get($rollId)?->name ?? 'Student';
                            } elseif (isset($studentsMap) && is_array($studentsMap) && isset($studentsMap[$rollId])) {
                                $stdName = is_object($studentsMap[$rollId]) ? $studentsMap[$rollId]->name : $studentsMap[$rollId];
                            } else {
                                $stdName = 'Student';
                            }
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                                    Seat #{{ $arrangement->chairs }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border fw-bold px-3 py-1">
                                    <i class="bi bi-door-closed text-muted me-1"></i> Room {{ $arrangement->room }}
                                </span>
                            </td>
                            <td class="text-center">
                                <strong class="text-dark">#{{ $arrangement->roll_no }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #EEF2FF; color: #4F46E5; display: grid; place-items: center; font-size: 12px; font-weight: 700;">
                                        {{ substr($stdName, 0, 1) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $stdName }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($arrangement->classRecord)
                                    <span class="badge bg-info-subtle text-info fw-bold border">
                                        {{ $arrangement->classRecord->class_name }} ({{ $arrangement->classRecord->section_name }})
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    @if($currentUser->hasPermission('arrangements.update'))
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" data-crud-modal-open="arrangement-modal-{{ $arrangement->id }}" title="Edit arrangement">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    @endif
                                    @if($currentUser->hasPermission('arrangements.delete'))
                                        <form action="{{ route('arrangements.destroy', $arrangement) }}" method="POST" data-crud-action="delete" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete seat arrangement">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-diagram-3 fs-2 d-block mb-2 text-muted"></i>
                                <strong>No examination seating arrangements found.</strong>
                                <p class="small text-muted mb-3">Generate classroom seating plans for students before issuing examination roll slips.</p>
                                <a href="{{ route('arrangements.create') }}" class="admin-btn admin-btn-primary admin-btn-sm fw-bold">
                                    <i class="bi bi-plus-circle me-1"></i> Generate Arrangement Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- EXACT ADMIN PANEL PAGINATION --}}
        @if($arrangements instanceof \Illuminate\Pagination\LengthAwarePaginator && $arrangements->total() > 0)
            <div class="admin-pagination-bar mt-4">
                <div class="admin-pagination-info">
                    Showing <strong>{{ $arrangements->firstItem() }}</strong> to <strong>{{ $arrangements->lastItem() }}</strong> of <strong>{{ $arrangements->total() }}</strong> arrangements
                </div>
                <div class="admin-pagination-links">
                    <ul class="pagination mb-0">
                        @if ($arrangements->onFirstPage())
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $arrangements->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                        @endif

                        @foreach ($arrangements->getUrlRange(1, $arrangements->lastPage()) as $page => $url)
                            @if ($page == $arrangements->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @elseif ($page == 1 || $page == $arrangements->lastPage() || abs($page - $arrangements->currentPage()) <= 2)
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @elseif (abs($page - $arrangements->currentPage()) == 3)
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            @endif
                        @endforeach

                        @if ($arrangements->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $arrangements->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
    </div>

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
                    <h2 id="arrangement-modal-title-{{ $arrangement->id }}" class="fs-5 fw-bold mb-3">Edit Seat Arrangement</h2>
                    <form action="{{ route('arrangements.update', $arrangement) }}" method="POST" data-crud-action="update" class="crud-form">
                        @csrf
                        @method('PUT')
                        <div class="crud-field mb-3">
                            <label for="room-{{ $arrangement->id }}" class="form-label fw-bold small">Class Room No</label>
                            <input id="room-{{ $arrangement->id }}" type="text" name="room" value="{{ $val('room') }}" class="form-control" required>
                        </div>
                        <div class="crud-field mb-3">
                            <label for="chairs-{{ $arrangement->id }}" class="form-label fw-bold small">Chair / Seat No</label>
                            <input id="chairs-{{ $arrangement->id }}" type="number" name="chairs" min="1" max="1000" value="{{ $val('chairs') }}" class="form-control" required>
                        </div>
                        <div class="crud-field mb-3">
                            <label for="roll-no-{{ $arrangement->id }}" class="form-label fw-bold small">Student Std. ID</label>
                            <input id="roll-no-{{ $arrangement->id }}" type="text" name="roll_no" value="{{ $val('roll_no') }}" class="form-control" required>
                        </div>
                        <div class="crud-field mb-4">
                            <label for="class-id-{{ $arrangement->id }}" class="form-label fw-bold small">Class &amp; Section</label>
                            <select id="class-id-{{ $arrangement->id }}" name="class_id" class="form-select" required>
                                @foreach($classOptions as $opt)
                                    <option value="{{ $opt->id }}" @selected((int)$val('class_id') === (int)$opt->id)>
                                        {{ $opt->class_name }} / {{ $opt->section_name }} (Room {{ $opt->class_no }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="admin-btn admin-btn-primary w-100 py-2 fw-bold">Update Arrangement</button>
                    </form>
                </section>
            </div>
        @endforeach
    @endif
</div>
@endsection