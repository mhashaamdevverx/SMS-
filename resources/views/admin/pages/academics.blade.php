<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-journal-bookmark-fill text-coral"></i> Academics & Operations Modules
        </h3>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <i class="bi bi-easel2-fill fs-1 text-primary"></i>
                    <span class="badge bg-primary rounded-pill fs-6">{{ count($classes) }} Classes</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Classes & Enrolment</h5>
                <p class="text-muted small mb-3">Manage class groups, chair capacities, and student allocations.</p>
                <a href="/classes" class="admin-btn admin-btn-outline w-100 justify-content-center">Manage Classes</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <i class="bi bi-grid-3x3-gap-fill fs-1 text-success"></i>
                    <span class="badge bg-success rounded-pill fs-6">{{ count($sections) }} Sections</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Sections Management</h5>
                <p class="text-muted small mb-3">Add, edit, or reconfigure class sections and streams.</p>
                <a href="/sections" class="admin-btn admin-btn-outline w-100 justify-content-center">Manage Sections</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <i class="bi bi-book-fill fs-1 text-info"></i>
                    <span class="badge bg-info text-light rounded-pill fs-6">{{ count($subjects) }} Subjects</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Subjects & Syllabus</h5>
                <p class="text-muted small mb-3">Configure subject list, total marks, and passing criteria.</p>
                <a href="/subjects" class="admin-btn admin-btn-outline w-100 justify-content-center">Manage Subjects</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <i class="bi bi-clipboard2-data-fill fs-1 text-warning"></i>
                    <span class="badge bg-warning text-dark rounded-pill fs-6">{{ $totalResults }} Marks</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Exam Results Management</h5>
                <p class="text-muted small mb-3">Add exam marks, edit student grades, and publish report cards.</p>
                <a href="/results" class="admin-btn admin-btn-outline w-100 justify-content-center">Manage Results</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <i class="bi bi-file-earmark-text-fill fs-1 text-danger"></i>
                    <span class="badge bg-danger rounded-pill fs-6">{{ $totalRollSlips }} Issued</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Roll Number Slips</h5>
                <p class="text-muted small mb-3">Issue examination roll slips and print class documents.</p>
                <a href="/roll-slips" class="admin-btn admin-btn-outline w-100 justify-content-center">Manage Roll Slips</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <i class="bi bi-columns-gap fs-1 text-purple" style="color: #8b5cf6;"></i>
                    <span class="badge bg-purple text-light rounded-pill fs-6" style="background: #8b5cf6;">{{ $totalArrangements }} Plans</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Seating Arrangements</h5>
                <p class="text-muted small mb-3">Generate examination hall seating arrangements and room allocation.</p>
                <a href="/arrangements" class="admin-btn admin-btn-outline w-100 justify-content-center">Manage Seating</a>
            </div>
        </div>
    </div>
</div>
