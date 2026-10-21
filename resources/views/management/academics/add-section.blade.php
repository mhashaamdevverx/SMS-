@extends('admin.layout', ['currentPage' => 'academics', 'pageTitle' => 'Add Section'])

@section('content')
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
                    <i class="bi bi-grid-plus-fill" style="color: #10B981;"></i> Sections Management
                </h3>
                <p class="text-muted small mb-0">Create and categorize section streams (Blue, Green, Red, Pink) per academic class.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/admin/academics" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Academics
                </a>
                <a href="/sections" class="admin-btn admin-btn-success admin-btn-sm fw-bold">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> View All Sections
                </a>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white" style="border-top: 4px solid #10B981 !important;">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: #ECFDF5; color: #10B981; display: grid; place-items: center; font-size: 24px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0 fs-5">Add New Section Stream</h4>
                        <p class="text-muted small mb-0">Define section name for room partition and student allocation.</p>
                    </div>
                </div>

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #FEF2F2; border-left: 4px solid #EF4444 !important;">
                        <ul class="mb-0 small text-danger ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/add-section" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="section-name" class="form-label fw-bold text-dark small mb-1">
                            Section Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-success"><i class="bi bi-tag-fill"></i></span>
                            <input id="section-name" type="text" name="section_name" placeholder="e.g. Blue, Green, Red, Pink, A, B" value="{{ old('section_name') }}" class="form-control border-start-0 @error('section_name') is-invalid @enderror" style="height: 44px;" required autofocus>
                        </div>
                        @error('section_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Standard section stream name (alphabetic or color name).</small>
                    </div>

                    <div class="pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                        <button type="submit" class="admin-btn admin-btn-success px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
                            <i class="bi bi-check-circle-fill me-1"></i> Save Section Stream
                        </button>
                        <a href="/admin/academics" class="admin-btn admin-btn-outline px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 bg-white" style="border-top: 4px solid #4F46E5 !important;">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-2"></i> Section Stream Purpose</h6>
                <p class="text-muted small mb-3" style="line-height: 1.6;">
                    Section streams organize students into manageable room divisions per class batch. Once created, sections are available for:
                </p>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2 text-primary fw-bold"></i>
                        <span>Class batch configuration & desk mapping</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2 text-primary fw-bold"></i>
                        <span>Student enrollment & roll number sequence</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2 text-primary fw-bold"></i>
                        <span>Teacher assignment per section roster</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection