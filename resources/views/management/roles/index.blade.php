@extends('layout')

@push('page-css')
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="roles-page">
    <header class="roles-page-heading">
        <div>
            <p class="roles-eyebrow">ACCESS CONTROL</p>
            <h1>Roles &amp; Permissions</h1>
            <p>Decide who can access each part of the school system.</p>
        </div>
        <button type="button" class="student-add-btn" data-crud-modal-open="role-create-modal"><i class="bi bi-plus-lg" aria-hidden="true"></i> Create role</button>
    </header>

    @if(session('success'))<div class="success auto-hide-message">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="field-error has-error auto-hide-message">{{ $errors->first() }}</div>@endif
    @if(session('issued_credentials'))
        <div class="roles-credentials-notice">
            <i class="bi bi-key-fill" aria-hidden="true"></i>
            <div><strong>Temporary sign-in details</strong><span>{{ session('issued_credentials.email') }} · {{ session('issued_credentials.password') }}</span><small>Share privately. The user must change this password at first sign-in.</small></div>
        </div>
    @endif

    <section class="roles-section">
        <div class="roles-section-heading">
            <div><span>01</span><h2>Teacher access requests</h2></div>
            <small>{{ $pendingRequests->total() }} awaiting review</small>
        </div>
        <div class="roles-table-wrap">
            <table class="roles-table">
                <thead><tr><th>Teacher</th><th>Contact</th><th>Request date</th><th>Assign class / section</th><th>Decision</th></tr></thead>
                <tbody>
                    @forelse($pendingRequests as $accessRequest)
                        <tr>
                            <td><strong>{{ $accessRequest->name }}</strong><small>{{ $accessRequest->teacher?->course ?? 'Teacher profile' }}</small></td>
                            <td>{{ $accessRequest->email }}<small>{{ $accessRequest->phone }}</small></td>
                            <td>{{ $accessRequest->created_at?->format('M j, Y') }}</td>
                            <td>
                                <form action="{{ route('teacher-access-requests.approve', $accessRequest) }}" method="POST" class="role-approve-form">
                                    @csrf
                                    <select name="class_id" aria-label="Assign class and section" required @disabled($classes->isEmpty())>
                                        <option value="">Choose class / section</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" @disabled(\App\Models\ClassIncharge::where('class_id', $class->id)->exists())>{{ $class->class_name }} / {{ $class->section_name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="role-approve-btn" @disabled($classes->isEmpty())>Approve</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('teacher-access-requests.reject', $accessRequest) }}" method="POST" data-crud-action="delete">
                                    @csrf
                                    <button type="submit" class="icon-action delete-action" title="Reject request" aria-label="Reject request"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="roles-empty">No teacher account requests are waiting for review.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pendingRequests->hasPages())<div class="roles-pagination">{{ $pendingRequests->links('pagination::bootstrap-4') }}</div>@endif
    </section>

    <section class="roles-section">
        <div class="roles-section-heading">
            <div><span>02</span><h2>Assign class incharges</h2></div>
            <small>One teacher per class / section</small>
        </div>
        <div class="roles-table-wrap roles-incharge-wrap">
            <form action="{{ route('roles.assign-incharge') }}" method="POST" class="roles-incharge-form">
                @csrf
                <label for="incharge-teacher">Teacher</label>
                <select id="incharge-teacher" name="teacher_id" required>
                    <option value="">Select a teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }} · {{ $teacher->teacher_email }}</option>
                    @endforeach
                </select>
                <label for="incharge-class">Class / section</label>
                <select id="incharge-class" name="class_id" required>
                    <option value="">Select a class</option>
                    @foreach($classes as $class)
                        @php $assignedIncharge = $inchargeOptions->firstWhere('class_id', $class->id); @endphp
                        <option value="{{ $class->id }}">{{ $class->class_name }} / {{ $class->section_name }}{{ $assignedIncharge ? ' · '.$assignedIncharge->teacher?->teacher_name : '' }}</option>
                    @endforeach
                </select>
                <button type="submit" class="role-approve-btn"><i class="bi bi-person-check-fill" aria-hidden="true"></i> Assign</button>
            </form>
            <table class="roles-table">
                <thead><tr><th>Teacher</th><th>Login email</th><th>Class / section</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($incharges as $incharge)
                        <tr>
                            <td><strong>{{ $incharge->teacher?->teacher_name }}</strong></td>
                            <td>{{ $incharge->user?->email }}</td>
                            <td>{{ $incharge->classRecord?->class_name }} / {{ $incharge->classRecord?->section_name }}</td>
                            <td><form action="{{ route('roles.incharges.destroy', $incharge) }}" method="POST" data-crud-action="delete">@csrf @method('DELETE')<button type="submit" class="icon-action delete-action" title="Remove incharge assignment" aria-label="Remove {{ $incharge->teacher?->teacher_name }} as incharge"><i class="bi bi-person-dash-fill" aria-hidden="true"></i></button></form></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="roles-empty">No class incharges assigned yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incharges->hasPages())<div class="roles-pagination">{{ $incharges->links('pagination::bootstrap-4') }}</div>@endif
    </section>

    <section class="roles-section">
        <div class="roles-section-heading">
            <div><span>03</span><h2>Role library</h2></div>
            <small>{{ $roles->total() }} saved roles</small>
        </div>
        <div class="roles-table-wrap">
            <table class="roles-table">
                <thead><tr><th>Role</th><th>Permissions</th><th>Assigned users</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td><strong>{{ $role->name }}</strong><small>{{ $role->is_system ? 'System role' : $role->slug }}</small></td>
                            <td>{{ $role->permissions->count() }} permissions</td>
                            <td>{{ $role->users_count }} users</td>
                            <td class="roles-actions">
                                <button type="button" class="icon-action view-action" data-crud-modal-open="role-view-{{ $role->id }}" title="View role" aria-label="View {{ $role->name }} role"><i class="bi bi-eye-fill" aria-hidden="true"></i></button>
                                @if($role->slug !== 'admin')
                                    <button type="button" class="icon-action edit-action" data-crud-modal-open="role-edit-{{ $role->id }}" title="Edit role" aria-label="Edit {{ $role->name }} role"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                                    @if(!$role->is_system && $role->users_count === 0)
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" data-crud-action="delete">@csrf @method('DELETE')<button type="submit" class="icon-action delete-action" title="Delete role" aria-label="Delete {{ $role->name }} role"><i class="bi bi-trash3" aria-hidden="true"></i></button></form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($roles->hasPages())<div class="roles-pagination">{{ $roles->links('pagination::bootstrap-4') }}</div>@endif
    </section>

    <section class="roles-section">
        <div class="roles-section-heading">
            <div><span>04</span><h2>Account role assignments</h2></div>
            <small>{{ $users->total() }} accounts</small>
        </div>
        <div class="roles-table-wrap">
            <table class="roles-table">
                <thead><tr><th>Account</th><th>Email</th><th>Current role</th><th>Assignment</th><th>Save</th></tr></thead>
                <tbody>
                    @foreach($users as $account)
                        <tr>
                            <td><strong>{{ $account->name ?: $account->username }}</strong></td>
                            <td>{{ $account->email }}</td>
                            <td>{{ $account->role?->name ?? 'No role' }}</td>
                            <td>{{ $account->classIncharge?->classRecord ? $account->classIncharge->classRecord->class_name.' / '.$account->classIncharge->classRecord->section_name : '—' }}</td>
                            <td>
                                @if(!$account->isAdmin())
                                    <form action="{{ route('roles.users.update', $account) }}" method="POST" class="role-user-form">
                                        @csrf @method('PUT')
                                        <select name="role_id" aria-label="Role for {{ $account->name ?: $account->username }}" required>
                                            @foreach($allRoles->whereNotIn('slug', ['admin', 'class-incharge']) as $role)
                                                <option value="{{ $role->id }}" @selected($account->role_id === $role->id)>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="role-save-btn" title="Save account role" aria-label="Save role for {{ $account->name ?: $account->username }}"><i class="bi bi-check2" aria-hidden="true"></i></button>
                                    </form>
                                @else
                                    <span class="roles-owner-badge">Administrator</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())<div class="roles-pagination">{{ $users->links('pagination::bootstrap-4') }}</div>@endif
    </section>
</div>

<div class="crud-backdrop" id="role-create-modal" hidden>
    <section class="crud-modal roles-permission-modal" role="dialog" aria-modal="true" aria-labelledby="role-create-title">
        <button type="button" class="modal-close" data-crud-modal-close aria-label="Close create role dialog">&times;</button>
        <p class="roles-modal-kicker">NEW ACCESS PROFILE</p>
        <h2 id="role-create-title">Create role</h2>
        <form action="{{ route('roles.store') }}" method="POST" class="crud-form">
            @csrf
            <div class="crud-field"><label for="new-role-name">Role name</label><input id="new-role-name" type="text" name="name" maxlength="80" required></div>
            <div class="crud-field"><label for="new-role-description">Description</label><input id="new-role-description" type="text" name="description" maxlength="255"></div>
            <div class="role-permission-groups">
                @foreach($permissionGroups as $module => $permissions)
                    <fieldset class="role-permission-group"><legend>{{ str_replace('-', ' ', ucfirst($module)) }}</legend>
                        @foreach($permissions as $permission)
                            <label><input type="checkbox" name="permissions[]" value="{{ $permission->id }}"><span>{{ $permission->label }}</span></label>
                        @endforeach
                    </fieldset>
                @endforeach
            </div>
            <button type="submit" class="modal-primary-btn crud-submit">Save role</button>
        </form>
    </section>
</div>

@foreach($roles as $role)
    <div class="crud-backdrop" id="role-view-{{ $role->id }}" hidden>
        <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="role-view-title-{{ $role->id }}">
            <button type="button" class="modal-close" data-crud-modal-close aria-label="Close role details dialog">&times;</button>
            <p class="roles-modal-kicker">ROLE DETAILS</p>
            <h2 id="role-view-title-{{ $role->id }}">{{ $role->name }}</h2>
            <p class="roles-role-description">{{ $role->description ?: 'No description added.' }}</p>
            <div class="roles-detail-permissions">
                @forelse($role->permissions as $permission)
                    <span><i class="bi bi-check2" aria-hidden="true"></i>{{ $permission->label }}</span>
                @empty
                    <span>No permissions assigned.</span>
                @endforelse
            </div>
        </section>
    </div>

    @if($role->slug !== 'admin')
        <div class="crud-backdrop" id="role-edit-{{ $role->id }}" hidden>
            <section class="crud-modal roles-permission-modal" role="dialog" aria-modal="true" aria-labelledby="role-edit-title-{{ $role->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit role dialog">&times;</button>
                <p class="roles-modal-kicker">UPDATE ACCESS PROFILE</p>
                <h2 id="role-edit-title-{{ $role->id }}">Edit {{ $role->name }}</h2>
                <form action="{{ route('roles.update', $role) }}" method="POST" class="crud-form">
                    @csrf @method('PUT')
                    <div class="crud-field"><label for="role-name-{{ $role->id }}">Role name</label><input id="role-name-{{ $role->id }}" type="text" name="name" value="{{ $role->name }}" maxlength="80" required></div>
                    <div class="crud-field"><label for="role-description-{{ $role->id }}">Description</label><input id="role-description-{{ $role->id }}" type="text" name="description" value="{{ $role->description }}" maxlength="255"></div>
                    <div class="role-permission-groups">
                        @foreach($permissionGroups as $module => $permissions)
                            <fieldset class="role-permission-group"><legend>{{ str_replace('-', ' ', ucfirst($module)) }}</legend>
                                @foreach($permissions as $permission)
                                    <label><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked($role->permissions->contains('id', $permission->id))><span>{{ $permission->label }}</span></label>
                                @endforeach
                            </fieldset>
                        @endforeach
                    </div>
                    <button type="submit" class="modal-primary-btn crud-submit">Update role</button>
                </form>
            </section>
        </div>
    @endif
@endforeach
@endsection