@extends('admin.layouts.admin')

@section('title', 'AdMob Settings')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i>
    <a href="{{ route('admin.settings.general') }}">Settings</a>
    <i class="ri-arrow-right-s-line"></i> AdMob
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1>AdMob Settings</h1>
            <p>Configure Google AdMob unit IDs. The app loads these at startup — no app update needed.</p>
        </div>
        <a href="{{ route('admin.settings.ai') }}" class="btn btn-white btn-sm">
            <i class="ri-arrow-left-line"></i> Back to AI Settings
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.ads.update') }}" method="POST">
        @csrf

        <div class="section-card mb-3">
            <div class="section-card-header">
                <h3><i class="ri-advertisement-line" style="color:var(--amber-500);"></i> AdMob Master Switch</h3>
                <p>Disable all ads instantly — no rebuild required.</p>
            </div>
            <div class="section-card-body">
                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>AdMob Enabled</strong>
                        <span>When OFF, all ads are silently skipped in the Flutter app.</span>
                    </div>
                    <label class="toggle">
                        <input type="hidden" name="admob_enabled" value="0">
                        <input type="checkbox" name="admob_enabled" value="1" {{ ($configs->get('admob_enabled')?->value ?? '1') == '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="section-card mb-3">
            <div class="section-card-header">
                <h3><i class="ri-key-line" style="color:var(--indigo-500);"></i> Ad Unit IDs</h3>
                <p>Get these from your <strong>AdMob → Apps → Ad units</strong> console. Leave empty to skip that ad type.
                </p>
            </div>
            <div class="section-card-body">
                @php
                    $adUnits = [
                        ['key' => 'admob_banner_unit_id', 'label' => 'Banner Ad', 'icon' => 'ri-layout-bottom-line', 'hint' => 'ca-app-pub-xxxxx/xxxxxxxx'],
                        ['key' => 'admob_interstitial_unit_id', 'label' => 'Interstitial Ad', 'icon' => 'ri-fullscreen-line', 'hint' => 'ca-app-pub-xxxxx/xxxxxxxx'],
                        ['key' => 'admob_rewarded_unit_id', 'label' => 'Rewarded Ad', 'icon' => 'ri-gift-line', 'hint' => 'ca-app-pub-xxxxx/xxxxxxxx — used for earning coins'],
                        ['key' => 'admob_app_open_unit_id', 'label' => 'App Open Ad', 'icon' => 'ri-door-open-line', 'hint' => 'ca-app-pub-xxxxx/xxxxxxxx'],
                        ['key' => 'admob_native_unit_id', 'label' => 'Native Ad', 'icon' => 'ri-newspaper-line', 'hint' => 'ca-app-pub-xxxxx/xxxxxxxx'],
                    ];
                @endphp

                @foreach($adUnits as $unit)
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex;align-items:center;gap:7px;">
                            <i class="{{ $unit['icon'] }}" style="color:var(--indigo-500);font-size:14px;"></i>
                            {{ $unit['label'] }} Unit ID
                        </label>
                        <input type="text" name="{{ $unit['key'] }}" class="form-control"
                            value="{{ $configs->get($unit['key'])?->value ?? '' }}" placeholder="{{ $unit['hint'] }}"
                            style="font-family:monospace;font-size:13px;">
                        <p class="form-hint">Format: {{ $unit['hint'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Info box --}}
        <div style="
            background:var(--amber-50);border:1px solid var(--amber-100);
            border-radius:var(--radius-lg);padding:16px 20px;
            display:flex;gap:14px;align-items:flex-start;margin-bottom:20px;
        ">
            <i class="ri-information-line" style="color:var(--amber-600);font-size:18px;flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--amber-700);margin-bottom:4px;">How it works</div>
                <ul style="font-size:12.5px;color:var(--amber-700);line-height:1.8;margin:0;padding-left:16px;">
                    <li>The Flutter app fetches <code
                            style="background:var(--amber-100);padding:1px 5px;border-radius:3px;">GET /api/config</code> at
                        every launch</li>
                    <li>Ad unit IDs are delivered as part of the config response</li>
                    <li>If an ID is empty or <em>admob_enabled</em> is OFF → that ad type is silently skipped</li>
                    <li>Changes take effect on next app launch — <strong>no app update needed</strong></li>
                </ul>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Save AdMob Settings
            </button>
        </div>
    </form>

@endsection