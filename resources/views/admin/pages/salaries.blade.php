<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-cash-stack text-success"></i> Teacher Salary Declaration & Management
        </h3>

        <div class="admin-filter-bar">
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#declareSalaryModal">
                <i class="bi bi-plus-circle-fill"></i> Declare Teacher Salary
            </button>
        </div>
    </div>

    {{-- Salary Metrics Header Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 border">
                <small class="text-muted fw-bold uppercase">Total Declared Salaries</small>
                <h4 class="fw-extrabold text-dark mb-0">Rs {{ number_format($totalDeclaredSalaries ?? 0, 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-success-subtle rounded-3 border border-success-subtle">
                <small class="text-success fw-bold uppercase">Total Salaries Paid</small>
                <h4 class="fw-extrabold text-success mb-0">Rs {{ number_format($totalPaidSalaries ?? 0, 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle">
                <small class="text-warning fw-bold uppercase">Total Pending Salaries</small>
                <h4 class="fw-extrabold text-warning mb-0">Rs {{ number_format($totalPendingSalaries ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>

    @if($salaries->isEmpty())
        <div class="admin-empty-state">
            <i class="bi bi-wallet2 admin-empty-icon"></i>
            <p>No teacher salaries declared yet. Click "Declare Teacher Salary" above to add salary declarations.</p>
        </div>
    @else
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Month</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $sal)
                        <tr>
                            <td>
                                <strong class="d-block text-dark">{{ $sal->teacher?->teacher_name ?? 'Staff' }}</strong>
                                <small class="text-muted">{{ $sal->teacher?->teacher_email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-dark text-light fw-bold" style="font-size: 11px;">
                                    {{ $sal->month }}
                                </span>
                            </td>
                            <td>Rs {{ number_format($sal->basic_salary, 2) }}</td>
                            <td class="text-success">+Rs {{ number_format($sal->allowances, 2) }}</td>
                            <td class="text-danger">-Rs {{ number_format($sal->deductions, 2) }}</td>
                            <td>
                                <strong class="text-dark fs-6">Rs {{ number_format($sal->net_salary, 2) }}</strong>
                            </td>
                            <td>
                                <span class="admin-badge {{ strtolower($sal->status) }}">
                                    {{ $sal->status }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $sal->paid_date ? Carbon\Carbon::parse($sal->paid_date)->format('M d, Y') : '-' }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.salaries.payslip', $sal->id) }}" target="_blank" class="admin-btn admin-btn-outline admin-btn-sm" title="Print Payslip">
                                        <i class="bi bi-printer"></i> Slip
                                    </a>

                                    <button type="button" class="admin-btn admin-btn-primary admin-btn-sm" data-bs-toggle="modal" data-bs-target="#editSalaryModal{{ $sal->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <form action="{{ route('admin.salary.destroy', $sal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this salary record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Salary Modal for this item --}}
                        <div class="modal fade" id="editSalaryModal{{ $sal->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.salary.update', $sal->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Edit Salary Declaration</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Teacher</label>
                                                <input type="text" class="form-control" value="{{ $sal->teacher?->teacher_name }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Month</label>
                                                <input type="text" class="form-control" value="{{ $sal->month }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Basic Salary (Rs)</label>
                                                <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ $sal->basic_salary }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Allowances (Rs)</label>
                                                <input type="number" step="0.01" name="allowances" class="form-control" value="{{ $sal->allowances }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Deductions (Rs)</label>
                                                <input type="number" step="0.01" name="deductions" class="form-control" value="{{ $sal->deductions }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="Pending" {{ $sal->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Paid" {{ $sal->status === 'Paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="Unpaid" {{ $sal->status === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Paid Date</label>
                                                <input type="date" name="paid_date" class="form-control" value="{{ $sal->paid_date }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
        </div>

        @if($salaries instanceof \Illuminate\Pagination\LengthAwarePaginator && $salaries->hasPages())
            <div class="admin-pagination-bar">
                <div class="admin-pagination-info">
                    Showing <strong>{{ $salaries->firstItem() }}</strong> to <strong>{{ $salaries->lastItem() }}</strong> of <strong>{{ $salaries->total() }}</strong> records
                </div>
                <div class="admin-pagination-links">
                    {{ $salaries->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    @endif
</div>

{{-- DECLARE TEACHER SALARY MODAL --}}
<div class="modal fade" id="declareSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.salary.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin text-success"></i> Declare Teacher Salary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Teacher *</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Choose Teacher --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->teacher_name }} ({{ $t->course ?: 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Salary Month *</label>
                        <input type="month" name="month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Basic Salary (Rs) *</label>
                        <input type="number" step="0.01" name="basic_salary" class="form-control" placeholder="e.g. 45000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Allowances (Rs)</label>
                        <input type="number" step="0.01" name="allowances" class="form-control" placeholder="e.g. 5000" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deductions (Rs)</label>
                        <input type="number" step="0.01" name="deductions" class="form-control" placeholder="e.g. 1000" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="paid_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Declare Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>
