@extends('admin.layouts.admin')

@section('title', 'General Settings')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i>
    <a href="{{ route('admin.settings.general') }}">Settings</a>
    <i class="ri-arrow-right-s-line"></i> General
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1>General Settings</h1>
        <p>Configure core app behavior and metadata.</p>
    </div>
</div>

{{-- Settings Tab Nav --}}
<div class="settings-tabs">
    <a href="{{ route('admin.settings.general') }}" class="settings-tab active">
        <i class="ri-settings-3-line"></i> General
    </a>
    <a href="{{ route('admin.settings.billing') }}" class="settings-tab">
        <i class="ri-money-dollar-circle-line"></i> Billing
    </a>
    <a href="{{ route('admin.settings.ai') }}" class="settings-tab">
        <i class="ri-robot-2-line"></i> AI Provider
    </a>
</div>

<form action="{{ route('admin.settings.general.update') }}" method="POST">
    @csrf

    {{-- App Info --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3><i class="ri-information-line" style="color:var(--indigo-500);font-size:16px;"></i> App Information</h3>
            <p>Basic details about your application.</p>
        </div>
        <div class="section-card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">App Name</label>
                    <input type="text" name="app_name" class="form-control"
                        value="{{ $configs->get('app_name')?->value ?? 'AI Photo Enhancer' }}"
                        placeholder="My App">
                    <div class="form-hint">Displayed in the app and notifications.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">App Version</label>
                    <input type="text" name="app_version" class="form-control"
                        value="{{ $configs->get('app_version')?->value ?? '1.0.0' }}"
                        placeholder="1.0.0">
                </div>
                <div class="form-group">
                    <label class="form-label">Support Email</label>
                    <input type="email" name="support_email" class="form-control"
                        value="{{ $configs->get('support_email')?->value }}"
                        placeholder="support@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Privacy Policy URL</label>
                    <input type="url" name="privacy_policy_url" class="form-control"
                        value="{{ $configs->get('privacy_policy_url')?->value }}"
                        placeholder="https://example.com/privacy">
                </div>
            </div>
        </div>
    </div>

    {{-- Free Tier --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3><i class="ri-gift-line" style="color:var(--emerald-600);font-size:16px;"></i> Free Tier</h3>
            <p>Control what free users can access.</p>
        </div>
        <div class="section-card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Free Credits on Signup</label>
                    <input type="number" name="free_credits" class="form-control"
                        value="{{ $configs->get('free_credits')?->value ?? 5 }}"
                        min="0" max="1000">
                    <div class="form-hint">Credits given to new users upon registration.</div>
                </div>
            </div>

            <div class="toggle-row">
                <div class="toggle-info">
                    <strong>Free Tier Enabled</strong>
                    <span>Allow users to use the app for free (with credit limits).</span>
                </div>
                <label class="toggle">
                    <input type="hidden" name="free_tier_enabled" value="0">
                    <input type="checkbox" name="free_tier_enabled" value="1"
                        {{ ($configs->get('free_tier_enabled')?->value ?? '1') ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <strong>Allow New Registrations</strong>
                    <span>Let new users sign up. Disable to pause onboarding.</span>
                </div>
                <label class="toggle">
                    <input type="hidden" name="allow_registration" value="0">
                    <input type="checkbox" name="allow_registration" value="1"
                        {{ ($configs->get('allow_registration')?->value ?? '1') ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    {{-- Maintenance --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3><i class="ri-tools-line" style="color:var(--amber-600);font-size:16px;"></i> Maintenance</h3>
            <p>Take the app offline for maintenance without deleting data.</p>
        </div>
        <div class="section-card-body">
            <div class="toggle-row">
                <div class="toggle-info">
                    <strong>Maintenance Mode</strong>
                    <span>Show a maintenance message to all app users.</span>
                </div>
                <label class="toggle">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" value="1"
                        {{ ($configs->get('maintenance_mode')?->value) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> Save General Settings
        </button>
    </div>
</form>

@endsection
