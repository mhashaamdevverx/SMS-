<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-receipt-cutoff text-primary"></i> Student Fee Structure & Fee Collections
        </h3>

        <div class="admin-filter-bar">
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addFeeModal">
                <i class="bi bi-plus-circle-fill"></i> Generate Fee Voucher
            </button>
        </div>
    </div>

    {{-- Filter controls --}}
    <form method="GET" action="{{ route('admin.page', 'fees') }}" class="admin-filter-form row g-3 mb-0">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by student name, roll no, voucher #..." value="{{ $searchQuery ?? '' }}">
        </div>
        <div class="col-md-3">
            <select name="class" class="form-select">
                <option value="">-- All Classes --</option>
                @foreach($classes as $c)
                    <option value="{{ $c->class_name }}" {{ ($selectedClass ?? '') == $c->class_name ? 'selected' : '' }}>
                        {{ $c->class_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="section" class="form-select">
                <option value="">-- All Sections --</option>
                @foreach($sections as $sec)
                    <option value="{{ $sec->section_name }}" {{ ($selectedSection ?? '') == $sec->section_name ? 'selected' : '' }}>
                        Section {{ $sec->section_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="admin-btn admin-btn-primary w-100 justify-content-center">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </form>

    @if($feeCollections->isEmpty())
        <div class="admin-empty-state">
            <i class="bi bi-receipt admin-empty-icon"></i>
            <p>No fee records found. Click "Generate Fee Voucher" above to create fee records.</p>
        </div>
    @else
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="text-center">Voucher #</th>
                        <th class="text-center">Student</th>
                        <th class="text-center">Class</th>
                        <th class="text-center">Month</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Net Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Paid Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feeCollections as $fee)
                        <tr>
                            <td class="text-center">
                                <strong class="badge bg-dark text-light fw-bold" style="font-size: 11px;">
                                    {{ $fee->voucher_no ?: ('VOUCH-#'.$fee->id) }}
                                </strong>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <strong class="d-block text-dark" style="white-space: nowrap;">{{ $fee->student?->name ?? 'Student' }}</strong>
                                <small class="text-muted">Roll #{{ $fee->student?->std_id ?: $fee->student_id }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-dark fw-bold">
                                    {{ $fee->student?->class ?: '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info-emphasis fw-bold">
                                    {{ $fee->month }}
                                </span>
                            </td>
                            <td class="text-center">Rs {{ number_format($fee->tuition_fee ?? $fee->amount ?? 0, 2) }}</td>
                            <td class="text-center">
                                <strong class="text-dark fs-6">Rs {{ number_format($fee->net_amount, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="admin-badge {{ strtolower($fee->status) }}">
                                    {{ $fee->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small class="text-muted">{{ $fee->paid_date ? Carbon\Carbon::parse($fee->paid_date)->format('M d, Y') : '-' }}</small>
                            </td>
                            <td class="text-center">
                                <div class="admin-table-actions justify-content-center">
                                    {{-- Print Voucher --}}
                                    <a href="/fee-structure/voucher/{{ $fee->id }}" target="_blank" class="admin-btn-icon print" title="View/Print Voucher">
                                        <i class="bi bi-printer"></i>
                                    </a>

                                    @if($fee->status !== 'Paid')
                                        {{-- Mark as Paid --}}
                                        <form action="/fee-structure/collection/{{ $fee->id }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Paid">
                                            <input type="hidden" name="paid_date" value="{{ date('Y-m-d') }}">
                                            <button type="submit" class="admin-btn-icon pay" title="Mark as Paid">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($feeCollections instanceof \Illuminate\Pagination\LengthAwarePaginator && $feeCollections->total() > 0)
            <div class="admin-pagination-bar">
                <div class="admin-pagination-info">
                    Showing <strong>{{ $feeCollections->firstItem() }}</strong> to <strong>{{ $feeCollections->lastItem() }}</strong> of <strong>{{ $feeCollections->total() }}</strong> fee records
                </div>
                <div class="admin-pagination-links">
                    <ul class="pagination mb-0">
                        @if ($feeCollections->onFirstPage())
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $feeCollections->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                        @endif

                        @foreach ($feeCollections->getUrlRange(1, $feeCollections->lastPage()) as $page => $url)
                            @if ($page == $feeCollections->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @elseif ($page == 1 || $page == $feeCollections->lastPage() || abs($page - $feeCollections->currentPage()) <= 2)
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @elseif (abs($page - $feeCollections->currentPage()) == 3)
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            @endif
                        @endforeach

                        @if ($feeCollections->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $feeCollections->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
    @endif
</div>

{{-- GENERATE FEE VOUCHER MODAL (2 FIELDS PER ROW, COMPACT, NO SCROLLBAR) --}}
<div class="modal fade" id="addFeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('admin.fee.store') }}" method="POST" id="generateFeeForm">
                @csrf
                <div class="modal-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold fs-6 text-dark mb-0">
                        <i class="bi bi-receipt text-primary me-1"></i> Issue Student Fee Voucher
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3" style="max-height: none; overflow: visible;">
                    <div class="row g-2">
                        {{-- Row 1: Student and Fee Month --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="fee_student_select" class="form-select form-select-sm" required>
                                <option value="">-- Choose Student --</option>
                                @foreach($studentsList as $std)
                                    <option value="{{ $std->id }}"
                                        data-name="{{ $std->name }}"
                                        data-father="{{ $std->father_name }}"
                                        data-std-id="{{ $std->std_id }}"
                                        data-class="{{ $std->class }}"
                                        data-section="{{ $std->section }}"
                                        data-phone="{{ $std->phone_no ?: $std->emergency_phone }}"
                                        data-monthly-fee="{{ $std->monthly_fee ?: 0 }}"
                                        data-admission-fee="{{ $std->admission_fee ?: 0 }}">
                                        {{ $std->name }} (Roll #{{ $std->std_id ?: $std->id }} - {{ $std->class }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Fee Month <span class="text-danger">*</span></label>
                            <input type="text" name="month" id="fee_month" class="form-control form-control-sm" value="{{ date('F Y') }}" placeholder="e.g. Feb - 2026" required>
                        </div>

                        {{-- Row 2: Father's Name and Voucher No --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Father's Name</label>
                            <input type="text" name="father_name" id="fee_father_name" class="form-control form-control-sm" placeholder="Father Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Voucher No</label>
                            <input type="text" name="voucher_no" id="fee_voucher_no" class="form-control form-control-sm" placeholder="Auto-generated if left empty">
                        </div>

                        {{-- Row 3: Mobile No and Class / Section --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Mobile No</label>
                            <input type="text" name="mobile_no" id="fee_mobile_no" class="form-control form-control-sm" placeholder="03XXXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Student Class & Section</label>
                            <div class="d-flex align-items-center h-100 pb-1">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 w-100 text-start" id="fee_class_badge" style="font-size: 12px;">
                                    <i class="bi bi-mortarboard me-1"></i> Not Selected
                                </span>
                            </div>
                        </div>

                        {{-- Row 4: Issue Date and Due Date --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Issue Date</label>
                            <input type="date" name="issue_date" id="fee_issue_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Due Date</label>
                            <input type="date" name="due_date" id="fee_due_date" class="form-control form-control-sm" value="{{ date('Y-m-10') }}">
                        </div>

                        {{-- Row 5: Monthly Fee and Arrears --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Monthly Fee (Rs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="tuition_fee" id="fee_monthly" class="form-control form-control-sm fw-bold text-dark" placeholder="e.g. 3995" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Arrears (Rs)</label>
                            <input type="number" step="0.01" name="arrears" id="fee_arrears" class="form-control form-control-sm" value="0">
                        </div>

                        {{-- Row 6: Admission Fee and Late Fee --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Admission Fee (Rs)</label>
                            <input type="number" step="0.01" name="admission_fee" id="fee_admission" class="form-control form-control-sm" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Late Fee (After Due Date) (Rs)</label>
                            <input type="number" step="0.01" name="late_fee" id="fee_late_fee" class="form-control form-control-sm" value="500">
                        </div>

                        {{-- Row 7: Payment Status and Paid Date --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1">Payment Status <span class="text-danger">*</span></label>
                            <select name="status" id="fee_status" class="form-select form-select-sm" required>
                                <option value="Pending" selected>Pending</option>
                                <option value="Paid">Paid</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="fee_paid_date_box">
                            <label class="form-label fw-semibold small mb-1">Paid Date</label>
                            <input type="date" name="paid_date" id="fee_paid_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- Row 8: Live Calculation Summary Banner --}}
                        <div class="col-12 mt-2">
                            <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center" style="font-size: 12px;">
                                <div>
                                    <span class="text-muted">Total (Within Due Date):</span>
                                    <strong class="text-success ms-1 fs-6" id="fee_total_within">Rs 0</strong>
                                </div>
                                <div class="vr mx-2"></div>
                                <div>
                                    <span class="text-muted">Total (After Due Date):</span>
                                    <strong class="text-danger ms-1 fs-6" id="fee_total_after">Rs 0</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-4">
                        <i class="bi bi-check-circle me-1"></i> Generate Fee Slip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('fee_student_select');
    const fatherInput = document.getElementById('fee_father_name');
    const mobileInput = document.getElementById('fee_mobile_no');
    const voucherInput = document.getElementById('fee_voucher_no');
    const classBadge = document.getElementById('fee_class_badge');
    const monthlyInput = document.getElementById('fee_monthly');
    const admissionInput = document.getElementById('fee_admission');
    const arrearsInput = document.getElementById('fee_arrears');
    const lateFeeInput = document.getElementById('fee_late_fee');
    const statusSelect = document.getElementById('fee_status');
    const paidDateInput = document.getElementById('fee_paid_date');
    const totalWithinSpan = document.getElementById('fee_total_within');
    const totalAfterSpan = document.getElementById('fee_total_after');

    function calculateFeeTotals() {
        const monthly = parseFloat(monthlyInput?.value) || 0;
        const arrears = parseFloat(arrearsInput?.value) || 0;
        const admission = parseFloat(admissionInput?.value) || 0;
        const lateFee = parseFloat(lateFeeInput?.value) || 0;
        const within = monthly + arrears + admission;
        const after = within + lateFee;
        if (totalWithinSpan) totalWithinSpan.textContent = 'Rs ' + Math.round(within).toLocaleString('en-US');
        if (totalAfterSpan) totalAfterSpan.textContent = 'Rs ' + Math.round(after).toLocaleString('en-US');
    }

    if (studentSelect) {
        studentSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!opt || !opt.value) {
                if (classBadge) classBadge.innerHTML = '<i class="bi bi-mortarboard me-1"></i> Not Selected';
                return;
            }
            if (fatherInput) fatherInput.value = opt.getAttribute('data-father') || '';
            if (mobileInput) mobileInput.value = opt.getAttribute('data-phone') || '';
            const stdId = opt.getAttribute('data-std-id') || opt.value;
            const cls = opt.getAttribute('data-class') || '-';
            const sec = opt.getAttribute('data-section') || '';
            if (classBadge) classBadge.innerHTML = '<i class="bi bi-mortarboard me-1"></i> Class: <strong>' + cls + '</strong>' + (sec ? ' (' + sec + ')' : '');

            const mFee = parseFloat(opt.getAttribute('data-monthly-fee')) || 0;
            const aFee = parseFloat(opt.getAttribute('data-admission-fee')) || 0;
            if (monthlyInput && (!monthlyInput.value || monthlyInput.value == '0')) {
                monthlyInput.value = mFee > 0 ? mFee : '';
            }
            if (admissionInput && (!admissionInput.value || admissionInput.value == '0')) {
                admissionInput.value = aFee > 0 ? aFee : 0;
            }

            const now = new Date();
            const monthCode = String(now.getMonth() + 1).padStart(2, '0') + String(now.getFullYear()).slice(-2);
            if (voucherInput && !voucherInput.value) {
                voucherInput.value = 'V-' + stdId + '-' + monthCode;
            }

            calculateFeeTotals();
        });
    }

    [monthlyInput, arrearsInput, admissionInput, lateFeeInput].forEach(function(inp) {
        if (inp) inp.addEventListener('input', calculateFeeTotals);
    });

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'Paid') {
                if (paidDateInput && !paidDateInput.value) {
                    paidDateInput.value = new Date().toISOString().split('T')[0];
                }
            }
        });
    }

    calculateFeeTotals();
});
</script>
