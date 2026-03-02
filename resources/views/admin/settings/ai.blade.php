@extends('admin.layouts.admin')

@section('title', 'AI Provider')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i>
    <a href="{{ route('admin.settings.general') }}">Settings</a>
    <i class="ri-arrow-right-s-line"></i> AI Provider
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1>AI Provider</h1>
        <p>Choose and configure the AI engine powering photo enhancement.</p>
    </div>
</div>

<div class="settings-tabs">
    <a href="{{ route('admin.settings.general') }}" class="settings-tab">
        <i class="ri-settings-3-line"></i> General
    </a>
    <a href="{{ route('admin.settings.billing') }}" class="settings-tab">
        <i class="ri-money-dollar-circle-line"></i> Billing
    </a>
    <a href="{{ route('admin.settings.ai') }}" class="settings-tab active">
        <i class="ri-robot-2-line"></i> AI Provider
    </a>
</div>

<form action="{{ route('admin.settings.ai.update') }}" method="POST">
    @csrf

    @php $current = $configs->get('ai_provider')?->value ?? 'replicate'; @endphp

    {{-- Provider selector --}}
    <div class="section-card mb-3">
        <div class="section-card-header">
            <h3><i class="ri-cpu-line" style="color:var(--indigo-500);font-size:16px;"></i> Select Active Provider</h3>
            <p>Only one provider processes photos at a time.</p>
        </div>
        <div class="section-card-body">
            <div class="grid-3" style="gap:12px;">

                @foreach([
                    'replicate' => ['icon'=>'ri-cpu-line',     'accent'=>'#4f46e5','bg'=>'#eef2ff','border'=>'#c7d2fe','label'=>'Replicate',     'desc'=>'Real-ESRGAN & Stable Diffusion','note'=>'Best for upscaling & restoration'],
                    'openai'    => ['icon'=>'ri-openai-line',  'accent'=>'#059669','bg'=>'#ecfdf5','border'=>'#a7f3d0','label'=>'OpenAI',         'desc'=>'DALL·E image editing','note'=>'Best for creative enhancements'],
                    'gemini'    => ['icon'=>'ri-google-line',  'accent'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a','label'=>'Google Gemini',  'desc'=>'Gemini Vision AI','note'=>'Best for smart scene analysis'],
                ] as $key => $info)
                <label style="cursor:pointer;">
                    <input type="radio" name="ai_provider" value="{{ $key }}" {{ $current===$key ? 'checked' : '' }} style="display:none;" class="provider-radio" data-key="{{ $key }}">
                    <div class="provider-card {{ $current===$key ? 'selected' : '' }}" data-key="{{ $key }}" style="
                        border: 2px solid {{ $current===$key ? $info['accent'] : $info['border'] }};
                        background: {{ $current===$key ? $info['bg'] : 'var(--white)' }};
                        border-radius: 12px;
                        padding: 18px;
                        transition: all 0.15s;
                    ">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;">
                            <div style="
                                width:44px;height:44px;border-radius:10px;
                                background:{{ $info['accent'] }};
                                display:flex;align-items:center;justify-content:center;
                                font-size:22px;color:white;
                            "><i class="{{ $info['icon'] }}"></i></div>
                            @if($current===$key)
                            <div style="
                                background:{{ $info['accent'] }};color:white;
                                font-size:10px;font-weight:700;letter-spacing:0.4px;
                                padding:3px 8px;border-radius:99px;text-transform:uppercase;
                            ">Active</div>
                            @endif
                        </div>
                        <div style="font-size:15px;font-weight:700;color:var(--gray-900);margin-bottom:3px;">{{ $info['label'] }}</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:4px;">{{ $info['desc'] }}</div>
                        <div style="font-size:11.5px;color:{{ $info['accent'] }};font-weight:500;">{{ $info['note'] }}</div>
                    </div>
                </label>
                @endforeach

            </div>
        </div>
    </div>

    {{-- Replicate --}}
    <div class="section-card mb-3" id="config-replicate" style="{{ $current!=='replicate' ? 'opacity:0.5;' : '' }}">
        <div class="section-card-header">
            <h3>
                <i class="ri-cpu-line" style="color:#4f46e5;font-size:16px;"></i>
                Replicate Configuration
                @if($current==='replicate') <span class="badge badge-indigo ml-2">Active</span> @endif
            </h3>
            <p>API settings for Replicate's model hosting platform. <a href="https://replicate.com/account/api-tokens" target="_blank" style="color:var(--indigo-600);">Get API key ↗</a></p>
        </div>
        <div class="section-card-body">
            <div class="grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">API Key</label>
                    <input type="password" name="replicate_api_key" class="form-control"
                        value="{{ $configs->get('replicate_api_key')?->value }}"
                        placeholder="r8_xxxxxxxxxxxxxxxxxxxx">
                    <div class="form-hint">Starts with <code>r8_</code></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Default Model (Fallback)</label>
                    <select name="replicate_model" class="form-select">
                        <option value="nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b" {{ ($configs->get('replicate_model')?->value) === 'nightmareai/real-esrgan:42fed1c4974146d4d2414e2be2c5277c7fcf05fcc3a73abf41610695738c1d7b' ? 'selected' : '' }}>Real-ESRGAN (General Upscaler)</option>
                        <option value="sczhou/codeformer:7de2ea26c616d5bf2245ad0d5e24f0ff9a6204578a5c876db53142edd9d2cd56" {{ ($configs->get('replicate_model')?->value) === 'sczhou/codeformer:7de2ea26c616d5bf2245ad0d5e24f0ff9a6204578a5c876db53142edd9d2cd56' ? 'selected' : '' }}>CodeFormer (Face Restoration)</option>
                        <option value="tencentarc/gfpgan:9283608cc6b7be6b65a8e44983db012355fde4132009bf99d976b2f0896856a3" {{ ($configs->get('replicate_model')?->value) === 'tencentarc/gfpgan:9283608cc6b7be6b65a8e44983db012355fde4132009bf99d976b2f0896856a3' ? 'selected' : '' }}>GFPGAN (Fast Face Restore)</option>
                        <option value="lucataco/remove-bg:95fcc2a26d3899cd6c2691c900465aaeff466285a65c14638cc5f36f34befaf1" {{ ($configs->get('replicate_model')?->value) === 'lucataco/remove-bg:95fcc2a26d3899cd6c2691c900465aaeff466285a65c14638cc5f36f34befaf1' ? 'selected' : '' }}>lucataco/remove-bg (Background Removal)</option>
                    </select>
                    <div class="form-hint">Models are automatically chosen per-tool, but this is used if an exact match isn't found.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- OpenAI --}}
    <div class="section-card mb-3" id="config-openai" style="{{ $current!=='openai' ? 'opacity:0.5;' : '' }}">
        <div class="section-card-header">
            <h3>
                <i class="ri-openai-line" style="color:#059669;font-size:16px;"></i>
                OpenAI Configuration
                @if($current==='openai') <span class="badge badge-emerald ml-2">Active</span> @endif
            </h3>
            <p>API settings for OpenAI image generation. <a href="https://platform.openai.com/api-keys" target="_blank" style="color:var(--emerald-600);">Get API key ↗</a></p>
        </div>
        <div class="section-card-body">
            <div class="grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">API Key</label>
                    <input type="password" name="openai_api_key" class="form-control"
                        value="{{ $configs->get('openai_api_key')?->value }}"
                        placeholder="sk-xxxxxxxxxxxxxxxxxxxx">
                    <div class="form-hint">Starts with <code>sk-</code></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Model (Image Editing)</label>
                    <select name="openai_model" class="form-select">
                        <option value="dall-e-2" selected>DALL·E 2 (Required for image edits)</option>
                    </select>
                    <div class="form-hint">DALL-E 3 does not support the <code>/v1/images/edits</code> endpoint used for enhancement.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gemini --}}
    <div class="section-card mb-3" id="config-gemini" style="{{ $current!=='gemini' ? 'opacity:0.5;' : '' }}">
        <div class="section-card-header">
            <h3>
                <i class="ri-google-line" style="color:#d97706;font-size:16px;"></i>
                Google Gemini Configuration
                @if($current==='gemini') <span class="badge badge-amber ml-2">Active</span> @endif
            </h3>
            <p>API settings for Google's Gemini Vision AI. <a href="https://makersuite.google.com/app/apikey" target="_blank" style="color:var(--amber-600);">Get API key ↗</a></p>
        </div>
        <div class="section-card-body">
            <div class="grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">API Key</label>
                    <input type="password" name="gemini_api_key" class="form-control"
                        value="{{ $configs->get('gemini_api_key')?->value }}"
                        placeholder="AIzaxxxxxxxxxxxxxxxxxxxx">
                    <div class="form-hint">Starts with <code>AIza</code></div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> Save AI Settings
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.provider-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const selected = this.value;

        // highlight cards
        document.querySelectorAll('.provider-card').forEach(card => {
            const key = card.dataset.key;
            const colors = {
                replicate: { accent: '#4f46e5', bg: '#eef2ff', border: '#c7d2fe' },
                openai:    { accent: '#059669', bg: '#ecfdf5', border: '#a7f3d0' },
                gemini:    { accent: '#d97706', bg: '#fffbeb', border: '#fde68a' },
            };
            if (key === selected) {
                card.style.borderColor = colors[key].accent;
                card.style.background  = colors[key].bg;
            } else {
                card.style.borderColor = colors[key].border;
                card.style.background  = 'white';
            }
        });

        // opacity config sections
        ['replicate','openai','gemini'].forEach(k => {
            const el = document.getElementById('config-' + k);
            if (el) el.style.opacity = k === selected ? '1' : '0.5';
        });
    });
});
</script>
@endpush
