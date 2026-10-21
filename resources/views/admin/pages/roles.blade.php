<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-shield-lock-fill text-primary"></i> Roles & Perms Management Center
        </h3>

        <div class="admin-filter-bar">
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserRoleModal">
                <i class="bi bi-person-gear"></i> Assign Role to User
            </button>
            <button type="button" class="admin-btn admin-btn-success" data-bs-toggle="modal" data-bs-target="#assignInchargeModal">
                <i class="bi bi-person-workspace"></i> Assign Class Incharge
            </button>
            <button type="button" class="admin-btn admin-btn-outline" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="bi bi-plus-circle"></i> Create New Role
            </button>
        </div>
    </div>

    {{-- Pending Teacher Access Requests Alert --}}
    @if(isset($accessRequests) && $accessRequests->where('status', 'pending')->isNotEmpty())
        <div class="alert alert-warning border-0 shadow-sm rounded-4 m-3">
            <h5 class="fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Pending Teacher Registration / Access Requests</h5>
            <p class="small text-muted mb-3">The following teacher accounts are waiting for Admin verification and approval.</p>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Teacher Name</th>
                            <th>Email</th>
                            <th>Requested Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accessRequests->where('status', 'pending') as $req)
                            <tr>
                                <td><strong>{{ $req->teacher?->teacher_name ?: $req->user?->name }}</strong></td>
                                <td>{{ $req->teacher?->teacher_email ?: $req->user?->email }}</td>
                                <td><span class="badge bg-secondary">{{ $req->class_name ?: 'General' }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form action="{{ route('teacher-access-requests.approve', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="class_id" value="{{ $classes->first()?->id }}">
                                            <button type="submit" class="btn btn-sm btn-success fw-bold">Approve</button>
                                        </form>
                                        <form action="{{ route('teacher-access-requests.reject', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($accessRequests instanceof \Illuminate\Pagination\LengthAwarePaginator && $accessRequests->hasPages())
                <div class="mt-2 text-center">
                    {{ $accessRequests->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif

    <!-- Main Tabs for Role Management -->
    <div class="p-3">
        <ul class="nav nav-pills mb-3 border-bottom pb-2" id="roleCenterTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4" id="users-roles-tab" data-bs-toggle="pill" data-bs-target="#users-roles-panel" type="button" role="tab">
                    <i class="bi bi-people-fill me-2"></i> User Role Assignments ({{ isset($allUsers) && method_exists($allUsers, 'total') ? $allUsers->total() : count($allUsers ?? []) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4" id="system-roles-tab" data-bs-toggle="pill" data-bs-target="#system-roles-panel" type="button" role="tab">
                    <i class="bi bi-shield-check me-2"></i> System Roles & Permissions ({{ count($roles ?? []) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4" id="class-incharges-tab" data-bs-toggle="pill" data-bs-target="#class-incharges-panel" type="button" role="tab">
                    <i class="bi bi-person-workspace me-2"></i> Class Incharges ({{ isset($classIncharges) && method_exists($classIncharges, 'total') ? $classIncharges->total() : count($classIncharges ?? []) }})
                </button>
            </li>
        </ul>

        <div class="tab-content" id="roleCenterTabsContent">

            {{-- TAB 1: USER ROLE ASSIGNMENTS --}}
            <div class="tab-pane fade show active" id="users-roles-panel" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-gear text-primary"></i> User Accounts & Assigned Roles</h5>
                    <small class="text-muted">Manage and assign roles to system users, teachers, and admins</small>
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Email / Contact</th>
                                <th>Current Assigned Role</th>
                                <th>Class Incharge Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allUsers ?? [] as $u)
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark fs-6">{{ $u->name }}</strong>
                                        <small class="text-muted">Username: {{ $u->username }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-secondary">{{ $u->email ?: 'No Email' }}</div>
                                        <small class="text-muted">{{ $u->phone ?: '' }}</small>
                                    </td>
                                    <td>
                                        @if($u->isAdmin())
                                            <span class="badge bg-danger px-3 py-1 fw-bold"><i class="bi bi-shield-fill"></i> Administrator</span>
                                        @elseif($u->role)
                                            <span class="badge bg-primary px-3 py-1 fw-bold">{{ $u->role->name }}</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-1">No Role Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($u->classIncharge)
                                            <span class="badge bg-success-subtle text-success fw-bold">
                                                <i class="bi bi-check-circle"></i> Incharge: {{ $u->classIncharge->classRecord?->class_name }} ({{ $u->classIncharge->classRecord?->section_name }})
                                            </span>
                                        @else
                                            <span class="text-muted fs-7">None</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($u->isAdmin())
                                            <span class="badge bg-secondary-subtle text-secondary">Protected Admin</span>
                                        @else
                                            <div class="admin-table-actions">
                                                <button type="button" class="admin-btn-icon edit" data-bs-toggle="modal" data-bs-target="#changeUserRoleModal{{ $u->id }}" title="Assign / Change Role">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                {{-- MODAL CHANGE USER ROLE --}}
                                @if(!$u->isAdmin())
                                    <div class="modal fade admin-modal" id="changeUserRoleModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('roles.users.update', $u->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold"><i class="bi bi-person-gear text-primary me-2"></i> Assign Role to {{ $u->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted mb-3">Select a new system role for <strong>{{ $u->name }}</strong> ({{ $u->email }}):</p>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Select Role *</label>
                                                            <select name="role_id" class="form-select" required>
                                                                <option value="">-- Choose Role --</option>
                                                                @foreach($roles as $r)
                                                                    @if(!in_array($r->slug, ['admin', 'class-incharge']))
                                                                        <option value="{{ $r->id }}" {{ $u->role_id == $r->id ? 'selected' : '' }}>
                                                                            {{ $r->name }} — {{ $r->description ?: 'System role' }}
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="admin-btn admin-btn-primary">Update Role</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($allUsers instanceof \Illuminate\Pagination\LengthAwarePaginator && $allUsers->hasPages())
                    <div class="admin-pagination-bar">
                        <div class="admin-pagination-info">
                            Showing <strong>{{ $allUsers->firstItem() }}</strong> to <strong>{{ $allUsers->lastItem() }}</strong> of <strong>{{ $allUsers->total() }}</strong> users
                        </div>
                        <div class="admin-pagination-links">
                            <ul class="pagination mb-0">
                                @if ($allUsers->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $allUsers->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach ($allUsers->getUrlRange(1, $allUsers->lastPage()) as $page => $url)
                                    @if ($page == $allUsers->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif ($page == 1 || $page == $allUsers->lastPage() || abs($page - $allUsers->currentPage()) <= 2)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @elseif (abs($page - $allUsers->currentPage()) == 3)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if ($allUsers->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $allUsers->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            {{-- TAB 2: SYSTEM ROLES & PERMISSIONS MATRIX --}}
            <div class="tab-pane fade" id="system-roles-panel" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check text-success"></i> Defined System Roles & Specific Permissions</h5>
                        <small class="text-muted">Exact permissions assigned to each role defining what each user role is permitted to do</small>
                    </div>
                    <button type="button" class="admin-btn admin-btn-primary admin-btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="bi bi-plus-lg"></i> Create Custom Role
                    </button>
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 190px;">Role Name</th>
                                <th style="width: 130px;">Key / Slug</th>
                                <th style="width: 95px;">Users</th>
                                <th>Assigned Permissions (What this role can do)</th>
                                <th class="text-end" style="width: 90px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $r)
                                <tr>
                                    <td class="align-top">
                                        <strong class="text-dark fs-6 d-block">{{ $r->name }}</strong>
                                        @if($r->is_system)
                                            <span class="badge bg-info-subtle text-info-emphasis" style="font-size: 10px;">System Role</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success-emphasis" style="font-size: 10px;">Custom Role</span>
                                        @endif
                                        <small class="text-muted d-block mt-1">{{ $r->description ?: 'System defined access role' }}</small>
                                    </td>
                                    <td class="align-top">
                                        <code class="px-2 py-1 bg-light border rounded">{{ $r->slug }}</code>
                                    </td>
                                    <td class="align-top">
                                        <span class="badge bg-secondary-subtle text-dark fw-bold">
                                            <i class="bi bi-people me-1"></i> {{ $r->users_count }}
                                        </span>
                                    </td>
                                    <td class="align-top">
                                        @if($r->slug === 'admin')
                                            <div class="mb-2">
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold px-2 py-1" style="font-size: 12px;">
                                                    <i class="bi bi-shield-lock-fill me-1"></i> Super Administrator (All 52 Permissions Granted)
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-2">Full unrestricted permission across all modules: Students, Teachers, Staff Salaries, Fees, Attendance, Results, Academics, Incharges, System Accounts & Permissions.</p>
                                        @else
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 11px;">
                                                    <i class="bi bi-key-fill me-1"></i> {{ $r->permissions->count() }} Active Permissions:
                                                </span>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1" style="max-height: 160px; overflow-y: auto;">
                                                @forelse($r->permissions as $perm)
                                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px; font-weight: 600;">
                                                        <i class="bi bi-check-circle-fill text-success me-1" style="font-size: 10px;"></i>{{ $perm->label }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted small fst-italic">No permissions assigned to this role yet. Click edit icon to assign.</span>
                                                @endforelse
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end align-top">
                                        @if($r->slug === 'admin')
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">Protected</span>
                                        @else
                                            <div class="admin-table-actions justify-content-end">
                                                <button type="button" class="admin-btn-icon edit" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $r->id }}" title="Edit Role & Permissions">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                @if(!$r->is_system && $r->users_count == 0)
                                                    <form action="{{ route('roles.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Delete this custom role?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="admin-btn-icon danger" title="Delete Role">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                {{-- MODAL EDIT ROLE & PERMISSIONS --}}
                                @if($r->slug !== 'admin')
                                    <div class="modal fade admin-modal" id="editRoleModal{{ $r->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('roles.update', $r->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold"><i class="bi bi-shield-check text-primary me-2"></i> Edit Role: {{ $r->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold">Role Name *</label>
                                                                <input type="text" name="name" class="form-control" value="{{ $r->name }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold">Description</label>
                                                                <input type="text" name="description" class="form-control" value="{{ $r->description }}">
                                                            </div>
                                                        </div>

                                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Permissions Matrix</h6>
                                                        <div class="row g-3">
                                                            @foreach($permissionGroups ?? [] as $module => $perms)
                                                                <div class="col-md-6">
                                                                    <div class="p-3 bg-light rounded border h-100">
                                                                        <strong class="d-block text-primary text-uppercase fs-7 mb-2">{{ ucfirst($module) }} Module</strong>
                                                                        @foreach($perms as $perm)
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $r->id }}_{{ $perm->id }}" {{ $r->permissions->contains('id', $perm->id) ? 'checked' : '' }}>
                                                                                <label class="form-check-label fs-7" for="perm_{{ $r->id }}_{{ $perm->id }}">
                                                                                    {{ $perm->label }}
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="admin-btn admin-btn-primary">Save Role Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: CLASS INCHARGES --}}
            <div class="tab-pane fade" id="class-incharges-panel" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-workspace text-primary"></i> Assigned Class Incharge Teachers</h5>
                    <button type="button" class="admin-btn admin-btn-success admin-btn-sm" data-bs-toggle="modal" data-bs-target="#assignInchargeModal">
                        <i class="bi bi-plus-lg"></i> Assign New Incharge
                    </button>
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Class & Section</th>
                                <th>Incharge Teacher</th>
                                <th>Teacher Email</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classIncharges as $incharge)
                                <tr>
                                    <td>
                                        <strong class="badge bg-primary text-light fs-6">
                                            {{ $incharge->classRecord?->class_name }} / {{ $incharge->classRecord?->section_name }}
                                        </strong>
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $incharge->teacher?->teacher_name ?: $incharge->user?->name }}</strong>
                                    </td>
                                    <td>{{ $incharge->teacher?->teacher_email ?: $incharge->user?->email }}</td>
                                    <td class="text-end">
                                        <div class="admin-table-actions">
                                            <form action="{{ route('roles.incharges.destroy', $incharge->id) }}" method="POST" onsubmit="return confirm('Remove incharge assignment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-btn-icon danger" title="Remove Incharge Assignment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No Class Incharges assigned yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($classIncharges instanceof \Illuminate\Pagination\LengthAwarePaginator && $classIncharges->hasPages())
                    <div class="admin-pagination-bar">
                        <div class="admin-pagination-info">
                            Showing <strong>{{ $classIncharges->firstItem() }}</strong> to <strong>{{ $classIncharges->lastItem() }}</strong> of <strong>{{ $classIncharges->total() }}</strong> incharges
                        </div>
                        <div class="admin-pagination-links">
                            <ul class="pagination mb-0">
                                @if ($classIncharges->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $classIncharges->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach ($classIncharges->getUrlRange(1, $classIncharges->lastPage()) as $page => $url)
                                    @if ($page == $classIncharges->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif ($page == 1 || $page == $classIncharges->lastPage() || abs($page - $classIncharges->currentPage()) <= 2)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @elseif (abs($page - $classIncharges->currentPage()) == 3)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if ($classIncharges->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $classIncharges->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
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

{{-- MODAL 1: ASSIGN ROLE TO USER --}}
<div class="modal fade admin-modal" id="assignUserRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <form id="assignUserRoleForm" action="" method="POST" onsubmit="return validateAssignUserRole(this);">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold fs-6 text-dark mb-0">
                        <i class="bi bi-person-gear text-primary me-2"></i> Assign Role to System User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Step 1: Select Category (Students or Teachers) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-people-fill text-primary me-1"></i> Select User Category <span class="text-danger">*</span>
                        </label>
                        <select id="user_category_select" class="form-select" onchange="onCategoryChanged(this.value)" style="width: 100% !important; max-width: 100% !important; box-sizing: border-box !important;">
                            <option value="">-- Choose Category (Students or Teachers) --</option>
                            <option value="student">🎓 Students</option>
                            <option value="teacher">👨‍🏫 Teachers</option>
                        </select>
                    </div>

                    <!-- Step 2: Class & Section Filters (Cascading) -->
                    <div class="row g-2 mb-3" id="class_section_filter_row" style="display: none; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; width: 100%; margin: 0 0 16px 0;">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-mortarboard me-1"></i> Class
                            </label>
                            <select id="filter_class_select" class="form-select form-select-sm" onchange="filterUserList()" style="width: 100% !important; max-width: 100% !important; box-sizing: border-box !important;">
                                <option value="">-- All Classes --</option>
                                @foreach($classes->pluck('class_name')->filter()->unique()->values() as $cName)
                                    <option value="{{ $cName }}">{{ $cName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-grid-3x3-gap me-1"></i> Section
                            </label>
                            <select id="filter_section_select" class="form-select form-select-sm" onchange="filterUserList()" style="width: 100% !important; max-width: 100% !important; box-sizing: border-box !important;">
                                <option value="">-- All Sections --</option>
                                @foreach($sections->pluck('section_name')->filter()->unique()->values() as $sName)
                                    <option value="{{ $sName }}">{{ $sName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Step 3: Select Specific User Account (Opens strictly DOWNWARDS, bounded 100% between red lines) -->
                    <div class="mb-3 position-relative" id="user_select_container">
                        <label class="form-label fw-bold">
                            <i class="bi bi-person-check-fill text-primary me-1"></i> Select User Account <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" name="user_id" id="quick_user_select" value="" required>

                        <div class="custom-modal-select" style="position: relative; width: 100%;">
                            <button type="button" class="form-select text-start d-flex justify-content-between align-items-center w-100" id="userSelectBtn" onclick="toggleDropdownMenu('userDropdownMenu')" style="background-color: #fff; cursor: pointer; min-height: 42px; width: 100% !important; max-width: 100% !important; box-sizing: border-box !important;">
                                <span id="userSelectLabel" class="text-muted text-truncate" style="max-width: calc(100% - 24px);">-- Select Category First --</span>
                                <i class="bi bi-chevron-down text-muted" style="font-size: 12px; pointer-events: none;"></i>
                            </button>

                            <!-- Custom Dropdown Menu: Forced strictly downwards with top: 100%, 100% width within red lines -->
                            <div id="userDropdownMenu" class="custom-dropdown-panel shadow" style="display: none; position: absolute; top: 100%; left: 0; right: 0; width: 100%; max-width: 100%; margin-top: 4px; box-sizing: border-box; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 1080; overflow: hidden;">
                                <div id="userDropdownItems" style="max-height: 200px; overflow-y: auto; overflow-x: hidden; padding: 4px 0;">
                                    <div class="px-3 py-2 text-muted small fst-italic">Select a category above first</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-text" id="user_count_hint">Select Students or Teachers above to populate user accounts.</div>
                    </div>

                    <!-- Step 4: Select Target Role (Opens strictly DOWNWARDS, bounded 100% between red lines) -->
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold">
                            <i class="bi bi-shield-check text-primary me-1"></i> Select Target Role <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" name="role_id" id="quick_role_select" value="" required>

                        <div class="custom-modal-select" style="position: relative; width: 100%;">
                            <button type="button" class="form-select text-start d-flex justify-content-between align-items-center w-100" id="roleSelectBtn" onclick="toggleDropdownMenu('roleDropdownMenu')" style="background-color: #fff; cursor: pointer; min-height: 42px; width: 100% !important; max-width: 100% !important; box-sizing: border-box !important;">
                                <span id="roleSelectLabel" class="text-muted text-truncate" style="max-width: calc(100% - 24px);">-- Choose Role --</span>
                                <i class="bi bi-chevron-down text-muted" style="font-size: 12px; pointer-events: none;"></i>
                            </button>

                            <!-- Custom Dropdown Menu: Forced strictly downwards with top: 100%, 100% width within red lines -->
                            <div id="roleDropdownMenu" class="custom-dropdown-panel shadow" style="display: none; position: absolute; top: 100%; left: 0; right: 0; width: 100%; max-width: 100%; margin-top: 4px; box-sizing: border-box; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 1080; overflow: hidden;">
                                <div style="max-height: 200px; overflow-y: auto; overflow-x: hidden; padding: 4px 0;">
                                    @foreach($roles as $r)
                                        @if(!in_array($r->slug, ['admin']))
                                            <div class="custom-role-option px-3 py-2" onclick="selectRoleItem('{{ $r->id }}', '{{ addslashes($r->name) }}', '{{ $r->slug }}')" style="cursor: pointer; border-bottom: 1px solid #f8fafc; transition: background 0.15s ease;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                                <div class="d-flex justify-content-between align-items-center" style="gap: 8px; width: 100%; overflow: hidden;">
                                                    <strong class="text-dark text-truncate" style="font-size: 13px;">{{ $r->name }}</strong>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10.5px; flex-shrink: 0;">{{ $r->slug }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0 border-0">
                        <i class="bi bi-info-circle-fill me-1"></i> Updating a user's role grants them all permissions assigned to that target role immediately.
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary fw-bold px-4">
                        <i class="bi bi-check-circle me-1"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: ASSIGN CLASS INCHARGE --}}
<div class="modal fade admin-modal" id="assignInchargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('roles.assign-incharge') }}" method="POST">
                @csrf
                <div class="modal-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold fs-6 text-dark mb-0">
                        <i class="bi bi-person-workspace text-success me-2"></i> Assign Class Incharge Teacher
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Teacher <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->teacher_name }} ({{ $t->teacher_email ?: 'No Email' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Class & Section <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->class_name }} {{ $c->section_name ? '(Section ' . $c->section_name . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-success py-2 px-3 small mb-0 border-0">
                        <i class="bi bi-info-circle-fill me-1"></i> The assigned teacher will manage attendance, leaves, and marks for students in this class.
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-success fw-bold px-4">
                        <i class="bi bi-check-circle me-1"></i> Assign Class Incharge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 3: CREATE NEW ROLE --}}
<div class="modal fade admin-modal" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold fs-6 text-dark mb-0">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Create New Custom System Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Accountant, Exam Incharge" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Brief summary of duties">
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-check2-square text-primary me-1"></i> Assign Initial Permissions
                    </h6>
                    <div class="row g-3" style="max-height: 380px; overflow-y: auto;">
                        @foreach($permissionGroups ?? [] as $module => $perms)
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-primary text-uppercase fs-7">{{ ucfirst($module) }} Module</strong>
                                        <span class="badge bg-secondary-subtle text-dark" style="font-size: 10px;">{{ count($perms) }} perms</span>
                                    </div>
                                    @foreach($perms as $perm)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="new_perm_{{ $perm->id }}">
                                            <label class="form-check-label fs-7" for="new_perm_{{ $perm->id }}">
                                                {{ $perm->label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary fw-bold px-4">
                        <i class="bi bi-plus-lg me-1"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const systemUsersData = [
@foreach($allUsersList ?? [] as $u)
@if(!$u->isAdmin())
@php
    $isStudent = (bool) $u->studentRecord || ($u->role?->slug === 'student');
    $isTeacher = (bool) $u->teacherRecord || in_array($u->role?->slug, ['teacher', 'class-incharge']);
    $userType = $isStudent ? 'student' : ($isTeacher ? 'teacher' : 'other');

    $uClass = $u->studentRecord?->class ?? '';
    $uSec = $u->studentRecord?->section ?? '';

    $tClasses = [];
    $tSecs = [];
    if ($u->teacherRecord) {
        $tClasses = $u->teacherRecord->assignments->pluck('class_name')->filter()->unique()->values()->all();
        $tSecs = $u->teacherRecord->assignments->pluck('section_name')->filter()->unique()->values()->all();
    }
@endphp
    {
        id: {{ $u->id }},
        name: @json($u->name ?: $u->username),
        email: @json($u->email),
        role: @json($u->role?->name ?? 'No Role'),
        type: @json($userType),
        stdClass: @json($uClass),
        stdSec: @json($uSec),
        tClasses: @json($tClasses),
        tSecs: @json($tSecs)
    },
@endif
@endforeach
];

function toggleDropdownMenu(menuId) {
    const target = document.getElementById(menuId);
    document.querySelectorAll('.custom-dropdown-panel').forEach(p => {
        if (p.id !== menuId) p.style.display = 'none';
    });
    if (target) {
        target.style.display = (target.style.display === 'block') ? 'none' : 'block';
    }
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.custom-modal-select')) {
        document.querySelectorAll('.custom-dropdown-panel').forEach(p => p.style.display = 'none');
    }
});

function selectUserItem(id, name, detail) {
    const input = document.getElementById('quick_user_select');
    const label = document.getElementById('userSelectLabel');
    const menu = document.getElementById('userDropdownMenu');
    const form = document.getElementById('assignUserRoleForm');

    if (input) input.value = id;
    if (label) {
        label.innerHTML = '<strong class="text-dark">' + name + '</strong> <span class="text-muted small">(' + detail + ')</span>';
        label.classList.remove('text-muted');
    }
    if (menu) menu.style.display = 'none';
    if (form && id) {
        form.action = '/roles/users/' + id;
    }
}

function selectRoleItem(id, name, slug) {
    const input = document.getElementById('quick_role_select');
    const label = document.getElementById('roleSelectLabel');
    const menu = document.getElementById('roleDropdownMenu');

    if (input) input.value = id;
    if (label) {
        label.innerHTML = '<strong class="text-dark">' + name + '</strong> <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 10px;">' + slug + '</span>';
        label.classList.remove('text-muted');
    }
    if (menu) menu.style.display = 'none';
}

function onCategoryChanged(cat) {
    const filterRow = document.getElementById('class_section_filter_row');
    const classSelect = document.getElementById('filter_class_select');
    const sectionSelect = document.getElementById('filter_section_select');
    
    if (classSelect) classSelect.value = '';
    if (sectionSelect) sectionSelect.value = '';

    if (filterRow) {
        filterRow.style.display = cat ? 'flex' : 'none';
    }

    filterUserList();
}

function filterUserList() {
    const cat = document.getElementById('user_category_select')?.value || '';
    const selectedClass = (document.getElementById('filter_class_select')?.value || '').toLowerCase().trim();
    const selectedSec = (document.getElementById('filter_section_select')?.value || '').toLowerCase().trim();
    const userLabel = document.getElementById('userSelectLabel');
    const userInput = document.getElementById('quick_user_select');
    const userItemsList = document.getElementById('userDropdownItems');
    const hint = document.getElementById('user_count_hint');
    const form = document.getElementById('assignUserRoleForm');

    if (userInput) userInput.value = '';
    if (form) form.action = '';
    if (userLabel) {
        userLabel.innerHTML = cat ? `-- Choose ${cat === 'student' ? 'Student' : 'Teacher'} --` : '-- Select Category First --';
        userLabel.classList.add('text-muted');
    }

    if (!userItemsList) return;
    userItemsList.innerHTML = '';

    if (!cat) {
        userItemsList.innerHTML = '<div class="px-3 py-2 text-muted small fst-italic">Select a category above first</div>';
        if (hint) hint.textContent = 'Select Students or Teachers above to populate user accounts.';
        return;
    }

    const filtered = systemUsersData.filter(u => {
        if (u.type !== cat) return false;

        if (cat === 'student') {
            if (selectedClass && u.stdClass.toLowerCase().trim() !== selectedClass) return false;
            if (selectedSec && u.stdSec.toLowerCase().trim() !== selectedSec) return false;
            return true;
        }

        if (cat === 'teacher') {
            if (selectedClass) {
                const hasClass = u.tClasses.some(c => c.toLowerCase().trim() === selectedClass);
                if (!hasClass) return false;
            }
            if (selectedSec) {
                const hasSec = u.tSecs.some(s => s.toLowerCase().trim() === selectedSec);
                if (!hasSec) return false;
            }
            return true;
        }

        return true;
    });

    if (filtered.length === 0) {
        userItemsList.innerHTML = '<div class="px-3 py-2 text-muted small fst-italic">No accounts found matching this filter.</div>';
        if (hint) hint.textContent = `No ${cat === 'student' ? 'student' : 'teacher'} accounts found in this class/section.`;
        return;
    }

    filtered.forEach(u => {
        const item = document.createElement('div');
        item.className = 'custom-user-option px-3 py-2';
        item.style.cursor = 'pointer';
        item.style.borderBottom = '1px solid #f8fafc';
        item.style.transition = 'background 0.15s ease';
        item.onmouseover = () => item.style.background = '#f1f5f9';
        item.onmouseout = () => item.style.background = 'transparent';

        const detail = (cat === 'student') 
            ? (u.stdClass ? u.stdClass + (u.stdSec ? ' (' + u.stdSec + ')' : '') : 'General')
            : (u.tClasses.length ? u.tClasses.join(', ') : u.role);

        item.onclick = () => selectUserItem(u.id, u.name, detail);

        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-center" style="gap: 8px; width: 100%; overflow: hidden;">
                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                    <strong class="d-block text-dark text-truncate" style="font-size: 13px;">${u.name}</strong>
                    <small class="text-muted text-truncate d-block" style="font-size: 11.5px;">${detail}</small>
                </div>
                <span class="badge bg-secondary-subtle text-dark border" style="font-size: 10px; flex-shrink: 0;">${u.role}</span>
            </div>
        `;
        userItemsList.appendChild(item);
    });

    if (hint) {
        hint.textContent = `Showing ${filtered.length} ${cat === 'student' ? 'student' : 'teacher'} account(s) matching your filter.`;
    }
}

function validateAssignUserRole(form) {
    const userSelect = document.getElementById('quick_user_select');
    if (!userSelect || !userSelect.value) {
        alert('Please select a user account first.');
        return false;
    }
    const roleSelect = document.getElementById('quick_role_select');
    if (!roleSelect || !roleSelect.value) {
        alert('Please choose a target role first.');
        return false;
    }
    form.action = '/roles/users/' + userSelect.value;
    return true;
}
</script>
