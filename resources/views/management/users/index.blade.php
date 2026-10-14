@extends('layout')

@section('content')

<div class="container">

    <div class="student-header flex-between-center-mb24" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div class="flex-center-gap12-wrap" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <h1 class="m-0">Registered Users</h1>
            <span class="total-count-pill">
                <i class="bi bi-people-fill fs-14" aria-hidden="true"></i> Total: {{ $users->total() }}
            </span>
        </div>

        <form method="GET" action="/users" class="d-flex align-items-center gap-2" style="display: flex; align-items: center; gap: 8px;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name, email, phone..." class="form-control form-control-sm" style="width: 250px; max-width: 100%; border-radius: var(--radius-md, 8px);">
            <button type="submit" class="btn btn-sm btn-primary" style="border-radius: var(--radius-md, 8px);"><i class="bi bi-search"></i> Search</button>
            @if(!empty($search))
                <a href="/users" class="btn btn-sm btn-light border" style="border-radius: var(--radius-md, 8px);">Clear</a>
            @endif
        </form>
    </div>

    @if(session('issued_credentials'))
        <div class="success auto-hide-message">Temporary sign-in: <strong>{{ session('issued_credentials.email') }}</strong> · <strong>{{ session('issued_credentials.password') }}</strong>. Share privately; password change is required at first sign-in.</div>
    @endif

    <div class="unified-table-card">
        <div class="table-responsive-wrapper">
            <table class="student-table unified-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Image</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name ?: $user->username }}</strong></td>
                        <td>
                            <span class="badge {{ $user->isAdmin() ? 'bg-danger' : ($user->role?->slug === 'class-incharge' ? 'bg-primary' : ($user->role?->slug === 'teacher' ? 'bg-success' : 'bg-info')) }} px-2 py-1">
                                {{ $user->role?->name ?? 'User' }}
                            </span>
                        </td>
                        <td class="table-image-cell">
                            <img
                                src="{{ !$user->image || $user->image === 'dummy.jpeg' ? asset('upload/dummy.jpeg') : asset('storage/'.$user->image) }}"
                                alt="User Image"
                                class="table-avatar"
                            >
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?: '—' }}</td>
                        <td class="table-actions text-center">
                            @if($currentUser->hasPermission('users.update'))
                                <button type="button" class="icon-action edit-action" data-crud-modal-open="user-modal-{{ $user->id }}" title="Edit user" aria-label="Edit user"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                            @endif
                            @if($currentUser->hasPermission('users.delete'))
                                @unless($user->isAdmin())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" data-crud-action="delete" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-action delete-action" title="Delete user" aria-label="Delete user"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                                </form>
                                @endunless
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($users->total() > 0)
        <div class="flex-between-center-mt18-wrap" style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; flex-wrap: wrap; gap: 12px;">
            <p class="student-pagination-info m-0" style="color: #64748b; font-size: 13px; font-weight: 600;">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
            </p>

            @if($users->hasPages())
                <div class="student-pagination">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif

    @if($currentUser->hasPermission('users.update'))
    @foreach($users as $user)
        <div class="crud-backdrop" id="user-modal-{{ $user->id }}" data-open="{{ session('edit_modal') === 'user-'.$user->id ? 'true' : 'false' }}" hidden>
            <section class="crud-modal" role="dialog" aria-modal="true" aria-labelledby="user-modal-title-{{ $user->id }}">
                <button type="button" class="modal-close" data-crud-modal-close aria-label="Close edit user dialog">&times;</button>
                <h2 id="user-modal-title-{{ $user->id }}">Edit User</h2>
                <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" data-crud-action="update" class="crud-form">
                    @csrf
                    @method('PUT')
                    <div class="crud-field"><label for="user-name-{{ $user->id }}">Name / Username</label><input id="user-name-{{ $user->id }}" type="text" name="name" value="{{ old('name', $user->name ?: $user->username) }}" class="@error('name') input-error @enderror" required>@error('name')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="user-email-{{ $user->id }}">Email</label><input id="user-email-{{ $user->id }}" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="name@gmail.com" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Must be a valid @gmail.com address (e.g. name@gmail.com)" class="@error('email') input-error @enderror" required>@error('email')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field"><label for="user-phone-{{ $user->id }}">Phone</label><input id="user-phone-{{ $user->id }}" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="@error('phone') input-error @enderror">@error('phone')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <div class="crud-field crud-field-full"><label for="user-image-{{ $user->id }}">Replace Image</label><div class="current-image-row"><img src="{{ !$user->image || $user->image === 'dummy.jpeg' ? asset('upload/dummy.jpeg') : asset('storage/'.$user->image) }}" alt="Current user image"><input id="user-image-{{ $user->id }}" type="file" name="image" accept="image/jpeg,image/png"></div>@error('image')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
                    <button type="submit" class="modal-primary-btn crud-submit">Update User</button>
                </form>
            </section>
        </div>
    @endforeach
    @endif

</div>

@endsection