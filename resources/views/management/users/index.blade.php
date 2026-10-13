@extends('layout')

@section('content')

<div class="container">

    <div class="student-header">

        <h1>Registered Users</h1>

    </div>

    @if(session('issued_credentials'))
        <div class="success auto-hide-message">Temporary sign-in: <strong>{{ session('issued_credentials.email') }}</strong> · <strong>{{ session('issued_credentials.password') }}</strong>. Share privately; password change is required at first sign-in.</div>
    @endif


    <table class="student-table">

        <tr>
            <th>Name</th>
            <th>Image</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>

        @foreach($users as $user)

        <tr>

            <td>{{ $user->name ?: $user->username }}</td>

            <td class="table-image-cell">
                <img
                    src="{{ !$user->image || $user->image === 'dummy.jpeg' ? asset('upload/dummy.jpeg') : asset('storage/'.$user->image) }}"
                    alt="User Image"
                    class="table-avatar"
                >
            </td>

            <td>{{ $user->email }}</td>

            <td>{{ $user->phone }}</td>

            <td class="table-actions">
                @if($currentUser->hasPermission('users.update'))
                    <button type="button" class="icon-action edit-action" data-crud-modal-open="user-modal-{{ $user->id }}" title="Edit user" aria-label="Edit user"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                @endif
                @if($currentUser->hasPermission('users.delete'))
                    @unless($user->isAdmin())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" data-crud-action="delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-action delete-action" title="Delete user" aria-label="Delete user"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                    </form>
                    @endunless
                @endif
            </td>

        </tr>

        @endforeach

    </table>

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
                    <div class="crud-field"><label for="user-email-{{ $user->id }}">Email</label><input id="user-email-{{ $user->id }}" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="name@gmail.com" class="@error('email') input-error @enderror" required>@error('email')<span class="crud-field-error">{{ $message }}</span>@enderror</div>
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