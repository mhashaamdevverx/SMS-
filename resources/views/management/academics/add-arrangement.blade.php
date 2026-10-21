@extends('admin.layout', ['currentPage' => 'academics', 'pageTitle' => 'Generate Arrangement'])

@section('content')
@php
    $oldClassIds = collect(old('class_ids', []))->map(fn ($value) => (int) $value)->all();
    $arrangementClassData = $classOptions->map(fn ($class) => [
        'id' => (int) $class->id,
        'class_no' => $class->class_no,
        'class_name' => $class->class_name,
        'section_name' => $class->section_name,
        'chairs' => (int) $class->chairs,
    ])->values();
@endphp

<style>
    /* Arrangement Dropdown & Multiselect Custom Styles */
    .arrangement-selector-wrapper {
        position: relative;
    }
    .arrangement-trigger-btn {
        width: 100%;
        min-height: 48px;
        padding: 10px 16px;
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        font-weight: 600;
        color: #1E293B;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    .arrangement-trigger-btn:hover,
    .arrangement-trigger-btn:focus {
        border-color: #8B5CF6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        outline: none;
    }
    .arrangement-dropdown-card {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        padding: 12px;
        display: none;
    }
    .arrangement-dropdown-card.show {
        display: block;
        animation: fadeInDown 0.18s ease-out;
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .arrangement-search-box {
        position: relative;
        margin-bottom: 10px;
    }
    .arrangement-search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 14px;
    }
    .arrangement-search-box input {
        width: 100%;
        height: 38px;
        padding-left: 36px;
        padding-right: 12px;
        font-size: 13px;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.2s;
    }
    .arrangement-search-box input:focus {
        border-color: #8B5CF6;
    }
    .arrangement-options-container {
        max-height: 240px;
        overflow-y: auto;
        display: flex;
        flex-column;
        gap: 6px;
        padding-right: 4px;
    }
    .arrangement-option-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.15s ease;
        margin-bottom: 4px;
        border: 1px solid transparent;
    }
    .arrangement-option-item:hover {
        background: #F8FAFC;
        border-color: #E2E8F0;
    }
    .arrangement-option-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        cursor: pointer;
        accent-color: #8B5CF6;
    }
    .preview-info-card {
        background: #F5F3FF;
        border: 1.5px solid #DDD6FE;
        border-radius: 12px;
        padding: 14px 18px;
    }
</style>

<div class="academic-operations-hub" data-arrangement-page data-arrangement-records="{{ json_encode($arrangementClassData) }}">
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
                    <i class="bi bi-diagram-3-fill" style="color: #8B5CF6;"></i> Examination Seating Plan Generator
                </h3>
                <p class="text-muted small mb-0">Select class-sections to alternate seating desks, map room capacities, and ensure fair hall spacing.</p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/admin/academics" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Academics
                </a>
                <a href="{{ route('arrangements.index') }}" class="admin-btn admin-btn-primary admin-btn-sm fw-bold" style="background: #8B5CF6; border-color: #8B5CF6;">
                    <i class="bi bi-diagram-3 me-1"></i> View All Arrangements
                </a>
                <a href="/roll-slips" class="admin-btn admin-btn-outline admin-btn-sm fw-bold">
                    <i class="bi bi-file-earmark-text-fill me-1"></i> View Roll Slips
                </a>
            </div>
        </div>
    </div>

    {{-- SYSTEM ALERTS & VALIDATION ERRORS --}}
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
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white" style="border-top: 4px solid #8B5CF6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 48px; height: 48px; border-radius: 14px; background: #F5F3FF; color: #8B5CF6; display: grid; place-items: center; font-size: 24px; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0 fs-5">Generate Seating Arrangement</h4>
                            <p class="text-muted small mb-0">Select class-sections to calculate room capacities and automatically mix student desks.</p>
                        </div>
                    </div>
                    <span class="badge bg-purple-subtle text-purple fw-bold px-3 py-1 rounded-pill" style="font-size: 12px; background: #EDE9FE; color: #7C3AED;">
                        Exam Seating Matrix
                    </span>
                </div>

                <form action="{{ route('arrangements.store') }}" method="POST" id="arrangementForm">
                    @csrf
                    <div class="row g-4">
                        {{-- Field 1: Class-Sections Multiselect --}}
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Class Sections &amp; Desk Allocation <span class="text-danger">*</span>
                            </label>

                            <div class="arrangement-selector-wrapper" data-arrangement-selector>
                                <button type="button" class="arrangement-trigger-btn" id="arrangementTriggerBtn" data-arrangement-trigger aria-expanded="false">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="bi bi-building-fill-check text-primary"></i>
                                        <span id="arrangementTriggerText" data-arrangement-trigger-text>Select class-sections to arrange...</span>
                                    </span>
                                    <i class="bi bi-chevron-down text-muted"></i>
                                </button>

                                <div class="arrangement-dropdown-card" id="arrangementDropdownMenu" data-arrangement-menu>
                                    <div class="arrangement-search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="search" placeholder="Search class or section name..." id="arrangementSearchInput" data-arrangement-search autocomplete="off">
                                    </div>
                                    <div class="text-muted small fw-bold px-2 mb-2" style="font-size: 11.5px;">AVAILABLE CLASS ROOMS &amp; STREAMS</div>

                                    <div class="arrangement-options-container" id="arrangementOptionsContainer" data-arrangement-options>
                                        @forelse($classOptions as $class)
                                            <label class="arrangement-option-item" data-arrangement-option data-search="{{ strtolower($class->class_name.' '.$class->section_name.' room '.$class->class_no) }}">
                                                <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" @checked(in_array($class->id, $oldClassIds, true))>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <strong class="text-dark fs-6">{{ $class->class_name }} ({{ $class->section_name }})</strong>
                                                        <span class="badge bg-light text-dark border">Room #{{ $class->class_no }}</span>
                                                    </div>
                                                    <small class="text-muted d-block" style="font-size: 11.5px;">Capacity: {{ $class->chairs }} chairs quota</small>
                                                </div>
                                            </label>
                                        @empty
                                            <div class="text-center text-muted py-3 small" data-arrangement-no-results>
                                                No configured class-sections found. Add classes first.
                                            </div>
                                        @endforelse
                                        <div class="text-center text-muted py-3 small d-none" id="noMatchMsg" data-arrangement-no-results>
                                            No matching class-section found.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('class_ids')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Choose two or more classes to alternate seating desks, or a single class room.</small>
                        </div>

                        {{-- Field 2: Calculated Rooms --}}
                        <div class="col-md-7">
                            <label for="arrangement-rooms" class="form-label fw-bold text-dark small mb-1">
                                Selected Class Nos / Rooms
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-door-open-fill"></i></span>
                                <input id="arrangement-rooms" type="text" readonly value="No class-section selected" class="form-control bg-light border-start-0 fw-bold text-dark" style="height: 46px; cursor: not-allowed;">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Designated examination hall room numbers.</small>
                        </div>

                        {{-- Field 3: Calculated Chairs --}}
                        <div class="col-md-5">
                            <label for="arrangement-chairs" class="form-label fw-bold text-dark small mb-1">
                                Total Available Chairs
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-success"><i class="bi bi-chair"></i></span>
                                <input id="arrangement-chairs" type="number" readonly value="0" class="form-control bg-light border-start-0 fw-bold text-dark" style="height: 46px; cursor: not-allowed;">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Aggregate desk capacity for selected rooms.</small>
                        </div>

                        {{-- Calculation Preview Card --}}
                        <div class="col-12">
                            <div class="preview-info-card d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #EDE9FE; color: #7C3AED; display: grid; place-items: center; font-size: 20px; flex-shrink: 0;">
                                    <i class="bi bi-info-circle-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong class="text-dark d-block fs-6 mb-1" id="arrangement-preview-title">No class-section selected</strong>
                                    <span class="text-muted small" id="arrangement-preview-detail">Select one or more class-sections from the dropdown above to calculate total seats.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-12 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                            <button type="submit" class="admin-btn admin-btn-primary px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px; background: #8B5CF6; border-color: #8B5CF6;" @disabled($classOptions->isEmpty())>
                                <i class="bi bi-magic me-1"></i> Generate Seating Plan
                            </button>
                            <a href="{{ route('arrangements.index') }}" class="admin-btn admin-btn-outline px-4 py-2 fw-bold" style="font-size: 14.5px; border-radius: 10px;">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT SIDEBAR WIDGETS (RIGHT COLUMN: 4 COLS) --}}
        <div class="col-lg-4">
            {{-- Widget 1: Smart Seating Algorithm --}}
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" style="border-top: 4px solid #10B981 !important;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #10B981; display: grid; place-items: center; font-size: 18px;">
                        <i class="bi bi-shuffle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Alternating Seating Logic</h6>
                        <small class="text-muted">Anti-Cheating Spacing</small>
                    </div>
                </div>
                <p class="text-muted small mb-3" style="line-height: 1.6;">
                    The arrangement engine automatically mixes students from different selected class streams in alternating sequence across desks:
                </p>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>No two adjacent seats belong to the same class batch</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Desks sequentially numbered per room (Seat #1 to N)</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Matched with official student standard registration IDs</span>
                    </li>
                </ul>
            </div>

            {{-- Widget 2: Automatic System Reflection --}}
            <div class="admin-card border-0 shadow-sm rounded-4 p-4 bg-white" style="border-top: 4px solid #06B6D4 !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-database-check text-info me-1"></i> Multi-Portal Sync
                    </h6>
                    <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                        Instant
                    </span>
                </div>
                <p class="text-muted small mb-3">Upon generating seating arrangements, records immediately propagate:</p>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 12.5px;">
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Student Portal: Hall Room &amp; Desk allocation</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Teacher &amp; Incharge: Seating chart verification</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold"></i>
                        <span>Immediate readiness for Roll Slip datesheets</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT FOR INTERACTIVE DROPDOWN & LIVE CALCULATION --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const triggerBtn = document.getElementById('arrangementTriggerBtn');
    const triggerText = document.getElementById('arrangementTriggerText');
    const dropdownMenu = document.getElementById('arrangementDropdownMenu');
    const searchInput = document.getElementById('arrangementSearchInput');
    const options = document.querySelectorAll('.arrangement-option-item');
    const noMatchMsg = document.getElementById('noMatchMsg');

    const roomsInput = document.getElementById('arrangement-rooms');
    const chairsInput = document.getElementById('arrangement-chairs');
    const previewTitle = document.getElementById('arrangement-preview-title');
    const previewDetail = document.getElementById('arrangement-preview-detail');

    const records = @json($arrangementClassData);

    // Toggle dropdown menu
    triggerBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = dropdownMenu.classList.contains('show');
        if (isOpen) {
            dropdownMenu.classList.remove('show');
            triggerBtn.setAttribute('aria-expanded', 'false');
        } else {
            dropdownMenu.classList.add('show');
            triggerBtn.setAttribute('aria-expanded', 'true');
            if (searchInput) searchInput.focus();
        }
    });

    // Close dropdown on click outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('[data-arrangement-selector]')) {
            dropdownMenu.classList.remove('show');
            triggerBtn.setAttribute('aria-expanded', 'false');
        }
    });

    // Filter options in search box
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;
            options.forEach(opt => {
                const searchData = opt.getAttribute('data-search') || '';
                if (searchData.includes(query)) {
                    opt.style.display = 'flex';
                    visibleCount++;
                } else {
                    opt.style.display = 'none';
                }
            });
            if (noMatchMsg) {
                noMatchMsg.classList.toggle('d-none', visibleCount > 0);
            }
        });
    }

    // Refresh calculations when checkboxes change
    function refreshCalculations() {
        const checkedCheckboxes = document.querySelectorAll('input[name="class_ids[]"]:checked');
        const checkedIds = Array.from(checkedCheckboxes).map(cb => parseInt(cb.value));

        const selected = records.filter(r => checkedIds.includes(r.id));
        const rooms = selected.map(r => r.class_no).sort((a, b) => a - b);
        const chairs = selected.reduce((sum, r) => sum + parseInt(r.chairs || 0), 0);

        if (selected.length > 0) {
            roomsInput.value = 'Room ' + rooms.join(', Room ');
            chairsInput.value = chairs;
            triggerText.textContent = `${selected.length} class-section${selected.length === 1 ? '' : 's'} selected`;
            previewTitle.textContent = `${selected.length} class-section room${selected.length === 1 ? '' : 's'} selected`;
            previewDetail.textContent = `Rooms: ${rooms.join(', ')} · Total seating capacity: ${chairs} desks quota`;
        } else {
            roomsInput.value = 'No class-section selected';
            chairsInput.value = 0;
            triggerText.textContent = 'Select class-sections to arrange...';
            previewTitle.textContent = 'No class-section selected';
            previewDetail.textContent = 'Select one or more class-sections from the dropdown above to calculate total seats.';
        }
    }

    document.querySelectorAll('input[name="class_ids[]"]').forEach(cb => {
        cb.addEventListener('change', refreshCalculations);
    });

    // Run initial calculation for old input (if any)
    refreshCalculations();
});
</script>
@endsection