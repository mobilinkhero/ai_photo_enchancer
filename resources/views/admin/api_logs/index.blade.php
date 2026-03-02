@extends('admin.layouts.admin')

@section('title', 'API Logs')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i> API Logs
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1>API Logs</h1>
            <p>Full request/response trace for every photo enhancement job.</p>
        </div>
        <form action="{{ route('admin.api-logs.clear') }}" method="POST"
            onsubmit="return confirm('Clear ALL log entries? This cannot be undone.')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="ri-delete-bin-line"></i> Clear All Logs
            </button>
        </form>
    </div>

    {{-- Stats Row --}}
    <div class="grid-4 mb-3" style="gap:12px;">
        <div class="stat-card">
            <div class="stat-icon indigo"><i class="ri-list-check-2"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon emerald"><i class="ri-checkbox-circle-line"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['success']) }}</div>
                <div class="stat-label">Successful</div>
                @if($stats['total'] > 0)
                    <div class="stat-trend trend-up">{{ round(($stats['success'] / $stats['total']) * 100) }}% rate</div>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose"><i class="ri-error-warning-line"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['error'] + $stats['timeout']) }}</div>
                <div class="stat-label">Failed / Timeout</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="ri-time-line"></i></div>
            <div class="stat-content">
                <div class="stat-value">
                    @if($stats['avg_time'] >= 1000)
                        {{ round($stats['avg_time'] / 1000, 1) }}s
                    @else
                        {{ $stats['avg_time'] }}ms
                    @endif
                </div>
                <div class="stat-label">Avg Total Time</div>
                <div class="text-xs text-muted mt-1">
                    AI: @if($stats['avg_ai_time'] >= 1000){{ round($stats['avg_ai_time'] / 1000, 1) }}s @else
                    {{ $stats['avg_ai_time'] }}ms @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.api-logs.index') }}" class="filter-bar">
                <div class="filter-field">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Request ID, user UID, endpoint…"
                        value="{{ request('search') }}" style="min-width:240px;">
                </div>
                <div class="filter-field">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Error</option>
                        <option value="timeout" {{ request('status') === 'timeout' ? 'selected' : '' }}>Timeout</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label class="form-label">Provider</label>
                    <select name="provider" class="form-select">
                        <option value="">All Providers</option>
                        <option value="replicate" {{ request('provider') === 'replicate' ? 'selected' : '' }}>Replicate</option>
                        <option value="openai" {{ request('provider') === 'openai' ? 'selected' : '' }}>OpenAI</option>
                        <option value="gemini" {{ request('provider') === 'gemini' ? 'selected' : '' }}>Gemini</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="filter-field" style="align-self:flex-end;flex-direction:row;gap:6px;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ri-filter-3-line"></i> Filter</button>
                    <a href="{{ route('admin.api-logs.index') }}" class="btn btn-white btn-sm"><i
                            class="ri-refresh-line"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ri-terminal-box-line"></i> Log Entries ({{ $logs->total() }})</div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Request ID</th>
                        <th>Client Endpoint</th>
                        <th>Provider</th>
                        <th>HTTP Status</th>
                        <th>Status</th>
                        <th>AI Time</th>
                        <th>Total</th>
                        <th>User</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                                    <tr style="cursor:pointer;" onclick="window.location='{{ route('admin.api-logs.show', $log) }}'">
                                        <td class="text-xs" style="white-space:nowrap;">
                                            <div style="font-weight:600;color:var(--gray-700);">{{ $log->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>
                                        <td>
                                            <code
                                                style="font-size:11px;color:var(--indigo-600);background:var(--indigo-50);padding:2px 6px;border-radius:4px;">
                                                    {{ Str::limit($log->request_id, 18) }}
                                                </code>
                                        </td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:5px;">
                                                <span class="badge badge-gray" style="font-size:10px;">{{ $log->client_method }}</span>
                                                <span
                                                    style="font-size:12px;color:var(--gray-600);font-family:monospace;">/{{ $log->client_endpoint }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($log->ai_provider === 'replicate')
                                                <span class="badge badge-indigo">Replicate</span>
                                            @elseif($log->ai_provider === 'openai')
                                                <span class="badge badge-emerald">OpenAI</span>
                                            @elseif($log->ai_provider === 'gemini')
                                                <span class="badge badge-amber">Gemini</span>
                                            @else
                                                <span class="badge badge-gray">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $code = $log->ai_response_status; @endphp
                                            <span style="
                                                    font-family:monospace;font-size:12px;font-weight:700;
                                                    color: {{ $code >= 200 && $code < 300 ? 'var(--emerald-600)' : ($code >= 400 ? 'var(--rose-600)' : 'var(--gray-500)') }};
                                                ">{{ $code ?? '—' }}</span>
                                        </td>
                                        <td>
                                            @if($log->status === 'success')
                                                <span class="badge badge-emerald"><i class="ri-check-line"></i> Success</span>
                                            @elseif($log->status === 'error')
                                                <span class="badge badge-rose"><i class="ri-close-line"></i> Error</span>
                                            @elseif($log->status === 'timeout')
                                                <span class="badge badge-amber"><i class="ri-time-line"></i> Timeout</span>
                                            @else
                                                <span class="badge badge-gray">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-xs text-muted" style="font-family:monospace;">
                                            {{ $log->ai_response_time_ms ? (
                            $log->ai_response_time_ms >= 1000
                            ? round($log->ai_response_time_ms / 1000, 1) . 's'
                            : round($log->ai_response_time_ms) . 'ms'
                        ) : '—' }}
                                        </td>
                                        <td class="text-xs text-muted" style="font-family:monospace;">
                                            {{ $log->total_time_ms ? (
                            $log->total_time_ms >= 1000
                            ? round($log->total_time_ms / 1000, 1) . 's'
                            : round($log->total_time_ms) . 'ms'
                        ) : '—' }}
                                        </td>
                                        <td>
                                            <span class="text-xs text-muted truncate" style="max-width:100px;display:inline-block;">
                                                {{ Str::limit($log->user_uid, 12) ?: '—' }}
                                            </span>
                                        </td>
                                        <td onclick="event.stopPropagation()">
                                            <div class="flex gap-1">
                                                <a href="{{ route('admin.api-logs.show', $log) }}" class="btn btn-white btn-xs"
                                                    title="View details">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <form action="{{ route('admin.api-logs.destroy', $log) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Delete this log?')" title="Delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="ri-terminal-box-line"></i>
                                    <h3>No logs yet</h3>
                                    <p>API calls will be logged here once your app starts processing photos.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="pagination">
                @if($logs->onFirstPage())
                    <span class="page-link" style="opacity:0.4"><i class="ri-arrow-left-s-line"></i></span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="page-link"><i class="ri-arrow-left-s-line"></i></a>
                @endif
                @foreach($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page == $logs->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="page-link"><i class="ri-arrow-right-s-line"></i></a>
                @else
                    <span class="page-link" style="opacity:0.4"><i class="ri-arrow-right-s-line"></i></span>
                @endif
                <span style="margin-left:auto;font-size:12px;color:var(--gray-400);">
                    {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
                </span>
            </div>
        @endif
    </div>

@endsection