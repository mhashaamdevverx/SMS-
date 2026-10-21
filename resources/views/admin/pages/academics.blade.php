@php
    $classCount = $totalClassesCount ?? ($classes instanceof \Illuminate\Pagination\LengthAwarePaginator ? $classes->total() : count($classes ?? []));
    $sectionCount = $totalSectionsCount ?? ($sections instanceof \Illuminate\Pagination\LengthAwarePaginator ? $sections->total() : count($sections ?? []));
    $subjectCount = $totalSubjectsCount ?? ($subjects instanceof \Illuminate\Pagination\LengthAwarePaginator ? $subjects->total() : count($subjects ?? []));
    $totalChairs = \App\Models\Classes::sum('chairs');
    $resultsCount = (int) ($totalResults ?? 0);
    $slipsCount = (int) ($totalRollSlips ?? 0);
    $arrangementsCount = (int) ($totalArrangements ?? 0);
    $activeTab = request('subjects_page') ? 'subjects' : (request('sections_page') ? 'sections' : 'classes');
@endphp

<style>
    /* Academic Modules Action Buttons */
    .acad-btn-main {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none !important;
        white-space: nowrap;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        color: #FFFFFF !important;
    }
    .acad-btn-main:hover {
        transform: translateY(-2px);
        color: #FFFFFF !important;
    }

    .acad-btn-quick {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .acad-btn-quick:hover {
        transform: translateY(-2px);
    }

    /* 1. Classes - Indigo */
    .acad-btn-indigo {
        background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.28);
    }
    .acad-btn-indigo:hover {
        background: linear-gradient(135deg, #4338CA 0%, #312E81 100%);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }
    .acad-quick-indigo {
        background: #EEF2FF;
        color: #4F46E5 !important;
        border: 1.5px solid #C7D2FE;
    }
    .acad-quick-indigo:hover {
        background: #4F46E5;
        color: #FFFFFF !important;
        border-color: #4F46E5;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    /* 2. Sections - Emerald */
    .acad-btn-emerald {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.28);
    }
    .acad-btn-emerald:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
    .acad-quick-emerald {
        background: #ECFDF5;
        color: #059669 !important;
        border: 1.5px solid #A7F3D0;
    }
    .acad-quick-emerald:hover {
        background: #10B981;
        color: #FFFFFF !important;
        border-color: #10B981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    /* 3. Subjects - Cyan */
    .acad-btn-cyan {
        background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%);
        box-shadow: 0 4px 14px rgba(6, 182, 212, 0.28);
    }
    .acad-btn-cyan:hover {
        background: linear-gradient(135deg, #0891B2 0%, #0E7490 100%);
        box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
    }
    .acad-quick-cyan {
        background: #ECFEFF;
        color: #0891B2 !important;
        border: 1.5px solid #A5F3FC;
    }
    .acad-quick-cyan:hover {
        background: #06B6D4;
        color: #FFFFFF !important;
        border-color: #06B6D4;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
    }

    /* 4. Results - Amber */
    .acad-btn-amber {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.28);
    }
    .acad-btn-amber:hover {
        background: linear-gradient(135deg, #D97706 0%, #B45309 100%);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
    }
    .acad-quick-amber {
        background: #FFFBEB;
        color: #D97706 !important;
        border: 1.5px solid #FDE68A;
    }
    .acad-quick-amber:hover {
        background: #F59E0B;
        color: #FFFFFF !important;
        border-color: #F59E0B;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    /* 5. Roll Slips - Rose */
    .acad-btn-rose {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.28);
    }
    .acad-btn-rose:hover {
        background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }
    .acad-quick-rose {
        background: #FEF2F2;
        color: #DC2626 !important;
        border: 1.5px solid #FECACA;
    }
    .acad-quick-rose:hover {
        background: #EF4444;
        color: #FFFFFF !important;
        border-color: #EF4444;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* 6. Seating - Purple */
    .acad-btn-purple {
        background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        box-shadow: 0 4px 14px rgba(139, 92, 246, 0.28);
    }
    .acad-btn-purple:hover {
        background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    }
    .acad-quick-purple {
        background: #F5F3FF;
        color: #7C3AED !important;
        border: 1.5px solid #DDD6FE;
    }
    .acad-quick-purple:hover {
        background: #8B5CF6;
        color: #FFFFFF !important;
        border-color: #8B5CF6;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
</style>

<div class="academic-operations-hub">
    {{-- TOP COMMAND BAR & HEADER --}}
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
                    <i class="bi bi-journal-bookmark-fill" style="color: #4F46E5;"></i> Academic Operations & Curriculum Center
                </h3>
                <p class="text-muted small mb-0">Unified administration hub for classes, sections, curricula, exams, roll number slips, and seating allocations.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/add-class" class="admin-btn admin-btn-primary admin-btn-sm fw-bold">
                    <i class="bi bi-plus-circle me-1"></i> Add Class
                </a>
                <a href="/add-section" class="admin-btn admin-btn-success admin-btn-sm fw-bold">
                    <i class="bi bi-grid-plus me-1"></i> Add Section
                </a>
                <a href="/add-subject" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-book me-1"></i> Add Subject
                </a>
                <a href="/issue-roll-slips" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-file-earmark-plus me-1"></i> Issue Slips
                </a>
            </div>
        </div>

        {{-- EXECUTIVE 4-METRIC TOP STATS GRID --}}
        <div class="p-4 bg-white rounded-bottom-4">
            <div class="row g-3">
                {{-- Metric 1: Classes --}}
                <div class="col-xl-3 col-md-6">
                    <div class="p-3 rounded-4 h-100" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.05), rgba(99, 102, 241, 0.12)); border: 1px solid rgba(79, 70, 229, 0.2);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #4F46E5; color: #fff; display: grid; place-items: center; font-size: 20px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                                <i class="bi bi-easel2-fill"></i>
                            </div>
                            <span class="badge bg-primary text-light fw-bold rounded-pill" style="font-size: 11px;">
                                {{ $totalChairs }} Chairs
                            </span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0 fs-3">{{ $classCount }}</h3>
                        <div class="fw-semibold text-muted small">Configured Classes</div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-primary-subtle text-muted" style="font-size: 11.5px;">
                            <span>Active grade levels</span>
                            <span class="text-primary fw-bold">Pre-KG &bull; 10th</span>
                        </div>
                    </div>
                </div>

                {{-- Metric 2: Sections --}}
                <div class="col-xl-3 col-md-6">
                    <div class="p-3 rounded-4 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.12)); border: 1px solid rgba(16, 185, 129, 0.2);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #10B981; color: #fff; display: grid; place-items: center; font-size: 20px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                <i class="bi bi-grid-3x3-gap-fill"></i>
                            </div>
                            <span class="badge bg-success text-light fw-bold rounded-pill" style="font-size: 11px;">
                                Operational
                            </span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0 fs-3">{{ $sectionCount }}</h3>
                        <div class="fw-semibold text-muted small">Active Sections</div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-success-subtle text-muted" style="font-size: 11.5px;">
                            <span>Streams configured</span>
                            <span class="text-success fw-bold">{{ $sections ? $sections->pluck('section_name')->take(3)->implode(', ') : 'All' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Metric 3: Subjects --}}
                <div class="col-xl-3 col-md-6">
                    <div class="p-3 rounded-4 h-100" style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.05), rgba(14, 165, 233, 0.12)); border: 1px solid rgba(6, 182, 212, 0.2);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #06B6D4; color: #fff; display: grid; place-items: center; font-size: 20px; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);">
                                <i class="bi bi-book-fill"></i>
                            </div>
                            <span class="badge bg-info text-white fw-bold rounded-pill" style="font-size: 11px;">
                                Syllabus Ready
                            </span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0 fs-3">{{ $subjectCount }}</h3>
                        <div class="fw-semibold text-muted small">Course Syllabi</div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-info-subtle text-muted" style="font-size: 11.5px;">
                            <span>Subject breakdown</span>
                            <span class="text-info fw-bold">Full Curriculum</span>
                        </div>
                    </div>
                </div>

                {{-- Metric 4: Exam Results & Slips --}}
                <div class="col-xl-3 col-md-6">
                    <div class="p-3 rounded-4 h-100" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(217, 119, 6, 0.12)); border: 1px solid rgba(245, 158, 11, 0.2);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #F59E0B; color: #fff; display: grid; place-items: center; font-size: 20px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                                <i class="bi bi-clipboard2-data-fill"></i>
                            </div>
                            <span class="badge bg-warning text-dark fw-bold rounded-pill" style="font-size: 11px;">
                                {{ $slipsCount }} Slips
                            </span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0 fs-3">{{ number_format($resultsCount) }}</h3>
                        <div class="fw-semibold text-muted small">Exam Marks Logged</div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-warning-subtle text-muted" style="font-size: 11.5px;">
                            <span>Hall arrangements</span>
                            <span class="text-warning-emphasis fw-bold">{{ $arrangementsCount }} Seating plans</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 6 CORE ACADEMIC MODULES GRID (HIGH-END AESTHETIC CARDS) --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Classes & Enrolment --}}
        <div class="col-lg-4 col-md-6">
            <div class="admin-card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden" style="background: #ffffff; border-top: 4px solid #4F46E5 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #EEF2FF; color: #4F46E5; display: grid; place-items: center; font-size: 24px;">
                        <i class="bi bi-easel2-fill"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        {{ $classCount }} Classes
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Classes & Enrolment</h5>
                <p class="text-muted small mb-3">Configure standard class batches, chair capacities, and grade group structures.</p>
                
                <div class="d-flex flex-wrap gap-1 mb-4">
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Capacity Matrix</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Chair Allocation</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Grade Hierarchy</span>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="/classes" class="acad-btn-main acad-btn-indigo w-100">
                        <span>Manage Classes</span> <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="/add-class" class="acad-btn-quick acad-quick-indigo" title="Quick Add Class">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 2: Sections Management --}}
        <div class="col-lg-4 col-md-6">
            <div class="admin-card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden" style="background: #ffffff; border-top: 4px solid #10B981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #ECFDF5; color: #10B981; display: grid; place-items: center; font-size: 24px;">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        {{ $sectionCount }} Sections
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Sections Management</h5>
                <p class="text-muted small mb-3">Add, edit, and categorize section streams (Blue, Green, Red, Pink) per class.</p>
                
                <div class="d-flex flex-wrap gap-1 mb-4">
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Stream Assignment</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Room Partition</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Rosters</span>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="/sections" class="acad-btn-main acad-btn-emerald w-100">
                        <span>Manage Sections</span> <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="/add-section" class="acad-btn-quick acad-quick-emerald" title="Quick Add Section">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3: Subjects & Syllabus --}}
        <div class="col-lg-4 col-md-6">
            <div class="admin-card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden" style="background: #ffffff; border-top: 4px solid #06B6D4 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #ECFEFF; color: #06B6D4; display: grid; place-items: center; font-size: 24px;">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <span class="badge bg-info-subtle text-info fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        {{ $subjectCount }} Subjects
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Subjects & Syllabus</h5>
                <p class="text-muted small mb-3">Configure course curriculum, total marks criteria, and pass benchmark policies.</p>
                
                <div class="d-flex flex-wrap gap-1 mb-4">
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Curriculum Map</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Max Marks</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Specializations</span>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="/subjects" class="acad-btn-main acad-btn-cyan w-100">
                        <span>Manage Subjects</span> <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="/add-subject" class="acad-btn-quick acad-quick-cyan" title="Quick Add Subject">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 4: Exam Results Management --}}
        <div class="col-lg-4 col-md-6">
            <div class="admin-card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden" style="background: #ffffff; border-top: 4px solid #F59E0B !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #FFFBEB; color: #F59E0B; display: grid; place-items: center; font-size: 24px;">
                        <i class="bi bi-clipboard2-data-fill"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        {{ number_format($resultsCount) }} Records
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Exam Results Management</h5>
                <p class="text-muted small mb-3">Record and edit student term examination marks, grade scales, and transcripts.</p>
                
                <div class="d-flex flex-wrap gap-1 mb-4">
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Bulk Entry</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Report Cards</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Grade Analytics</span>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="/results" class="acad-btn-main acad-btn-amber w-100">
                        <span>Manage Results</span> <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="/add-result" class="acad-btn-quick acad-quick-amber" title="Add Single/Class Result">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 5: Roll Number Slips --}}
        <div class="col-lg-4 col-md-6">
            <div class="admin-card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden" style="background: #ffffff; border-top: 4px solid #EF4444 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #FEF2F2; color: #EF4444; display: grid; place-items: center; font-size: 24px;">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <span class="badge bg-danger-subtle text-danger fw-bold px-3 py-1 rounded-pill" style="font-size: 12px;">
                        {{ $slipsCount }} Issued
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Roll Number Slips</h5>
                <p class="text-muted small mb-3">Generate verified examination roll slips, datesheets, and student admit cards.</p>
                
                <div class="d-flex flex-wrap gap-1 mb-4">
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Admit Cards</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">PDF Printing</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Center Schedules</span>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="/roll-slips" class="acad-btn-main acad-btn-rose w-100">
                        <span>Manage Roll Slips</span> <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="/issue-roll-slips" class="acad-btn-quick acad-quick-rose" title="Issue Roll Slips">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 6: Seating Arrangements --}}
        <div class="col-lg-4 col-md-6">
            <div class="admin-card h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden" style="background: #ffffff; border-top: 4px solid #8B5CF6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #F5F3FF; color: #8B5CF6; display: grid; place-items: center; font-size: 24px;">
                        <i class="bi bi-columns-gap"></i>
                    </div>
                    <span class="badge bg-purple-subtle text-purple fw-bold px-3 py-1 rounded-pill" style="font-size: 12px; background: #ede9fe; color: #7c3aed;">
                        {{ $arrangementsCount }} Plans
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Seating Arrangements</h5>
                <p class="text-muted small mb-3">Generate hall seating distributions, room allocations, and exam desk label tags.</p>
                
                <div class="d-flex flex-wrap gap-1 mb-4">
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Room Allotment</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Desk Spacing</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px;">Invigilation Plans</span>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="/arrangements" class="acad-btn-main acad-btn-purple w-100">
                        <span>Manage Seating</span> <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="/add-arrangement" class="acad-btn-quick acad-quick-purple" title="Add Seating Plan">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- LOWER SECTION: QUICK INTERACTIVE DATA OVERVIEW (TABLES & DIRECT ROSTER) --}}
    <div class="admin-card border-0 shadow-sm rounded-4">
        <div class="admin-card-header d-flex flex-wrap align-items-center justify-content-between p-3 border-bottom">
            <ul class="nav nav-pills gap-2" id="academicTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'classes' ? 'active' : '' }} fw-bold px-3 py-2 rounded-3" id="classes-tab" data-bs-toggle="pill" data-bs-target="#classes-panel" type="button" role="tab" style="font-size: 13px;">
                        <i class="bi bi-easel2 me-1"></i> Class Roster & Capacity ({{ $classCount }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'sections' ? 'active' : '' }} fw-bold px-3 py-2 rounded-3" id="sections-tab" data-bs-toggle="pill" data-bs-target="#sections-panel" type="button" role="tab" style="font-size: 13px;">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Sections ({{ $sectionCount }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'subjects' ? 'active' : '' }} fw-bold px-3 py-2 rounded-3" id="subjects-tab" data-bs-toggle="pill" data-bs-target="#subjects-panel" type="button" role="tab" style="font-size: 13px;">
                        <i class="bi bi-book me-1"></i> Curriculum Syllabi ({{ $subjectCount }})
                    </button>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-muted border px-2 py-1 small">
                    <i class="bi bi-info-circle me-1"></i> Live Database Records
                </span>
            </div>
        </div>

        <div class="tab-content p-4" id="academicTabsContent">
            {{-- TAB 1: CLASSES ROSTER --}}
            <div class="tab-pane fade {{ $activeTab === 'classes' ? 'show active' : '' }}" id="classes-panel" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-table text-primary me-1"></i> Active Classes & Seating Quotas</h6>
                    <a href="/classes" class="text-primary text-decoration-none small fw-bold">
                        Open Full Classes Module <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="admin-table-wrapper border rounded-3 overflow-hidden">
                    <table class="admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Class No</th>
                                <th>Class Name</th>
                                <th>Default Section</th>
                                <th>Desk / Chair Capacity</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes ?? [] as $cls)
                                <tr>
                                    <td><span class="badge bg-dark-subtle text-dark fw-bold">#{{ $cls->class_no ?: $cls->id }}</span></td>
                                    <td><strong class="text-dark fs-6">{{ $cls->class_name }}</strong></td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-dark border">
                                            {{ $cls->section_name ?: 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-chair text-muted"></i>
                                            <strong class="text-dark">{{ $cls->chairs ?: '30' }} Desks</strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success-subtle text-success fw-bold">Active Class</span></td>
                                    <td class="text-end">
                                        <a href="/classes" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 12px;">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No classes configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($classes instanceof \Illuminate\Pagination\LengthAwarePaginator && $classes->total() > 0)
                    <div class="admin-pagination-bar mt-3">
                        <div class="admin-pagination-info">
                            Showing <strong>{{ $classes->firstItem() }}</strong> to <strong>{{ $classes->lastItem() }}</strong> of <strong>{{ $classes->total() }}</strong> classes
                        </div>
                        <div class="admin-pagination-links">
                            <ul class="pagination mb-0">
                                @if ($classes->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $classes->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach ($classes->getUrlRange(1, $classes->lastPage()) as $page => $url)
                                    @if ($page == $classes->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif ($page == 1 || $page == $classes->lastPage() || abs($page - $classes->currentPage()) <= 2)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @elseif (abs($page - $classes->currentPage()) == 3)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if ($classes->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $classes->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            {{-- TAB 2: SECTIONS ROSTER --}}
            <div class="tab-pane fade {{ $activeTab === 'sections' ? 'show active' : '' }}" id="sections-panel" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-grid text-success me-1"></i> Section Streams</h6>
                    <a href="/sections" class="text-success text-decoration-none small fw-bold">
                        Open Full Sections Module <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="row g-3">
                    @forelse($sections ?? [] as $sec)
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 rounded-3 border bg-light text-center h-100">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #DCFCE7; color: #15803D; display: grid; place-items: center; margin: 0 auto 10px; font-weight: 800;">
                                    {{ substr($sec->section_name, 0, 1) }}
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Section {{ $sec->section_name }}</h6>
                                <span class="badge bg-success-subtle text-success fw-bold mb-2">Standard Stream</span>
                                <small class="text-muted d-block">Configured for classes</small>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-4">No sections found.</div>
                    @endforelse
                </div>

                @if($sections instanceof \Illuminate\Pagination\LengthAwarePaginator && $sections->total() > 10)
                    <div class="admin-pagination-bar mt-3">
                        <div class="admin-pagination-info">
                            Showing <strong>{{ $sections->firstItem() }}</strong> to <strong>{{ $sections->lastItem() }}</strong> of <strong>{{ $sections->total() }}</strong> sections
                        </div>
                        <div class="admin-pagination-links">
                            <ul class="pagination mb-0">
                                @if ($sections->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $sections->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach ($sections->getUrlRange(1, $sections->lastPage()) as $page => $url)
                                    @if ($page == $sections->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif ($page == 1 || $page == $sections->lastPage() || abs($page - $sections->currentPage()) <= 2)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @elseif (abs($page - $sections->currentPage()) == 3)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if ($sections->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $sections->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            {{-- TAB 3: SUBJECTS ROSTER --}}
            <div class="tab-pane fade {{ $activeTab === 'subjects' ? 'show active' : '' }}" id="subjects-panel" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-book text-info me-1"></i> Core Subjects Matrix</h6>
                    <a href="/subjects" class="text-info text-decoration-none small fw-bold">
                        Open Full Subjects Module <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="admin-table-wrapper border rounded-3 overflow-hidden">
                    <table class="admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Subject Title</th>
                                <th>Target Class</th>
                                <th>Target Section</th>
                                <th>Total Max Marks</th>
                                <th>Passing Benchmark</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjects ?? [] as $sb)
                                <tr>
                                    <td><strong class="text-dark">{{ $sb->subject_name }}</strong></td>
                                    <td><span class="badge bg-primary-subtle text-primary">{{ $sb->class_name ?: 'All Classes' }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $sb->section_name ?: 'All Sections' }}</span></td>
                                    <td><strong>{{ $sb->total_marks ?: 100 }}</strong> Marks</td>
                                    <td><span class="badge bg-success-subtle text-success">33% Passing</span></td>
                                    <td class="text-end">
                                        <a href="/subjects" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 12px;">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No subjects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subjects instanceof \Illuminate\Pagination\LengthAwarePaginator && $subjects->total() > 0)
                    <div class="admin-pagination-bar mt-3">
                        <div class="admin-pagination-info">
                            Showing <strong>{{ $subjects->firstItem() }}</strong> to <strong>{{ $subjects->lastItem() }}</strong> of <strong>{{ $subjects->total() }}</strong> subjects
                        </div>
                        <div class="admin-pagination-links">
                            <ul class="pagination mb-0">
                                @if ($subjects->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $subjects->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach ($subjects->getUrlRange(1, $subjects->lastPage()) as $page => $url)
                                    @if ($page == $subjects->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif ($page == 1 || $page == $subjects->lastPage() || abs($page - $subjects->currentPage()) <= 2)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @elseif (abs($page - $subjects->currentPage()) == 3)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if ($subjects->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $subjects->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
