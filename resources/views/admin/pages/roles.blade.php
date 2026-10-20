<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-shield-lock-fill text-primary"></i> System Roles, Permissions & Access Management Center
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
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check text-success"></i> Defined System Roles & Permissions</h5>
                    <button type="button" class="admin-btn admin-btn-primary admin-btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="bi bi-plus-lg"></i> Create Custom Role
                    </button>
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Slug Key</th>
                                <th>Description</th>
                                <th>Assigned Users</th>
                                <th>Permissions</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $r)
                                <tr>
                                    <td>
                                        <strong class="text-dark fs-6">{{ $r->name }}</strong>
                                        @if($r->is_system)
                                            <span class="badge bg-info-subtle text-info-emphasis ms-1" style="font-size: 10px;">System Role</span>
                                        @endif
                                    </td>
                                    <td><code>{{ $r->slug }}</code></td>
                                    <td><small class="text-muted">{{ $r->description ?: 'No description' }}</small></td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-dark fw-bold">{{ $r->users_count }} Users</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-bold">{{ $r->permissions->count() }} Permissions</span>
                                    </td>
                                    <td class="text-end">
                                        @if($r->slug === 'admin')
                                            <span class="badge bg-secondary-subtle text-secondary">Protected Master Role</span>
                                        @else
                                            <div class="admin-table-actions">
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-gear text-primary me-2"></i> Assign Role to System User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select User *</label>
                    <select id="quick_user_select" class="form-select" onchange="document.getElementById('assignUserRoleForm').action = '/roles/users/' + this.value;">
                        <option value="">-- Select User --</option>
                        @foreach($allUsers ?? [] as $u)
                            @if(!$u->isAdmin())
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }}) — Current: {{ $u->role?->name ?: 'No Role' }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <form id="assignUserRoleForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Target Role *</label>
                        <select name="role_id" class="form-select" required>
                            <option value="">-- Choose Role --</option>
                            @foreach($roles as $r)
                                @if(!in_array($r->slug, ['admin', 'class-incharge']))
                                    <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->description ?: 'System Role' }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="admin-btn admin-btn-primary">Assign Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: ASSIGN CLASS INCHARGE --}}
<div class="modal fade admin-modal" id="assignInchargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('roles.assign-incharge') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-workspace text-success me-2"></i> Assign Class Incharge Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Teacher *</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->teacher_name }} ({{ $t->teacher_email ?: 'No Email' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Class & Section *</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->class_name }} (Section {{ $c->section_name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-success">Assign Class Incharge</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 3: CREATE NEW ROLE --}}
<div class="modal fade admin-modal" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i> Create New Custom System Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Role Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Accountant, Exam Incharge" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Brief role summary">
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Assign Initial Permissions</h6>
                    <div class="row g-3">
                        @foreach($permissionGroups ?? [] as $module => $perms)
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <strong class="d-block text-primary text-uppercase fs-7 mb-2">{{ ucfirst($module) }} Module</strong>
                                    @foreach($perms as $perm)
                                        <div class="form-check">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
