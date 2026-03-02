@extends('admin.layouts.admin')

@section('title', 'Photos')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i> Photos
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1>Photo Processing</h1>
            <p>Monitor all photo enhancement jobs and their statuses.</p>
        </div>
    </div>

    {{-- Stats mini row --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
        @foreach($statusStats as $stat)
            <div style="
                            display:flex;align-items:center;gap:8px;
                            background:var(--bg-card);border:1px solid var(--border);
                            border-radius:8px;padding:10px 16px;font-size:13px;
                        ">
                @if($stat->status === 'done')
                    <span class="badge badge-success">Done</span>
                @elseif($stat->status === 'failed')
                    <span class="badge badge-danger">Failed</span>
                @elseif($stat->status === 'processing')
                    <span class="badge badge-warning">Processing</span>
                @else
                    <span class="badge badge-muted">{{ ucfirst($stat->status) }}</span>
                @endif
                <span style="font-weight:700;color:var(--text-primary);">{{ number_format($stat->count) }}</span>
            </div>
        @endforeach
        @foreach($providerStats as $stat)
            @if($stat->provider)
                <div style="
                                    display:flex;align-items:center;gap:8px;
                                    background:var(--bg-card);border:1px solid var(--border);
                                    border-radius:8px;padding:10px 16px;font-size:13px;
                                ">
                    <span class="badge badge-primary" style="text-transform:capitalize;">{{ $stat->provider }}</span>
                    <span style="font-weight:700;color:var(--text-primary);">{{ number_format($stat->count) }}</span>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3">
        <div class="card-body" style="padding:16px 22px;">
            <form method="GET" action="{{ route('admin.photos.index') }}" class="filter-bar">
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing
                        </option>
                        <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Provider</label>
                    <select name="provider" class="form-select">
                        <option value="">All Providers</option>
                        <option value="replicate" {{ request('provider') === 'replicate' ? 'selected' : '' }}>Replicate
                        </option>
                        <option value="openai" {{ request('provider') === 'openai' ? 'selected' : '' }}>OpenAI</option>
                        <option value="gemini" {{ request('provider') === 'gemini' ? 'selected' : '' }}>Gemini</option>
                    </select>
                </div>
                <div style="align-self:flex-end;display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ri-filter-3-line"></i> Filter</button>
                    <a href="{{ route('admin.photos.index') }}" class="btn btn-ghost btn-sm"><i
                            class="ri-refresh-line"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ri-image-2-line"></i> Photos ({{ $photos->total() }})</div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User UID</th>
                        <th>Provider</th>
                        <th>Model</th>
                        <th>Status</th>
                        <th>Error</th>
                        <th>Processing</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($photos as $photo)
                        <tr>
                            <td class="text-muted text-xs">{{ $photo->id }}</td>
                            <td>
                                <span class="text-xs text-muted">{{ Str::limit($photo->user_uid ?? '—', 16) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-primary" style="text-transform:capitalize;">
                                    {{ $photo->provider ?: '—' }}
                                </span>
                            </td>
                            <td class="text-xs text-muted truncate" style="max-width:180px;" title="{{ $photo->model }}">
                                {{ $photo->model ? Str::limit($photo->model, 30) : '—' }}
                            </td>
                            <td>
                                @if($photo->status === 'done')
                                    <span class="badge badge-success"><i class="ri-checkbox-circle-line"></i> Done</span>
                                @elseif($photo->status === 'failed')
                                    <span class="badge badge-danger"><i class="ri-close-circle-line"></i> Failed</span>
                                @elseif($photo->status === 'processing')
                                    <span class="badge badge-warning"><i class="ri-loader-4-line"></i> Processing</span>
                                @else
                                    <span class="badge badge-muted"><i class="ri-time-line"></i> Pending</span>
                                @endif
                            </td>
                            <td class="text-xs" style="max-width:160px;">
                                @if($photo->error_message)
                                    <span style="color:var(--danger);" title="{{ $photo->error_message }}">
                                        {{ Str::limit($photo->error_message, 40) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-xs text-muted">
                                {{ $photo->processing_time ? round($photo->processing_time, 2) . 's' : '—' }}
                            </td>
                            <td class="text-xs text-muted">{{ $photo->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    @if($photo->status === 'failed')
                                        <form action="{{ route('admin.photos.retry', $photo) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-xs" title="Retry">
                                                <i class="ri-refresh-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.photos.destroy', $photo) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" title="Delete"
                                            onclick="return confirm('Delete this photo record?')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="ri-image-2-line"></i>
                                    <h3>No photos found</h3>
                                    <p>Adjust your filters or check back later.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($photos->hasPages())
            <div class="pagination">
                @if($photos->onFirstPage())
                    <span class="page-link" style="opacity:0.4"><i class="ri-arrow-left-s-line"></i></span>
                @else
                    <a href="{{ $photos->previousPageUrl() }}" class="page-link"><i class="ri-arrow-left-s-line"></i></a>
                @endif

                @foreach($photos->getUrlRange(max(1, $photos->currentPage() - 2), min($photos->lastPage(), $photos->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page == $photos->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($photos->hasMorePages())
                    <a href="{{ $photos->nextPageUrl() }}" class="page-link"><i class="ri-arrow-right-s-line"></i></a>
                @else
                    <span class="page-link" style="opacity:0.4"><i class="ri-arrow-right-s-line"></i></span>
                @endif

                <span style="margin-left:auto;font-size:12px;color:var(--text-muted);">
                    Showing {{ $photos->firstItem() }}–{{ $photos->lastItem() }} of {{ $photos->total() }}
                </span>
            </div>
        @endif
    </div>

@endsection