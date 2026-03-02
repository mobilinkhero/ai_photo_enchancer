@extends('admin.layouts.admin')

@section('title', 'General Settings')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i> Settings
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1>General Settings</h1>
        <p>Manage global app configuration stored in the database.</p>
    </div>
    <a href="{{ route('admin.settings.ai') }}" class="btn btn-primary btn-sm">
        <i class="ri-robot-2-line"></i> AI Settings
    </a>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf @method('POST')

    @foreach($configs as $group => $groupConfigs)
        <div class="card mb-3">
            <div class="card-header">
                <div class="card-title">
                    @if($group === 'general')  <i class="ri-settings-3-line"></i>
                    @elseif($group === 'ai')   <i class="ri-robot-2-line"></i>
                    @elseif($group === 'billing') <i class="ri-money-dollar-circle-line"></i>
                    @else <i class="ri-database-2-line"></i>
                    @endif
                    {{ ucfirst($group) }} Settings
                </div>
            </div>
            <div class="card-body">
                <div class="grid-2">
                    @foreach($groupConfigs as $config)
                        <div class="form-group">
                            <label class="form-label" for="cfg_{{ $config->key }}">
                                {{ $config->label ?: $config->key }}
                            </label>

                            @if($config->type === 'boolean')
                                <div class="toggle-wrap">
                                    <label class="toggle" id="cfg_{{ $config->key }}">
                                        <input type="hidden" name="{{ $config->key }}" value="0">
                                        <input type="checkbox" name="{{ $config->key }}" value="1"
                                            {{ $config->value ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span style="font-size:13px;color:var(--text-secondary);">
                                        {{ $config->value ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            @else
                                <input
                                    type="{{ $config->type === 'integer' ? 'number' : 'text' }}"
                                    id="cfg_{{ $config->key }}"
                                    name="{{ $config->key }}"
                                    class="form-control"
                                    value="{{ $config->value }}"
                                    placeholder="{{ $config->label ?: $config->key }}"
                                >
                            @endif

                            @if($config->description)
                                <div class="form-hint">{{ $config->description }}</div>
                            @else
                                <div class="form-hint text-xs">Key: <code style="color:var(--primary-light);">{{ $config->key }}</code></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    @if($configs->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <i class="ri-settings-3-line"></i>
                    <h3>No settings found</h3>
                    <p>Run the seeder to populate default settings.</p>
                </div>
            </div>
        </div>
    @endif

    @if($configs->isNotEmpty())
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
            <button type="reset" class="btn btn-ghost">
                <i class="ri-refresh-line"></i> Reset
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Save All Settings
            </button>
        </div>
    @endif
</form>

@endsection
