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
                    <th>User ID</th>
                    <th>Name / Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td><span class="badge bg-dark text-light">#{{ $u->id }}</span></td>
                        <td>
                            <strong class="d-block text-dark">{{ $u->name ?: $u->username }}</strong>
                            <small class="text-muted">{{ $u->username }}</small>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge bg-info-subtle text-info-emphasis fw-bold">
                                {{ $u->role?->name ?: 'User' }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $u->created_at ? $u->created_at->format('M d, Y') : '-' }}</small></td>
                        <td class="text-end">
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user account?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
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
