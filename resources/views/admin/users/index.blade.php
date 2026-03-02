@extends('admin.layouts.admin')

@section('title', 'App Users')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i> Users
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1>App Users</h1>
            <p>{{ number_format($users->total()) }} registered users across all platforms.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
                <div class="filter-field">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, UID…"
                        value="{{ request('search') }}" style="min-width:200px;">
                </div>
                <div class="filter-field">
                    <label class="form-label">Plan</label>
                    <select name="subscription" class="form-select">
                        <option value="">All Plans</option>
                        <option value="free" {{ request('subscription') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="pro" {{ request('subscription') === 'pro' ? 'selected' : '' }}>Pro</option>
                        <option value="premium" {{ request('subscription') === 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label class="form-label">Platform</label>
                    <select name="platform" class="form-select">
                        <option value="">All Platforms</option>
                        <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android</option>
                        <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                    </select>
                </div>
                <div class="filter-field" style="align-self:flex-end;flex-direction:row;gap:6px;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ri-filter-3-line"></i> Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-white btn-sm" title="Clear">
                        <i class="ri-refresh-line"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Platform</th>
                        <th>Plan</th>
                        <th>Credits</th>
                        <th>Photos</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="
                                            width:32px;height:32px;border-radius:8px;flex-shrink:0;
                                            background: linear-gradient(135deg,#6366f1,#8b5cf6);
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:13px;font-weight:700;color:white;
                                        ">{{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}</div>
                                    <div>
                                        <div class="font-semibold text-dark" style="font-size:13.5px;">
                                            {{ $user->name ?: 'Anonymous' }}</div>
                                        <div class="text-xs text-muted truncate" style="max-width:150px;">
                                            {{ $user->email ?: $user->uid }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->platform === 'ios' ? 'badge-sky' : 'badge-indigo' }}">
                                    {{ ucfirst($user->platform) }}
                                </span>
                            </td>
                            <td>
                                @if($user->subscription === 'premium') <span class="badge badge-amber"><i
                                    class="ri-vip-crown-line"></i> Premium</span>
                                @elseif($user->subscription === 'pro') <span class="badge badge-emerald"><i
                                    class="ri-star-line"></i> Pro</span>
                                @else <span class="badge badge-gray">Free</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight:700;color:var(--indigo-600);">{{ $user->credits }}</span>
                            </td>
                            <td class="text-muted">{{ $user->photos_enhanced }}</td>
                            <td>
                                @if($user->is_banned)
                                    <span class="badge badge-rose"><i class="ri-forbid-line"></i> Banned</span>
                                @else
                                    <span class="badge badge-emerald"><i class="ri-check-line"></i> Active</span>
                                @endif
                            </td>
                            <td class="text-xs text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-white btn-xs" title="View">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn {{ $user->is_banned ? 'btn-success' : 'btn-warning' }} btn-xs"
                                            onclick="return confirm('{{ $user->is_banned ? 'Unban' : 'Ban' }} this user?')"
                                            title="{{ $user->is_banned ? 'Unban' : 'Ban' }}">
                                            <i class="ri-{{ $user->is_banned ? 'check' : 'forbid' }}-line"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs"
                                            onclick="return confirm('Delete this user permanently?')" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="ri-user-3-line"></i>
                                    <h3>No users found</h3>
                                    <p>Try different search or filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="pagination">
                @if($users->onFirstPage())
                    <span class="page-link" style="opacity:0.4"><i class="ri-arrow-left-s-line"></i></span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-link"><i class="ri-arrow-left-s-line"></i></a>
                @endif

                @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page == $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="page-link"><i class="ri-arrow-right-s-line"></i></a>
                @else
                    <span class="page-link" style="opacity:0.4"><i class="ri-arrow-right-s-line"></i></span>
                @endif

                <span style="margin-left:auto;font-size:12px;color:var(--gray-400);">
                    {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
                </span>
            </div>
        @endif
    </div>

@endsection