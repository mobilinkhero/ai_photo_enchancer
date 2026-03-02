@extends('admin.layouts.admin')

@section('title', 'User Details')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <i class="ri-arrow-right-s-line"></i>
    {{ $user->name ?: $user->uid }}
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1>User Details</h1>
            <p>UID: <code style="color:var(--primary-light);font-size:12px;">{{ $user->uid }}</code></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">
                <i class="ri-arrow-left-line"></i> Back
            </a>
        </div>
    </div>

    <div class="grid-2 mb-4">
        {{-- Profile Card --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="ri-user-line"></i> Profile</div>
                @if($user->is_banned)
                    <span class="badge badge-danger"><i class="ri-forbid-line"></i> Banned</span>
                @else
                    <span class="badge badge-success"><i class="ri-check-line"></i> Active</span>
                @endif
            </div>
            <div class="card-body">
                <div
                    style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                    <div style="
                        width:60px;height:60px;
                        background:linear-gradient(135deg,var(--primary),var(--accent));
                        border-radius:50%;
                        display:flex;align-items:center;justify-content:center;
                        font-size:24px;font-weight:800;color:white;
                        flex-shrink:0;
                    ">
                        {{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:700;color:var(--text-primary);">
                            {{ $user->name ?: 'Anonymous User' }}</div>
                        <div style="font-size:13px;color:var(--text-muted);">{{ $user->email ?: 'No email' }}</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <div class="text-xs text-muted mb-1">Platform</div>
                        <span class="badge {{ $user->platform === 'ios' ? 'badge-info' : 'badge-primary' }}">
                            <i class="ri-{{ $user->platform === 'ios' ? 'apple' : 'android' }}-line"></i>
                            {{ ucfirst($user->platform) }}
                        </span>
                    </div>
                    <div>
                        <div class="text-xs text-muted mb-1">Joined</div>
                        <div style="font-size:13px;color:var(--text-secondary);">{{ $user->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-muted mb-1">Photos Enhanced</div>
                        <div style="font-size:20px;font-weight:800;color:var(--text-primary);">{{ $user->photos_enhanced }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-muted mb-1">Credits</div>
                        <div style="font-size:20px;font-weight:800;color:var(--accent);">{{ $user->credits }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions Card --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="ri-settings-line"></i> Quick Actions</div>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:16px;">

                {{-- Update Subscription --}}
                <form action="{{ route('admin.users.subscription', $user) }}" method="POST">
                    @csrf
                    <div class="form-label">Subscription Plan</div>
                    <div class="flex gap-2">
                        <select name="subscription" class="form-select">
                            <option value="free" {{ $user->subscription === 'free' ? 'selected' : '' }}>Free</option>
                            <option value="pro" {{ $user->subscription === 'pro' ? 'selected' : '' }}>Pro</option>
                            <option value="premium" {{ $user->subscription === 'premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap;">
                            <i class="ri-check-line"></i> Update
                        </button>
                    </div>
                </form>

                {{-- Update Credits --}}
                <form action="{{ route('admin.users.credits', $user) }}" method="POST">
                    @csrf
                    <div class="form-label">Credits Balance</div>
                    <div class="flex gap-2">
                        <input type="number" name="credits" class="form-control" value="{{ $user->credits }}" min="0">
                        <button type="submit" class="btn btn-success btn-sm" style="white-space:nowrap;">
                            <i class="ri-coin-line"></i> Set
                        </button>
                    </div>
                </form>

                <hr style="border:none;border-top:1px solid var(--border);">

                {{-- Ban/Unban --}}
                <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ $user->is_banned ? 'btn-success' : 'btn-warning' }}"
                        style="width:100%;" onclick="return confirm('{{ $user->is_banned ? 'Unban' : 'Ban' }} this user?')">
                        <i class="ri-{{ $user->is_banned ? 'check' : 'forbid' }}-line"></i>
                        {{ $user->is_banned ? 'Unban User' : 'Ban User' }}
                    </button>
                </form>

                {{-- Delete --}}
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;"
                        onclick="return confirm('Permanently delete this user? This cannot be undone.')">
                        <i class="ri-delete-bin-line"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Photo History --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ri-image-2-line"></i> Photo History ({{ $user->photos->count() }})</div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Provider</th>
                        <th>Model</th>
                        <th>Status</th>
                        <th>Processing Time</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->photos as $photo)
                        <tr>
                            <td class="text-muted text-xs">{{ $photo->id }}</td>
                            <td><span class="badge badge-primary">{{ $photo->provider ?: '—' }}</span></td>
                            <td class="text-xs text-muted truncate" style="max-width:200px;">{{ $photo->model ?: '—' }}</td>
                            <td>
                                @if($photo->status === 'done')
                                    <span class="badge badge-success"><i class="ri-checkbox-circle-line"></i> Done</span>
                                @elseif($photo->status === 'failed')
                                    <span class="badge badge-danger"><i class="ri-close-circle-line"></i> Failed</span>
                                @else
                                    <span class="badge badge-muted">{{ ucfirst($photo->status) }}</span>
                                @endif
                            </td>
                            <td class="text-muted text-xs">
                                {{ $photo->processing_time ? round($photo->processing_time, 2) . 's' : '—' }}</td>
                            <td class="text-xs text-muted">{{ $photo->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="ri-image-2-line"></i>
                                    <h3>No photos yet</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection