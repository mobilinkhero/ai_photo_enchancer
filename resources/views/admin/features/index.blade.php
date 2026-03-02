@extends('admin.layouts.admin')

@section('title', 'App Features')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i> App Features
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1>App Features</h1>
            <p>Control which AI tools appear in the Flutter app and their settings.</p>
        </div>
        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-white btn-sm">
            <i class="ri-terminal-box-line"></i> API Logs
        </a>
    </div>

    {{-- Live API preview --}}
    <div class="card mb-3" style="border-left: 4px solid var(--indigo-500);">
        <div class="card-body" style="padding:14px 20px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <div
                style="width:34px;height:34px;background:var(--indigo-50);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ri-broadcast-line" style="color:var(--indigo-500);font-size:17px;"></i>
            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--gray-900);">Live Config Endpoint</div>
                <div class="text-xs text-muted">Flutter app fetches this on every launch</div>
            </div>
            <code style="
                background:var(--gray-50);border:1px solid var(--gray-200);border-radius:6px;
                padding:7px 14px;font-size:12.5px;color:var(--indigo-600);
                display:inline-flex;align-items:center;gap:8px;
            ">
                <span class="badge badge-emerald" style="font-size:10px;">GET</span>
                {{ url('/api/config') }}
            </code>
            <a href="{{ url('/api/config') }}" target="_blank" class="btn btn-white btn-sm" style="margin-left:auto;">
                <i class="ri-external-link-line"></i> Test in Browser
            </a>
        </div>
    </div>

    {{-- Feature cards --}}
    @forelse($features as $feature)
        <div class="card mb-3">
            <div class="card-header">
                <div class="card-title">
                    <span style="
                        width:30px;height:30px;border-radius:8px;background:var(--indigo-50);
                        display:inline-flex;align-items:center;justify-content:center;
                        font-size:15px;color:var(--indigo-500);flex-shrink:0;
                    "><i class="ri-magic-line"></i></span>
                    {{ $feature->title }}
                    <code style="font-size:11px;color:var(--gray-400);font-weight:400;">({{ $feature->feature_id }})</code>
                </div>
                <div class="flex gap-2">
                    {{-- Quick toggle --}}
                    <form action="{{ route('admin.features.toggle', $feature) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ $feature->enabled ? 'btn-success' : 'btn-white' }} btn-sm">
                            @if($feature->enabled)
                                <i class="ri-eye-line"></i> Visible
                            @else
                                <i class="ri-eye-off-line"></i> Hidden
                            @endif
                        </button>
                    </form>
                    <button type="button" class="btn btn-white btn-sm" onclick="toggleEdit('form-{{ $feature->id }}')">
                        <i class="ri-edit-line"></i> Edit
                    </button>
                </div>
            </div>

            <form id="form-{{ $feature->id }}" action="{{ route('admin.features.update', $feature) }}" method="POST"
                style="display:none;">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="grid-2" style="gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $feature->title }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description <span class="text-muted">(shown in app)</span></label>
                            <input type="text" name="description" class="form-control" value="{{ $feature->description }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Icon <span class="text-muted">(Flutter MaterialIcon name)</span></label>
                            <input type="text" name="icon" class="form-control" value="{{ $feature->icon }}"
                                placeholder="auto_fix_high_rounded">
                            <p class="form-hint">e.g. face_retouching_natural_rounded, high_quality_rounded, palette_rounded</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Color <span class="text-muted">(Flutter Color int)</span></label>
                            <input type="text" name="color" class="form-control" value="{{ $feature->color }}"
                                placeholder="4280391411">
                            <p class="form-hint">Flutter Color(0xFF...) — convert hex to int: 0xFF6200EB = 4280391411</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Credit Cost (coins per use)</label>
                            <input type="number" name="coins" class="form-control" value="{{ $feature->coins }}" min="0"
                                max="100">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sort Order <span class="text-muted">(lower = appears first)</span></label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $feature->sort_order }}"
                                min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Before Image URL <span class="text-muted">(comparison demo)</span></label>
                            <input type="url" name="before_url" class="form-control" value="{{ $feature->before_url }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">After Image URL <span class="text-muted">(comparison demo)</span></label>
                            <input type="url" name="after_url" class="form-control" value="{{ $feature->after_url }}">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Benefits <span class="text-muted">(one per line — shown in unlock
                                    modal)</span></label>
                            <textarea name="benefits" class="form-control" rows="4"
                                placeholder="Works on any photo&#10;Remove noise and blur&#10;AI-powered correction">{{ implode("\n", $feature->benefits ?? []) }}</textarea>
                        </div>
                    </div>

                    <div style="display:flex;gap:16px;margin-top:4px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13.5px;">
                            <input type="hidden" name="is_premium" value="0">
                            <input type="checkbox" name="is_premium" value="1" {{ $feature->is_premium ? 'checked' : '' }}
                                style="width:16px;height:16px;accent-color:var(--amber-500);">
                            <span class="font-semibold">PRO Only</span>
                            <span class="text-muted text-xs">(shows lock + requires subscription)</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13.5px;">
                            <input type="hidden" name="enabled" value="0">
                            <input type="checkbox" name="enabled" value="1" {{ $feature->enabled ? 'checked' : '' }}
                                style="width:16px;height:16px;accent-color:var(--indigo-500);">
                            <span class="font-semibold">Visible in App</span>
                        </label>
                    </div>
                </div>

                <div
                    style="padding:14px 20px;border-top:1px solid var(--gray-100);display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="btn btn-white btn-sm"
                        onclick="toggleEdit('form-{{ $feature->id }}')">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ri-save-line"></i> Save Feature</button>
                </div>
            </form>

            {{-- View-only summary (shown when form is collapsed) --}}
            <div id="summary-{{ $feature->id }}" class="card-body" style="padding:14px 20px;">
                <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:center;">
                    <div>
                        <span class="text-xs text-muted">Description</span>
                        <div style="font-size:13px;color:var(--gray-700);">{{ $feature->description ?: '—' }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-muted">Cost</span>
                        <div style="font-size:13px;font-weight:700;color:var(--indigo-600);">{{ $feature->coins }}
                            coin{{ $feature->coins != 1 ? 's' : '' }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-muted">Access</span>
                        <div>
                            @if($feature->is_premium)
                                <span class="badge badge-amber"><i class="ri-vip-crown-line"></i> PRO Only</span>
                            @else
                                <span class="badge badge-emerald">Free</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-muted">Status</span>
                        <div>
                            @if($feature->enabled)
                                <span class="badge badge-emerald"><i class="ri-eye-line"></i> Visible</span>
                            @else
                                <span class="badge badge-gray"><i class="ri-eye-off-line"></i> Hidden</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-muted">Sort</span>
                        <div style="font-size:13px;color:var(--gray-600);">#{{ $feature->sort_order }}</div>
                    </div>
                    @if($feature->benefits && count($feature->benefits) > 0)
                        <div style="margin-left:auto;">
                            <span class="text-xs text-muted">{{ count($feature->benefits) }} benefits</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <i class="ri-magic-line"></i>
                    <h3>No features yet</h3>
                    <p>Run <code>php artisan db:seed --class=AppFeaturesSeeder</code> to seed the default features.</p>
                </div>
            </div>
        </div>
    @endforelse

@endsection

@push('scripts')
    <script>
        function toggleEdit(formId) {
            const form = document.getElementById(formId);
            const id = formId.replace('form-', '');
            const summary = document.getElementById('summary-' + id);
            const isHidden = form.style.display === 'none';
            form.style.display = isHidden ? 'block' : 'none';
            summary.style.display = isHidden ? 'none' : 'block';
        }
    </script>
@endpush