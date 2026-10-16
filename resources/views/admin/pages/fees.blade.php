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
    <form method="GET" action="{{ route('admin.page', 'fees') }}" class="row g-3 mb-4">
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
                        <th>Voucher #</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Month</th>
                        <th>Amount</th>
                        <th>Net Total</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feeCollections as $fee)
                        <tr>
                            <td>
                                <strong class="badge bg-dark text-light fw-bold" style="font-size: 11px;">
                                    {{ $fee->voucher_no ?: ('VOUCH-#'.$fee->id) }}
                                </strong>
                            </td>
                            <td>
                                <strong class="d-block text-dark">{{ $fee->student?->name ?? 'Student' }}</strong>
                                <small class="text-muted">Roll #{{ $fee->student?->std_id ?: $fee->student_id }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark fw-bold">
                                    {{ $fee->student?->class ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis fw-bold">
                                    {{ $fee->month }}
                                </span>
                            </td>
                            <td>Rs {{ number_format($fee->amount, 2) }}</td>
                            <td>
                                <strong class="text-dark fs-6">Rs {{ number_format($fee->net_amount, 2) }}</strong>
                            </td>
                            <td>
                                <span class="admin-badge {{ strtolower($fee->status) }}">
                                    {{ $fee->status }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $fee->paid_date ? Carbon\Carbon::parse($fee->paid_date)->format('M d, Y') : '-' }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="/fee-structure/voucher/{{ $fee->id }}" target="_blank" class="admin-btn admin-btn-outline admin-btn-sm" title="View/Print Voucher">
                                        <i class="bi bi-printer"></i> Voucher
                                    </a>

                                    @if($fee->status !== 'Paid')
                                        <form action="/fee-structure/collection/{{ $fee->id }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Paid">
                                            <input type="hidden" name="paid_date" value="{{ date('Y-m-d') }}">
                                            <button type="submit" class="admin-btn admin-btn-success admin-btn-sm" title="Mark as Paid">
                                                <i class="bi bi-check-lg"></i> Collect
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

{{-- GENERATE FEE VOUCHER MODAL --}}
<div class="modal fade" id="addFeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.fee.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-receipt-cutoff text-primary"></i> Issue Fee Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Student *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Select Student --</option>
                            @foreach($studentsList as $std)
                                <option value="{{ $std->id }}">{{ $std->name }} (Class: {{ $std->class }} - Roll #{{ $std->std_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fee Month *</label>
                        <input type="month" name="month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Base Fee Amount (Rs) *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="e.g. 5000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fine Amount (Rs)</label>
                        <input type="number" step="0.01" name="fine_amount" class="form-control" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Discount Amount (Rs)</label>
                        <input type="number" step="0.01" name="discount_amount" class="form-control" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Paid">Paid</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Paid Date</label>
                        <input type="date" name="paid_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Generate Voucher</button>
                </div>
            </form>
        </div>
    </div>
</div>
