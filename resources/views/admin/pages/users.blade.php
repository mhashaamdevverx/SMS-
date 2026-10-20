<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">
            <i class="bi bi-people-fill text-gold" style="color: #d97706;"></i> System User Accounts Management
        </h3>
        <a href="/users" class="admin-btn admin-btn-outline admin-btn-sm">
            <i class="bi bi-box-arrow-up-right"></i> Open Full User Accounts Page
        </a>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="text-center">User ID</th>
                    <th class="text-center">Name / Username</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Joined Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td class="text-center"><span class="badge bg-dark text-light">#{{ $u->id }}</span></td>
                        <td class="text-center" style="white-space: nowrap;">
                            <strong class="d-block text-dark" style="white-space: nowrap;">{{ $u->name ?: $u->username }}</strong>
                            <small class="text-muted">{{ $u->username }}</small>
                        </td>
                        <td class="text-center">{{ $u->email }}</td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info-emphasis fw-bold">
                                {{ $u->role?->name ?: 'User' }}
                            </span>
                        </td>
                        <td class="text-center"><small class="text-muted">{{ $u->created_at ? $u->created_at->format('M d, Y') : '-' }}</small></td>
                        <td class="text-center">
                            <div class="admin-table-actions justify-content-center">
                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user account?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn-icon danger" title="Delete User Account">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
        <div class="admin-pagination-bar">
            <div class="admin-pagination-info">
                Showing <strong>{{ $users->firstItem() }}</strong> to <strong>{{ $users->lastItem() }}</strong> of <strong>{{ $users->total() }}</strong> user accounts
            </div>
            <div class="admin-pagination-links">
                <ul class="pagination mb-0">
                    @if ($users->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $users->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                    @endif

                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if ($page == $users->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @elseif ($page == 1 || $page == $users->lastPage() || abs($page - $users->currentPage()) <= 2)
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @elseif (abs($page - $users->currentPage()) == 3)
                            <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                        @endif
                    @endforeach

                    @if ($users->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $users->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                    @endif
                </ul>
            </div>
        </div>
    @endif
</div>
