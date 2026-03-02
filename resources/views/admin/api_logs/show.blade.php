@extends('admin.layouts.admin')

@section('title', 'Log Detail')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i>
    <a href="{{ route('admin.api-logs.index') }}">API Logs</a>
    <i class="ri-arrow-right-s-line"></i> Detail
@endsection

@section('content')

{{-- Header --}}
<div class="page-header">
    <div>
        <h1>Log Detail</h1>
        <p style="font-family:monospace;font-size:11.5px;color:var(--gray-400);">{{ $apiLog->request_id }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-white btn-sm">
            <i class="ri-arrow-left-line"></i> Back
        </a>
        <form action="{{ route('admin.api-logs.destroy', $apiLog) }}" method="POST" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"
                onclick="return confirm('Delete this log?')">
                <i class="ri-delete-bin-line"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- Summary Bar --}}
<div class="card mb-3">
    <div class="card-body" style="padding: 16px 20px;">
        <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:center;">

            <div>
                @if($apiLog->status === 'success')
                    <span class="badge badge-emerald" style="font-size:12.5px;padding:4px 12px;">
                        <i class="ri-checkbox-circle-fill"></i> Success
                    </span>
                @elseif($apiLog->status === 'error')
                    <span class="badge badge-rose" style="font-size:12.5px;padding:4px 12px;">
                        <i class="ri-close-circle-fill"></i> Error
                    </span>
                @elseif($apiLog->status === 'timeout')
                    <span class="badge badge-amber" style="font-size:12.5px;padding:4px 12px;">
                        <i class="ri-time-fill"></i> Timeout
                    </span>
                @else
                    <span class="badge badge-gray" style="font-size:12.5px;padding:4px 12px;">Pending</span>
                @endif
            </div>

            @php
                $summaryItems = [
                    ['label' => 'Provider',      'value' => ucfirst($apiLog->ai_provider ?? '—')],
                    ['label' => 'HTTP Status',   'value' => $apiLog->ai_response_status ?? '—',
                     'color' => $apiLog->ai_response_status && $apiLog->ai_response_status < 300 ? 'var(--emerald-600)' : 'var(--rose-600)'],
                    ['label' => 'AI Time',       'value' => $apiLog->ai_response_time_ms
                        ? ($apiLog->ai_response_time_ms >= 1000
                            ? round($apiLog->ai_response_time_ms/1000,2).'s'
                            : round($apiLog->ai_response_time_ms).'ms')
                        : '—'],
                    ['label' => 'Total Time',    'value' => $apiLog->total_time_ms
                        ? ($apiLog->total_time_ms >= 1000
                            ? round($apiLog->total_time_ms/1000,2).'s'
                            : round($apiLog->total_time_ms).'ms')
                        : '—'],
                    ['label' => 'Logged',         'value' => $apiLog->created_at->format('M d, Y · H:i:s')],
                ];
            @endphp

            @foreach($summaryItems as $item)
            <div style="border-left:1px solid var(--gray-200);padding-left:20px;">
                <div class="text-xs text-muted">{{ $item['label'] }}</div>
                <div style="font-size:13.5px;font-weight:700;font-family:monospace;color:{{ $item['color'] ?? 'var(--gray-900)' }};">
                    {{ $item['value'] }}
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>

@if($apiLog->error_message)
<div class="alert alert-danger mb-3">
    <i class="ri-error-warning-line"></i>
    <span><strong>Error:</strong> {{ $apiLog->error_message }}</span>
</div>
@endif

{{-- ══════════════════════════════════════════
     SECTION 1 — INCOMING REQUEST (from app)
══════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:10px;margin:20px 0 10px;">
    <div style="
        width:28px;height:28px;border-radius:7px;flex-shrink:0;
        background:var(--indigo-100);color:var(--indigo-600);
        display:flex;align-items:center;justify-content:center;font-size:15px;
    "><i class="ri-arrow-down-circle-line"></i></div>
    <div>
        <span style="font-size:14px;font-weight:700;color:var(--gray-900);">① Incoming Request</span>
        <span class="text-xs text-muted" style="margin-left:8px;">From Flutter App → Laravel</span>
    </div>
</div>

<div class="grid-2 mb-3" style="gap:14px;">
    {{-- Client info --}}
    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header">
            <h3><i class="ri-smartphone-line" style="color:var(--indigo-500);"></i> Client Details</h3>
        </div>
        <div class="section-card-body" style="padding:16px 18px;">
            <table style="width:100%;border-collapse:collapse;">
                @foreach([
                    ['IP Address',  $apiLog->client_ip ?? '—'],
                    ['Method',      $apiLog->client_method ?? '—'],
                    ['Endpoint',    '/'.$apiLog->client_endpoint],
                    ['Req. Size',   $apiLog->ai_request_size_bytes ? number_format($apiLog->ai_request_size_bytes).' bytes' : '—'],
                    ['User UID',    $apiLog->user_uid ?: '—'],
                ] as [$label, $val])
                <tr>
                    <td style="padding:7px 0;font-size:12px;color:var(--gray-400);width:100px;vertical-align:top;">{{ $label }}</td>
                    <td style="padding:7px 0;font-size:12.5px;font-family:monospace;color:var(--gray-700);word-break:break-all;">{{ $val }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    {{-- Request payload --}}
    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <h3><i class="ri-code-s-slash-line" style="color:var(--indigo-500);"></i> Request Payload</h3>
            <button class="btn btn-white btn-xs" onclick="copyPre('client-payload')">
                <i class="ri-clipboard-line"></i> Copy
            </button>
        </div>
        <pre id="client-payload" class="json-pre">{{ json_encode($apiLog->client_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
</div>

{{-- Request Headers (collapsible) --}}
<div class="section-card mb-3">
    <div class="section-card-header" style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;" onclick="toggle('headers-block')">
        <h3><i class="ri-file-list-line" style="color:var(--indigo-500);"></i> Request Headers</h3>
        <span class="text-xs text-muted"><i class="ri-arrow-down-s-line" id="headers-icon"></i> Click to expand</span>
    </div>
    <div id="headers-block" style="display:none;">
        <pre class="json-pre">{{ json_encode($apiLog->client_headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
</div>


{{-- ══════════════════════════════════════════
     SECTION 2 — SENT TO AI PROVIDER
══════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:10px;margin:20px 0 10px;">
    <div style="
        width:28px;height:28px;border-radius:7px;flex-shrink:0;
        background:var(--amber-100);color:var(--amber-600);
        display:flex;align-items:center;justify-content:center;font-size:15px;
    "><i class="ri-arrow-up-circle-line"></i></div>
    <div>
        <span style="font-size:14px;font-weight:700;color:var(--gray-900);">② Sent to AI Provider</span>
        <span class="text-xs text-muted" style="margin-left:8px;">Laravel → {{ ucfirst($apiLog->ai_provider ?? 'AI') }}</span>
    </div>
</div>

<div class="grid-2 mb-3" style="gap:14px;">
    {{-- Endpoint info --}}
    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header">
            <h3>
                @if($apiLog->ai_provider === 'replicate') <i class="ri-cpu-line" style="color:var(--indigo-500);"></i>
                @elseif($apiLog->ai_provider === 'openai') <i class="ri-openai-line" style="color:var(--emerald-500);"></i>
                @else <i class="ri-google-line" style="color:var(--amber-500);"></i> @endif
                {{ ucfirst($apiLog->ai_provider ?? 'AI') }} Endpoint
            </h3>
        </div>
        <div class="section-card-body" style="padding:16px 18px;">
            <div class="text-xs text-muted mb-1">URL Called</div>
            <code style="
                display:block;
                background:var(--gray-50);border:1px solid var(--gray-200);
                border-radius:6px;padding:10px 12px;
                font-size:11.5px;color:var(--gray-700);
                word-break:break-all;line-height:1.6;
            ">{{ $apiLog->ai_endpoint ?? '—' }}</code>

            @if($apiLog->ai_model)
            <div class="text-xs text-muted mt-2 mb-1">Model</div>
            <code style="
                display:block;
                background:var(--indigo-50);border:1px solid var(--indigo-100);
                border-radius:6px;padding:8px 12px;
                font-size:11px;color:var(--indigo-700);
                word-break:break-all;
            ">{{ $apiLog->ai_model }}</code>
            @endif
        </div>
    </div>

    {{-- AI Request body --}}
    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <h3><i class="ri-code-s-slash-line" style="color:var(--amber-500);"></i> Request Body Sent to AI</h3>
            <button class="btn btn-white btn-xs" onclick="copyPre('ai-request')">
                <i class="ri-clipboard-line"></i> Copy
            </button>
        </div>
        <pre id="ai-request" class="json-pre">{{ json_encode($apiLog->ai_request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
</div>


{{-- ══════════════════════════════════════════
     SECTION 3 — AI RESPONSE
══════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:10px;margin:20px 0 10px;">
    <div style="
        width:28px;height:28px;border-radius:7px;flex-shrink:0;
        background: {{ $apiLog->isSuccess() ? 'var(--emerald-100)' : 'var(--rose-100)' }};
        color: {{ $apiLog->isSuccess() ? 'var(--emerald-600)' : 'var(--rose-600)' }};
        display:flex;align-items:center;justify-content:center;font-size:15px;
    "><i class="ri-arrow-left-circle-line"></i></div>
    <div>
        <span style="font-size:14px;font-weight:700;color:var(--gray-900);">③ AI Response Received</span>
        <span class="text-xs" style="margin-left:8px;font-family:monospace;font-weight:700;
            color: {{ ($apiLog->ai_response_status ?? 0) < 300 ? 'var(--emerald-600)' : 'var(--rose-600)' }};">
            HTTP {{ $apiLog->ai_response_status ?? '?' }}
        </span>
    </div>
</div>

@if($apiLog->ai_output_url)
<div class="section-card mb-3">
    <div class="section-card-header">
        <h3><i class="ri-image-line" style="color:var(--emerald-500);"></i> Output Image URL</h3>
    </div>
    <div class="section-card-body" style="padding:14px 18px;">
        <code style="
            display:block;
            background:var(--emerald-50);border:1px solid var(--emerald-100);
            border-radius:6px;padding:10px 12px;
            font-size:11.5px;color:var(--emerald-700);
            word-break:break-all;
        ">{{ $apiLog->ai_output_url }}</code>
    </div>
</div>
@endif

<div class="grid-2 mb-3" style="gap:14px;">
    {{-- Response body --}}
    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <h3>
                @if($apiLog->isSuccess())
                    <i class="ri-checkbox-circle-line" style="color:var(--emerald-500);"></i> Response Body
                @else
                    <i class="ri-close-circle-line" style="color:var(--rose-500);"></i> Error Response
                @endif
            </h3>
            <button class="btn btn-white btn-xs" onclick="copyPre('ai-response')">
                <i class="ri-clipboard-line"></i> Copy
            </button>
        </div>
        <pre id="ai-response" class="json-pre {{ $apiLog->isSuccess() ? '' : 'json-pre-error' }}">{{ json_encode($apiLog->ai_response_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>

    {{-- Timing --}}
    <div class="section-card" style="margin-bottom:0;">
        <div class="section-card-header">
            <h3><i class="ri-timer-line" style="color:var(--sky-500);"></i> Timing Breakdown</h3>
        </div>
        <div class="section-card-body" style="padding:16px 20px;">
            @php
                $aiMs      = $apiLog->ai_response_time_ms ?? 0;
                $totalMs   = $apiLog->total_time_ms ?? 0;
                $overhead  = max(0, $totalMs - $aiMs);
                $aiPct     = $totalMs > 0 ? round(($aiMs / $totalMs) * 100) : 0;
                $fmtMs     = fn($ms) => $ms >= 1000 ? round($ms/1000,2).'s' : round($ms).'ms';
            @endphp

            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                    <span style="font-size:12.5px;color:var(--gray-600);">AI Processing</span>
                    <span style="font-size:12.5px;font-weight:700;font-family:monospace;color:var(--indigo-600);">{{ $fmtMs($aiMs) }}</span>
                </div>
                <div style="background:var(--gray-100);border-radius:99px;height:7px;overflow:hidden;">
                    <div style="width:{{ $aiPct }}%;background:var(--indigo-500);height:100%;border-radius:99px;transition:.4s;"></div>
                </div>
                <div class="text-xs text-muted mt-1">{{ $aiPct }}% of total time</div>
            </div>

            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                    <span style="font-size:12.5px;color:var(--gray-600);">Server Overhead</span>
                    <span style="font-size:12.5px;font-weight:700;font-family:monospace;color:var(--gray-600);">{{ $fmtMs($overhead) }}</span>
                </div>
                <div style="background:var(--gray-100);border-radius:99px;height:7px;overflow:hidden;">
                    <div style="width:{{ 100-$aiPct }}%;background:var(--gray-300);height:100%;border-radius:99px;transition:.4s;"></div>
                </div>
            </div>

            <div style="border-top:1px solid var(--gray-100);padding-top:14px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;font-weight:700;color:var(--gray-700);">Total End-to-End</span>
                <span style="font-size:16px;font-weight:800;font-family:monospace;color:var(--gray-900);">{{ $fmtMs($totalMs) }}</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.json-pre {
    margin: 0;
    padding: 14px 18px;
    font-size: 12px;
    line-height: 1.75;
    color: var(--gray-700);
    background: var(--gray-25);
    overflow-x: auto;
    max-height: 320px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-word;
    font-family: 'Menlo', 'Consolas', monospace;
}
.json-pre-error {
    background: var(--rose-50);
    color: var(--rose-700);
}
</style>
@endpush

@push('scripts')
<script>
function copyPre(id) {
    const text = document.getElementById(id).innerText;
    navigator.clipboard.writeText(text).then(() => {
        // visual feedback on clicked button
        event.target.closest('button').innerHTML = '<i class="ri-check-line" style="color:var(--emerald-600);"></i> Copied!';
        setTimeout(() => {
            event.target.closest('button').innerHTML = '<i class="ri-clipboard-line"></i> Copy';
        }, 2000);
    });
}

function toggle(id) {
    const el = document.getElementById(id);
    const icon = document.getElementById('headers-icon');
    const open = el.style.display !== 'none';
    el.style.display = open ? 'none' : 'block';
    icon.className = open ? 'ri-arrow-down-s-line' : 'ri-arrow-up-s-line';
}

// Light JSON syntax highlighting
document.querySelectorAll('.json-pre').forEach(pre => {
    if (!pre.innerText.trim().startsWith('{') && !pre.innerText.trim().startsWith('[')) return;
    pre.innerHTML = pre.innerHTML
        .replace(/"([^"]+)"(\s*:)/g, '<span style="color:#4f46e5;font-weight:600;">"$1"</span>$2')
        .replace(/: "([^"]*)"/g, ': <span style="color:#059669;">"$1"</span>')
        .replace(/: (\d+\.?\d*)/g, ': <span style="color:#d97706;">$1</span>')
        .replace(/: (true|false)/g, ': <span style="color:#e11d48;font-weight:600;">$1</span>')
        .replace(/: null/g, ': <span style="color:#9ca3af;">null</span>');
});
</script>
@endpush