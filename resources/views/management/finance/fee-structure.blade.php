@extends('layout')

@section('content')

<div class="container" data-fee-page>

    {{-- HEADER --}}
    <div class="student-header ext-style-126">
        <div class="ext-style-109">
            <h1 class="ext-style-127">Fee Structure & Management</h1>
            <span class="ext-style-111">
                <i class="bi bi-wallet2 ext-style-112"></i> Expected: Rs. {{ number_format($stats['total_expected']) }}
            </span>
        </div>
        <div class="ext-style-128">
            @if($currentUser->hasPermission('fees.create'))
            <button type="button" class="student-add-btn" data-crud-modal-open="add-class-fee-modal">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Set Class Fee
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="success auto-hide-message">{{ session('success') }}</div>
    @endif

    {{-- SUMMARY STATS GRID --}}
    <div class="ext-style-129">
        <div class="ext-style-130">
            <div class="ext-style-131">
                <span class="ext-style-132">Total Expected Fee</span>
                <span class="ext-style-133"><i class="bi bi-bank"></i></span>
            </div>
            <div class="ext-style-134">Rs. {{ number_format($stats['total_expected']) }}</div>
            <div class="ext-style-135">Month: {{ $selectedMonth }}</div>
        </div>

        <div class="ext-style-130">
            <div class="ext-style-131">
                <span class="ext-style-132">Collected Fee</span>
                <span class="ext-style-136"><i class="bi bi-check-circle-fill"></i></span>
            </div>
            <div class="ext-style-137">Rs. {{ number_format($stats['total_collected']) }}</div>
            <div class="ext-style-138">{{ $stats['paid_count'] }} Students Paid</div>
        </div>

        <div class="ext-style-130">
            <div class="ext-style-131">
                <span class="ext-style-132">Pending / Overdue</span>
                <span class="ext-style-139"><i class="bi bi-clock-history"></i></span>
            </div>
            <div class="ext-style-140">Rs. {{ number_format($stats['total_pending']) }}</div>
            <div class="ext-style-141">{{ $stats['pending_count'] }} Pending · {{ $stats['overdue_count'] }} Overdue</div>
        </div>

        <div class="ext-style-130">
            <div class="ext-style-131">
                <span class="ext-style-132">Collection Rate</span>
                <span class="ext-style-142"><i class="bi bi-pie-chart-fill"></i></span>
            </div>
            @php
                $ratio = $stats['total_expected'] > 0 ? round(($stats['total_collected'] / $stats['total_expected']) * 100, 1) : 0;
            @endphp
            <div class="ext-style-143">{{ $ratio }}%</div>
            <div class="ext-style-144">Active Fee System</div>
        </div>
    </div>


    {{-- SECTION 1: CLASS-WISE FEE STRUCTURE TABLE --}}
    <div class="ext-style-145">
        <div class="ext-style-146">
            <h2 class="ext-style-147">
                <i class="bi bi-layers-fill ext-style-104"></i> Class Fee Structures
            </h2>
            <span class="ext-style-148">Total {{ $feeStructures->count() }} Classes Configured</span>
        </div>

        <div class="unified-table-card fee-structure-card"><div class="table-responsive-wrapper fee-structure-responsive"><table class="student-table unified-table fee-structure-table">
            <thead>
                <tr>
                    <th class="text-start" style="width: 8%;">Class</th>
                    <th class="text-center" style="width: 14%;">Tuition Fee<br><small class="opacity-75">(Monthly)</small></th>
                    <th class="text-center" style="width: 11%;">Admission Fee</th>
                    <th class="text-center" style="width: 11%;">Exam Fee</th>
                    <th class="text-center" style="width: 11%;">Test Session</th>
                    <th class="text-center" style="width: 11%;">Lab / Sports</th>
                    <th class="text-center" style="width: 11%;">Other Charges</th>
                    <th class="text-center" style="width: 13%;">Total Monthly</th>
                    <th class="text-center" style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feeStructures as $fs)
                    <tr>
                        <td><strong>{{ $fs->class_name }}</strong></td>
                        <td>Rs. {{ number_format($fs->tuition_fee) }}</td>
                        <td>Rs. {{ number_format($fs->admission_fee) }}</td>
                        <td>Rs. {{ number_format($fs->exam_fee) }}</td>
                        <td>Rs. {{ number_format($fs->test_session_fee) }}</td>
                        <td>Rs. {{ number_format($fs->lab_sports_fee) }}</td>
                        <td>Rs. {{ number_format($fs->other_charges) }}</td>
                        <td>
                            <span class="badge ext-style-149">
                                Rs. {{ number_format($fs->total_fee) }}
                            </span>
                        </td>
                        <td class="table-actions">
                            @if($currentUser->hasPermission('fees.update'))<button type="button" class="icon-action edit-action" data-crud-modal-open="edit-fee-modal-{{ $fs->id }}" title="Edit Class Fee" aria-label="Edit Class Fee"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>@endif
                            @if($currentUser->hasPermission('fees.delete'))
                            <form action="{{ route('fee-structure.class.destroy', $fs) }}" method="POST" data-crud-action="delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-action delete-action" title="Delete Fee Structure" aria-label="Delete Fee Structure">
                                    <i class="bi bi-trash3" aria-hidden="true"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="student-filter-empty">No class fee structures configured yet. Click "+ Set Class Fee" to add.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div></div>
    </div>


    {{-- SECTION 2: STUDENT FEE COLLECTIONS & STATUS --}}
    <div>
        <div class="ext-style-150">
            <h2 class="ext-style-147">
                <i class="bi bi-receipt-cutoff ext-style-104"></i> Student Fee Vouchers & Collection
            </h2>
        </div>

        {{-- FILTERS FORM --}}
        <form action="{{ route('fee-structure.index') }}" method="GET" class="student-filter-form ext-style-151">
            <div class="student-search-field">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ $search }}" placeholder="Search by student name, roll no or voucher #" aria-label="Search student fee records">
            </div>

            <select name="class" class="student-class-filter" aria-label="Filter by class">
                <option value="">All Classes</option>
                @foreach($classOptions as $c)
                    <option value="{{ $c }}" @selected($selectedClass === $c)>{{ $c }}</option>
                @endforeach
            </select>

            <select name="status" class="student-class-filter" aria-label="Filter by status">
                <option value="">All Statuses</option>
                <option value="Paid" @selected($selectedStatus === 'Paid')>Paid</option>
                <option value="Pending" @selected($selectedStatus === 'Pending')>Pending</option>
                <option value="Overdue" @selected($selectedStatus === 'Overdue')>Overdue</option>
            </select>

            <select name="month" class="student-class-filter" aria-label="Filter by month">
                @foreach($availableMonths as $m)
                    <option value="{{ $m }}" @selected($selectedMonth === $m)>{{ $m }}</option>
                @endforeach
            </select>
        </form>

        {{-- STUDENT FEE COLLECTIONS TABLE --}}
        <div class="unified-table-card fee-structure-card"><div class="table-responsive-wrapper fee-structure-responsive"><table class="student-table unified-table fee-structure-table">
            <thead>
                <tr>
                    <th class="text-start" style="width: 10%;">Voucher #</th>
                    <th class="text-start" style="width: 16%;">Student Name</th>
                    <th class="text-center" style="width: 14%;">Std. ID / Class</th>
                    <th class="text-center" style="width: 10%;">Month</th>
                    <th class="text-center" style="width: 11%;">Gross Amount</th>
                    <th class="text-center" style="width: 10%;">Discount</th>
                    <th class="text-center" style="width: 11%;">Net Amount</th>
                    <th class="text-center" style="width: 10%;">Status</th>
                    <th class="text-center" style="width: 8%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feeCollections as $fc)
                    <tr>
                        <td><strong class="ext-style-152">{{ $fc->voucher_no }}</strong></td>
                        <td>{{ $fc->student_name }}</td>
                        <td>{{ $fc->std_id ?? 'N/A' }} ({{ $fc->student_class }})</td>
                        <td>{{ $fc->month }}</td>
                        <td>Rs. {{ number_format($fc->gross_amount) }}</td>
                        <td>{{ $fc->discount > 0 ? 'Rs. '.number_format($fc->discount) : '-' }}</td>
                        <td><strong>Rs. {{ number_format($fc->net_amount) }}</strong></td>
                        <td>
                            @if($fc->status === 'Paid')
                                <span class="badge ext-style-153">
                                    <i class="bi bi-check-circle-fill"></i> Paid
                                </span>
                            @elseif($fc->status === 'Pending')
                                <span class="badge ext-style-154">
                                    <i class="bi bi-clock-fill"></i> Pending
                                </span>
                            @else
                                <span class="badge ext-style-155">
                                    <i class="bi bi-exclamation-octagon-fill"></i> Overdue
                                </span>
                            @endif
                        </td>
                        <td class="table-actions">
                            {{-- UPDATE STATUS MODAL BUTTON --}}
                            @if($currentUser->hasPermission('fees.update'))<button type="button" class="icon-action edit-action" data-crud-modal-open="status-modal-{{ $fc->id }}" title="Update Payment Status" aria-label="Update Payment Status">
                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                            </button>
                            @endif

                            {{-- PRINT VOUCHER LINK --}}
                            <a href="{{ route('fee-structure.voucher.show', $fc) }}" target="_blank" class="icon-action slip-download-action" title="Print Fee Voucher" aria-label="Print Fee Voucher">
                                <i class="bi bi-printer-fill" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="student-filter-empty">No fee records found for this filter criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div></div>

        {{-- PAGINATION --}}
        @if($feeCollections->total() > 0)
            <div class="flex-between-center-mt18-wrap">
                <p class="student-pagination-info m-0">
                    Showing {{ $feeCollections->firstItem() }} to {{ $feeCollections->lastItem() }} of {{ $feeCollections->total() }} fee records
                </p>

                @if($feeCollections->hasPages())
                    <div class="student-pagination">
                        {{ $feeCollections->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        @endif


{{-- MODAL: ADD CLASS FEE STRUCTURE --}}
@if($currentUser->hasPermission('fees.create'))
<div class="crud-backdrop" id="add-class-fee-modal" hidden>
    <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="add-class-fee-title">
        <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>
        <div class="modal-heading">
            <p class="modal-kicker">Fee Structure</p>
            <h2 id="add-class-fee-title">Set Class Fee Structure</h2>
            <p>Define monthly tuition, exam, lab & other charges for a class.</p>
        </div>
        <form action="{{ route('fee-structure.class.store') }}" method="POST" class="crud-form">
            @csrf
            <div class="crud-field">
                <label for="add-fee-class">Class Name</label>
                <select id="add-fee-class" name="class_name" class="student-section-select" required>
                    <option value="" disabled selected>-- Select Class --</option>
                    @foreach($classOptions as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="crud-field">
                <label for="add-tuition-fee">Monthly Tuition Fee (Rs.)</label>
                <input id="add-tuition-fee" type="number" name="tuition_fee" min="0" placeholder="e.g. 3500" required>
            </div>
            <div class="crud-field">
                <label for="add-admission-fee">One-time Admission Fee (Rs.)</label>
                <input id="add-admission-fee" type="number" name="admission_fee" min="0" placeholder="e.g. 5000">
            </div>
            <div class="crud-field">
                <label for="add-exam-fee">Exam Fee (Rs.)</label>
                <input id="add-exam-fee" type="number" name="exam_fee" min="0" placeholder="e.g. 500">
            </div>
            <div class="crud-field">
                <label for="add-test-session-fee">Test Session Fee (Rs.)</label>
                <input id="add-test-session-fee" type="number" name="test_session_fee" min="0" placeholder="e.g. 500">
            </div>
            <div class="crud-field">
                <label for="add-lab-fee">Lab / Sports Fee (Rs.)</label>
                <input id="add-lab-fee" type="number" name="lab_sports_fee" min="0" placeholder="e.g. 400">
            </div>
            <div class="crud-field">
                <label for="add-other-fee">Other Charges (Rs.)</label>
                <input id="add-other-fee" type="number" name="other_charges" min="0" placeholder="e.g. 300">
            </div>
            <button type="submit" class="modal-primary-btn crud-submit">Save Fee Structure</button>
        </form>
    </section>
</div>
@endif


{{-- MODALS: EDIT CLASS FEE STRUCTURE --}}
@if($currentUser->hasPermission('fees.update'))
@foreach($feeStructures as $fs)
    <div class="crud-backdrop" id="edit-fee-modal-{{ $fs->id }}" hidden>
        <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="edit-fee-title-{{ $fs->id }}">
            <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>
            <div class="modal-heading">
                <p class="modal-kicker">Edit Fee Structure</p>
                <h2 id="edit-fee-title-{{ $fs->id }}">Class {{ $fs->class_name }}</h2>
            </div>
            <form action="{{ route('fee-structure.class.update', $fs) }}" method="POST" class="crud-form">
                @csrf
                @method('PUT')
                <div class="crud-field">
                    <label for="edit-fee-class-{{ $fs->id }}">Class Name</label>
                    <input id="edit-fee-class-{{ $fs->id }}" type="text" name="class_name" value="{{ $fs->class_name }}" required>
                </div>
                <div class="crud-field">
                    <label for="edit-tuition-fee-{{ $fs->id }}">Monthly Tuition Fee (Rs.)</label>
                    <input id="edit-tuition-fee-{{ $fs->id }}" type="number" name="tuition_fee" value="{{ $fs->tuition_fee }}" min="0" required>
                </div>
                <div class="crud-field">
                    <label for="edit-admission-fee-{{ $fs->id }}">Admission Fee (Rs.)</label>
                    <input id="edit-admission-fee-{{ $fs->id }}" type="number" name="admission_fee" value="{{ $fs->admission_fee }}" min="0">
                </div>
                <div class="crud-field">
                    <label for="edit-exam-fee-{{ $fs->id }}">Exam Fee (Rs.)</label>
                    <input id="edit-exam-fee-{{ $fs->id }}" type="number" name="exam_fee" value="{{ $fs->exam_fee }}" min="0">
                </div>
                <div class="crud-field">
                    <label for="edit-test-session-fee-{{ $fs->id }}">Test Session Fee (Rs.)</label>
                    <input id="edit-test-session-fee-{{ $fs->id }}" type="number" name="test_session_fee" value="{{ $fs->test_session_fee }}" min="0">
                </div>
                <div class="crud-field">
                    <label for="edit-lab-fee-{{ $fs->id }}">Lab / Sports Fee (Rs.)</label>
                    <input id="edit-lab-fee-{{ $fs->id }}" type="number" name="lab_sports_fee" value="{{ $fs->lab_sports_fee }}" min="0">
                </div>
                <div class="crud-field">
                    <label for="edit-other-fee-{{ $fs->id }}">Other Charges (Rs.)</label>
                    <input id="edit-other-fee-{{ $fs->id }}" type="number" name="other_charges" value="{{ $fs->other_charges }}" min="0">
                </div>
                <button type="submit" class="modal-primary-btn crud-submit">Update Fee Structure</button>
            </form>
        </section>
    </div>
@endforeach
@endif


{{-- MODALS: UPDATE PAYMENT STATUS --}}
@if($currentUser->hasPermission('fees.update'))
@foreach($feeCollections as $fc)
    <div class="crud-backdrop" id="status-modal-{{ $fc->id }}" hidden>
        <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="status-title-{{ $fc->id }}">
            <button type="button" class="modal-close" data-crud-modal-close aria-label="Close dialog">&times;</button>
            <div class="modal-heading">
                <p class="modal-kicker">Fee Voucher Status</p>
                <h2 id="status-title-{{ $fc->id }}">{{ $fc->student_name }}</h2>
                <p>Voucher #: <strong>{{ $fc->voucher_no }}</strong> ({{ $fc->student_class }})</p>
            </div>
            <form action="{{ route('fee-structure.collection.update', $fc) }}" method="POST" class="crud-form">
                @csrf
                @method('PUT')
                <div class="crud-field">
                    <label for="status-val-{{ $fc->id }}">Payment Status</label>
                    <select id="status-val-{{ $fc->id }}" name="status" class="student-section-select" required>
                        <option value="Paid" @selected($fc->status === 'Paid')>Paid</option>
                        <option value="Pending" @selected($fc->status === 'Pending')>Pending</option>
                        <option value="Overdue" @selected($fc->status === 'Overdue')>Overdue</option>
                    </select>
                </div>
                <div class="crud-field">
                    <label for="discount-val-{{ $fc->id }}">Discount / Concession (Rs.)</label>
                    <input id="discount-val-{{ $fc->id }}" type="number" name="discount" value="{{ $fc->discount }}" min="0">
                </div>
                <div class="crud-field">
                    <label for="method-val-{{ $fc->id }}">Payment Method</label>
                    <select id="method-val-{{ $fc->id }}" name="payment_method" class="student-section-select">
                        <option value="Cash" @selected($fc->payment_method === 'Cash')>Cash</option>
                        <option value="Bank Transfer" @selected($fc->payment_method === 'Bank Transfer')>Bank Transfer / Online</option>
                        <option value="Cheque" @selected($fc->payment_method === 'Cheque')>Cheque</option>
                    </select>
                </div>
                <div class="crud-field">
                    <label for="date-val-{{ $fc->id }}">Payment Date</label>
                    <input id="date-val-{{ $fc->id }}" type="date" name="payment_date" value="{{ $fc->payment_date ?? date('Y-m-d') }}">
                </div>
                <button type="submit" class="modal-primary-btn crud-submit">Update Payment Record</button>
            </form>
        </section>
    </div>
@endforeach
@endif

@endsection
