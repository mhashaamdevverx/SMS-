@extends('admin.layout', ['currentPage' => 'academics', 'pageTitle' => 'Issue Roll Slips'])

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
                    <i class="bi bi-ticket-perforated-fill" style="color: #4F46E5;"></i> Examination Datesheet &amp; Roll Slips
                </h3>
                <p class="text-muted small mb-0">Select class grade, schedule examination paper dates, and generate verified student hall tickets.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/admin/academics" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Academics
                </a>
                <a href="{{ route('roll-slips.index') }}" class="admin-btn admin-btn-primary admin-btn-sm fw-bold">
                    <i class="bi bi-file-earmark-text-fill me-1"></i> View All Roll Slips
                </a>
                <a href="{{ route('arrangements.index') }}" class="admin-btn admin-btn-info text-white admin-btn-sm fw-bold" style="background: #06B6D4; border-color: #06B6D4;">
                    <i class="bi bi-diagram-3-fill me-1"></i> Seating Arrangements
                </a>
            </div>
        </div>
    </div>

    {{-- SYSTEM ALERTS & ARRANGEMENT STATUS NOTICES --}}
    @if($arrangementCount === 0)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: #FFFBEB; border-left: 5px solid #F59E0B !important;">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: grid; place-items: center; font-size: 22px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">No Seating Arrangements Found</h6>
                    <p class="text-muted small mb-0">You must generate classroom seating arrangements before issuing roll number slips to students.</p>
                </div>
            </div>
            <a href="{{ route('arrangements.create') }}" class="admin-btn admin-btn-primary fw-bold px-3 py-2">
                <i class="bi bi-plus-circle me-1"></i> Generate Arrangement
            </a>
        </div>
    @elseif($availableArrangementCount === 0)
        <div class="alert alert-info border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center gap-3" style="background: #EFF6FF; border-left: 5px solid #3B82F6 !important;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #DBEAFE; color: #2563EB; display: grid; place-items: center; font-size: 22px;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-6">All Roll Slips Are Already Issued</h6>
                <p class="text-muted small mb-0">Every student with an arrangement has already received an active roll slip. To reissue, delete an existing slip from the list.</p>
            </div>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #FEF2F2; border-left: 4px solid #EF4444 !important;">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                <strong class="text-danger small">Please correct the following:</strong>
            </div>
            <ul class="mb-0 small text-danger ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MAIN CONTENT AREA (FULL-WIDTH, HIGH-END RESPONSIVE GRID) --}}
    <div class="row g-4 mb-4">
        {{-- FORM CONTAINER (LEFT COLUMN: 8 COLS) --}}
        <div class="col-lg-8">
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white" style="border-top: 4px solid #4F46E5 !important;">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: #EEF2FF; color: #4F46E5; display: grid; place-items: center; font-size: 24px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                            <i class="bi bi-calendar2-week-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0 fs-5">Issue Examination Roll Slips</h4>
                            <p class="text-muted small mb-0">Choose class batch to automatically load only its distinct curriculum subjects.</p>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        Datesheet Builder
                    </span>
                </div>

                <form action="{{ route('roll-slips.store') }}" method="POST" id="issueRollSlipsForm">
                    @csrf
                    <div class="row g-4">
                        {{-- Field 1: Class Selection --}}
                        <div class="col-12">
                            <label for="class-select" class="form-label fw-bold text-dark small mb-1">
                                Target Class Batch <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-mortarboard-fill"></i></span>
                                <select id="class-select" name="class_name" class="form-select border-start-0 fw-bold text-dark" style="height: 46px;" required>
                                    <option value="" disabled {{ $selectedClass ? '' : 'selected' }}>Select a class to generate datesheet...</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->class_name }}" @selected(strtolower($selectedClass) === strtolower($cls->class_name))>
                                            Class {{ $cls->class_name }}
                                        </option>
                                    @endforeach
                                    <option value="all" @selected(strtolower($selectedClass) === 'all')>All Classes (Combined Datesheet)</option>
                                </select>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Selecting a class automatically loads only that class's unique registered subjects.</small>
                        </div>

                        {{-- Field 2: Exam Name --}}
                        <div class="col-md-7">
                            <label for="exam-name" class="form-label fw-bold text-dark small mb-1">
                                Examination Title <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-file-earmark-ruled-fill"></i></span>
                                <input id="exam-name" type="text" name="exam_name" value="{{ old('exam_name') }}" placeholder="e.g. Mid-Term Examination, Annual Finals 2026" class="form-control border-start-0 @error('exam_name') is-invalid @enderror" style="height: 46px;" required>
                            </div>
                            @error('exam_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Standard examination title printed on student roll slips.</small>
                        </div>

                        {{-- Field 3: Session --}}
                        <div class="col-md-5">
                            <label for="exam-session" class="form-label fw-bold text-dark small mb-1">
                                Academic Session
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-success"><i class="bi bi-calendar-event"></i></span>
                                <input id="exam-session" type="text" name="exam_session" value="{{ old('exam_session', now()->format('Y')) }}" placeholder="e.g. {{ now()->format('Y') }}" class="form-control border-start-0 @error('exam_session') is-invalid @enderror" style="height: 46px;">
                            </div>
                            @error('exam_session')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Academic year or term designation.</small>
                        </div>

                        {{-- DATESHEET SCHEDULE TABLE --}}
                        <div class="col-12 mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-check-fill text-primary"></i> Curriculum Datesheet Schedules
                                </h6>
                                <span class="badge bg-secondary-subtle text-secondary fw-bold rounded-pill px-2 py-1" id="subject-count-pill" style="font-size: 11.5px;">
                                    {{ count($subjects) }} Subjects Total
                                </span>
                            </div>

                            <div class="table-responsive rounded-3 border">
                                <table class="table table-hover align-middle mb-0" id="datesheet-table" style="font-size: 13.5px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%; padding: 12px 16px;">Subject Name</th>
                                            <th style="width: 50%; padding: 12px 16px;">Examination Paper Date <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="datesheet-tbody">
                                        @forelse($subjects as $index => $subject)
                                            <tr class="subject-schedule-row" data-index="{{ $index }}">
                                                <td style="padding: 12px 16px;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #EEF2FF; color: #4F46E5; display: grid; place-items: center; font-size: 14px;">
                                                            <i class="bi bi-book"></i>
                                                        </div>
                                                        <strong class="text-dark">{{ $subject->subject_name }}</strong>
                                                    </div>
                                                    <input type="hidden" name="schedules[{{ $index }}][subject_id]" value="{{ $subject->id }}">
                                                </td>
                                                <td style="padding: 12px 16px;">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                                                        <input type="date" name="schedules[{{ $index }}][exam_date]" value="{{ old("schedules.$index.exam_date") }}" class="form-control border-start-0" style="height: 40px;" required>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="empty-subjects-row">
                                                <td colspan="2" class="text-center text-muted py-4">
                                                    <i class="bi bi-book fs-3 d-block mb-1 text-muted"></i>
                                                    No subjects found for the selected class.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @error('schedules')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror

                            {{-- EXACT ADMIN PANEL PAGINATION (10 RESULTS PER PAGE) --}}
                            <div class="admin-pagination-bar mt-3" id="datesheet-pagination" style="display: none;">
                                <div class="admin-pagination-info" id="datesheet-pagination-info">
                                    Showing <strong id="pag-start">1</strong> to <strong id="pag-end">10</strong> of <strong id="pag-total">0</strong> subjects
                                </div>
                                <div class="admin-pagination-links">
                                    <ul class="pagination mb-0" id="datesheet-pagination-ul">
                                        {{-- Populated dynamically via JS --}}
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-12 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                            <button type="submit" class="admin-btn admin-btn-primary px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;" @disabled($arrangementCount === 0 || $availableArrangementCount === 0 || $subjects->isEmpty())>
                                <i class="bi bi-check-circle-fill me-1"></i> Issue &amp; Store Roll Slips
                            </button>
                            <a href="{{ route('roll-slips.index') }}" class="admin-btn admin-btn-outline px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT SIDEBAR WIDGETS (RIGHT COLUMN: 4 COLS) --}}
        <div class="col-lg-4">
            {{-- Widget 1: Seating & Desk Arrangements Status --}}
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" style="border-top: 4px solid #10B981 !important;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #10B981; display: grid; place-items: center; font-size: 18px;">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Arrangements &amp; Seating</h6>
                        <small class="text-muted">Live Hall Allocation Status</small>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 mb-3" style="font-size: 13px;">
                    <div class="p-3 rounded bg-light border d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-semibold">Total Hall Desks Arranged</span>
                        <span class="badge bg-primary text-white fw-bold fs-6">{{ $arrangementCount }}</span>
                    </div>
                    <div class="p-3 rounded bg-light border d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-semibold">Available for Roll Slips</span>
                        <span class="badge bg-success text-white fw-bold fs-6">{{ $availableArrangementCount }}</span>
                    </div>
                </div>

                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    Each student desk mapped in the seating plan receives a unique roll number slip combining room number, chair allocation, and this datesheet.
                </p>
            </div>

            {{-- Widget 2: Automatic System Reflection --}}
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 bg-white" style="border-top: 4px solid #06B6D4 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-database-check text-info me-1"></i> Class-Specific Datesheets
                    </h6>
                    <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                        No Duplicates
                    </span>
                </div>
                <p class="text-muted small mb-3">Selecting a class isolates that grade's syllabus and prevents repeated subjects:</p>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Subjects filtered strictly for the chosen class</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Zero duplicate names across streams</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>10 results per page standard pagination</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- CLIENT-SIDE DYNAMIC CLASS SWITCHING & 10-PER-PAGE PAGINATION --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('class-select');
    const tableBody = document.getElementById('datesheet-tbody');
    const countPill = document.getElementById('subject-count-pill');
    const paginationBar = document.getElementById('datesheet-pagination');
    const paginationUl = document.getElementById('datesheet-pagination-ul');
    const pagStart = document.getElementById('pag-start');
    const pagEnd = document.getElementById('pag-end');
    const pagTotal = document.getElementById('pag-total');

    // Preloaded unique subjects per class from server
    const allClassSubjects = @json($allClassSubjects);

    const pageSize = 10;
    let currentPage = 1;

    function renderSubjectsForClass(selectedClass) {
        if (!selectedClass) return;

        let subjectList = [];

        if (selectedClass.toLowerCase() === 'all') {
            // Aggregate all subjects, strictly unique by subject_name
            const seen = new Set();
            for (const cls in allClassSubjects) {
                allClassSubjects[cls].forEach(sub => {
                    const normalized = sub.subject_name.trim().toLowerCase();
                    if (!seen.has(normalized)) {
                        seen.add(normalized);
                        subjectList.push(sub);
                    }
                });
            }
            subjectList.sort((a, b) => a.subject_name.localeCompare(b.subject_name));
        } else {
            const normalizedCls = selectedClass.trim().toLowerCase();
            subjectList = allClassSubjects[normalizedCls] || [];
        }

        countPill.textContent = subjectList.length + ' Subjects for ' + (selectedClass === 'all' ? 'All Classes' : selectedClass);

        if (subjectList.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="2" class="text-center text-muted py-4"><i class="bi bi-book fs-3 d-block mb-1 text-muted"></i>No subjects registered for ${selectedClass}.</td></tr>`;
            paginationBar.style.display = 'none';
            return;
        }

        // Build HTML table rows
        tableBody.innerHTML = subjectList.map((subject, idx) => `
            <tr class="subject-schedule-row" data-index="${idx}">
                <td style="padding: 12px 16px;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #EEF2FF; color: #4F46E5; display: grid; place-items: center; font-size: 14px;">
                            <i class="bi bi-book"></i>
                        </div>
                        <strong class="text-dark">${subject.subject_name}</strong>
                    </div>
                    <input type="hidden" name="schedules[${idx}][subject_id]" value="${subject.id}">
                </td>
                <td style="padding: 12px 16px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="schedules[${idx}][exam_date]" class="form-control border-start-0" style="height: 40px;" required>
                    </div>
                </td>
            </tr>
        `).join('');

        currentPage = 1;
        applyPagination(subjectList.length);
    }

    function applyPagination(totalItems) {
        const rows = tableBody.querySelectorAll('.subject-schedule-row');
        if (rows.length === 0) {
            paginationBar.style.display = 'none';
            return;
        }

        const total = rows.length;
        const totalPages = Math.ceil(total / pageSize);

        if (totalPages <= 1) {
            paginationBar.style.display = 'none';
            rows.forEach(r => r.style.display = '');
            return;
        }

        paginationBar.style.display = 'flex';
        const start = (currentPage - 1) * pageSize;
        const end = Math.min(start + pageSize, total);

        pagStart.textContent = start + 1;
        pagEnd.textContent = end;
        pagTotal.textContent = total;

        rows.forEach((row, idx) => {
            if (idx >= start && idx < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Build pagination links
        let linksHtml = '';

        // Previous button
        if (currentPage === 1) {
            linksHtml += '<li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>';
        } else {
            linksHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></a></li>`;
        }

        // Page numbers
        for (let p = 1; p <= totalPages; p++) {
            if (p === currentPage) {
                linksHtml += `<li class="page-item active"><span class="page-link">${p}</span></li>`;
            } else if (p === 1 || p === totalPages || Math.abs(p - currentPage) <= 2) {
                linksHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
            } else if (Math.abs(p - currentPage) === 3) {
                linksHtml += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
            }
        }

        // Next button
        if (currentPage === totalPages) {
            linksHtml += '<li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>';
        } else {
            linksHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></a></li>`;
        }

        paginationUl.innerHTML = linksHtml;

        // Attach click handlers to pagination buttons
        paginationUl.querySelectorAll('a[data-page]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                currentPage = parseInt(this.getAttribute('data-page'));
                applyPagination(total);
            });
        });
    }

    // On class change, dynamically re-render subjects without page reload
    classSelect.addEventListener('change', function () {
        renderSubjectsForClass(this.value);
    });

    // Initial run on page load
    const initialRows = tableBody.querySelectorAll('.subject-schedule-row');
    if (initialRows.length > 0) {
        applyPagination(initialRows.length);
    }
});
</script>
@endsection