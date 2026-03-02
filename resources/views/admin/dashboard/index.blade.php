@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')

    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Here's everything happening with your app right now.</p>
        </div>
        <a href="{{ route('admin.settings.ai') }}" class="btn btn-primary btn-sm">
            <i class="ri-robot-2-line"></i> AI Settings
        </a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-icon sky"><i class="ri-image-2-line"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['total_photos']) }}</div>
                <div class="stat-label">Photos Processed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon violet"><i class="ri-calendar-check-line"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['photos_today']) }}</div>
                <div class="stat-label">Photos Today</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose"><i class="ri-error-warning-line"></i></div>
            <div class="stat-content">
                <div class="stat-value">{{ number_format($stats['failed_photos']) }}</div>
                <div class="stat-label">Failed Jobs</div>
                @if($stats['failed_photos'] > 0)
                    <div class="stat-trend trend-down"><i class="ri-arrow-right-line"></i>
                        <a href="{{ route('admin.photos.index', ['status' => 'failed']) }}" style="color:inherit;">Review</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid-2 mb-3">
        {{-- Chart --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="ri-bar-chart-2-line"></i> Photo Activity — Last 7 Days</div>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="190"></canvas>
            </div>
        </div>

        {{-- Provider card --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="ri-robot-2-line"></i> Active AI Provider</div>
                <a href="{{ route('admin.settings.ai') }}" class="btn btn-white btn-sm">
                    <i class="ri-pencil-line"></i> Change
                </a>
            </div>
            <div class="card-body">
                @php
                    $providerMeta = [
                        'replicate' => ['icon' => 'ri-cpu-line', 'color' => '#4f46e5', 'bg' => '#eef2ff', 'label' => 'Replicate', 'sub' => 'Real-ESRGAN upscaling'],
                        'openai' => ['icon' => 'ri-openai-line', 'color' => '#059669', 'bg' => '#ecfdf5', 'label' => 'OpenAI', 'sub' => 'DALL·E image processing'],
                        'gemini' => ['icon' => 'ri-google-line', 'color' => '#d97706', 'bg' => '#fffbeb', 'label' => 'Gemini', 'sub' => 'Google AI enhancement'],
                    ];
                    $pm = $providerMeta[$stats['active_provider']] ?? $providerMeta['replicate'];
                @endphp

                <div style="
                        display:flex; align-items:center; gap:14px;
                        background: {{ $pm['bg'] }}; border-radius:10px;
                        padding:18px; margin-bottom:20px;
                    ">
                    <div style="
                            width:48px;height:48px; border-radius:12px;
                            background:{{ $pm['color'] }}; display:flex;
                            align-items:center;justify-content:center;
                            font-size:24px;color:white;flex-shrink:0;
                        "><i class="{{ $pm['icon'] }}"></i></div>
                    <div>
                        <div style="font-size:17px;font-weight:800;color:var(--gray-900);">{{ $pm['label'] }}</div>
                        <div style="font-size:12px;color:var(--gray-500);">{{ $pm['sub'] }}</div>
                    </div>
                    <span class="badge badge-emerald" style="margin-left:auto;"><i class="ri-check-line"></i> Active</span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
                    <div
                        style="text-align:center;padding:12px;background:var(--gray-50);border-radius:8px;border:1px solid var(--gray-200);">
                        <div style="font-size:22px;font-weight:800;color:var(--gray-900);">
                            {{ number_format($stats['total_photos']) }}
                        </div>
                        <div style="font-size:11px;color:var(--gray-400);text-transform:uppercase;font-weight:600;">Total
                        </div>
                    </div>
                    <div
                        style="text-align:center;padding:12px;background:var(--emerald-50);border-radius:8px;border:1px solid var(--emerald-100);">
                        <div style="font-size:22px;font-weight:800;color:var(--emerald-600);">
                            {{ $stats['total_photos'] > 0 ? round((($stats['total_photos'] - $stats['failed_photos']) / $stats['total_photos']) * 100) : 100 }}%
                        </div>
                        <div style="font-size:11px;color:var(--emerald-600);text-transform:uppercase;font-weight:600;">
                            Success</div>
                    </div>
                    <div
                        style="text-align:center;padding:12px;background:var(--rose-50);border-radius:8px;border:1px solid var(--rose-100);">
                        <div style="font-size:22px;font-weight:800;color:var(--rose-600);">
                            {{ number_format($stats['failed_photos']) }}
                        </div>
                        <div style="font-size:11px;color:var(--rose-600);text-transform:uppercase;font-weight:600;">Failed
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent rows --}}


    {{-- Recent Photos --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ri-image-2-line"></i> Recent Photos</div>
            <a href="{{ route('admin.photos.index') }}" class="btn btn-white btn-sm">
                View all <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Provider</th>
                        <th>Status</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recent_photos'] as $photo)
                        <tr>
                            <td class="text-muted text-xs">#{{ $photo->id }}</td>
                            <td><span class="badge badge-violet"
                                    style="text-transform:capitalize;">{{ $photo->provider ?: '—' }}</span></td>
                            <td>
                                @if($photo->status === 'done') <span class="badge badge-emerald"><i class="ri-check-line"></i>
                                    Done</span>
                                @elseif($photo->status === 'failed') <span class="badge badge-rose"><i
                                        class="ri-close-line"></i>
                                    Failed</span>
                                @elseif($photo->status === 'processing') <span class="badge badge-amber">Processing</span>
                                @else <span class="badge badge-gray">Pending</span>
                                @endif
                            </td>
                            <td class="text-xs text-muted">{{ $photo->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state"><i class="ri-image-2-line"></i>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const chartData = @json($chartData);
        const ctx = document.getElementById('activityChart').getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 190);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.label),
                datasets: [{
                    label: 'Photos',
                    data: chartData.map(d => d.count),
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#6366f1',
                    borderWidth: 2,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#f9fafb',
                        bodyColor: '#9ca3af',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#9ba3b8', font: { size: 11, family: 'Inter' } }
                    },
                    y: {
                        grid: { color: '#f0f1f5', drawBorder: false },
                        border: { display: false },
                        ticks: { color: '#9ba3b8', font: { size: 11, family: 'Inter' }, stepSize: 1 },
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
@endpush