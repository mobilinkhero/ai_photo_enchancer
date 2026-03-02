@extends('admin.layouts.admin')

@section('title', 'AI Test Lab')
@section('breadcrumb')
    <i class="ri-arrow-right-s-line"></i> AI Test Lab
@endsection

@section('content')

    <div class="page-header">
        <div>
            <h1><i class="ri-flask-line" style="color:var(--indigo-500);"></i> AI Test Lab</h1>
            <p>Upload a real photo and test every AI tool live against your configured provider.</p>
        </div>
        <div class="flex gap-2">
            <span class="badge {{ $hasKey ? 'badge-emerald' : 'badge-rose' }}" style="font-size:12.5px;padding:5px 12px;">
                <i class="ri-{{ $hasKey ? 'checkbox-circle' : 'close-circle' }}-line"></i>
                {{ ucfirst($provider) }} — {{ $hasKey ? 'API Key Set' : 'No API Key!' }}
            </span>
            @if(!$hasKey)
                <a href="{{ route('admin.settings.ai') }}" class="btn btn-primary btn-sm">
                    <i class="ri-key-line"></i> Add API Key
                </a>
            @endif
        </div>
    </div>

    @if(!$hasKey)
        <div class="alert alert-danger mb-3">
            <i class="ri-error-warning-line"></i>
            <span><strong>No API key configured for {{ ucfirst($provider) }}.</strong>
                Go to <a href="{{ route('admin.settings.ai') }}" style="color:var(--rose-700);font-weight:700;">Settings → AI
                    Provider</a> and enter your key before testing.</span>
        </div>
    @endif

    <div class="grid-2" style="gap:20px;align-items:start;">

        {{-- ═══ LEFT: Upload + Tool Select ═══ --}}
        <div>

            {{-- Image Upload --}}
            <div class="card mb-3">
                <div class="card-header">
                    <div class="card-title"><i class="ri-image-add-line" style="color:var(--indigo-500);"></i> 1. Upload
                        Test Image</div>
                </div>
                <div class="card-body">
                    <div id="drop-zone" onclick="document.getElementById('file-input').click()" style="
                            border: 2px dashed var(--gray-300);
                            border-radius: 12px;
                            padding: 32px 20px;
                            text-align: center;
                            cursor: pointer;
                            transition: all .2s;
                            background: var(--gray-25);
                            position: relative;
                            overflow: hidden;
                        "
                        ondragover="event.preventDefault();this.style.borderColor='var(--indigo-400)';this.style.background='var(--indigo-50)'"
                        ondragleave="this.style.borderColor='var(--gray-300)';this.style.background='var(--gray-25)'"
                        ondrop="handleDrop(event)">

                        <div id="drop-placeholder">
                            <i class="ri-upload-cloud-2-line"
                                style="font-size:40px;color:var(--gray-300);display:block;margin-bottom:8px;"></i>
                            <div style="font-size:14px;font-weight:600;color:var(--gray-600);">Drop image here or click to
                                browse</div>
                            <div class="text-xs text-muted mt-1">JPG, PNG, WEBP — max 10MB</div>
                        </div>

                        <img id="preview-img" src="" alt=""
                            style="display:none;max-height:220px;border-radius:8px;object-fit:contain;max-width:100%;">

                        <input type="file" id="file-input" accept="image/jpeg,image/png,image/webp" style="display:none;"
                            onchange="handleFileSelect(this)">
                    </div>

                    <div id="file-info" style="display:none;margin-top:10px;" class="text-xs text-muted"></div>

                    <button id="clear-btn" onclick="clearImage()" style="display:none;margin-top:8px;"
                        class="btn btn-white btn-xs">
                        <i class="ri-close-line"></i> Clear
                    </button>
                </div>
            </div>

            {{-- Tool Selector --}}
            <div class="card mb-3">
                <div class="card-header">
                    <div class="card-title"><i class="ri-settings-4-line" style="color:var(--indigo-500);"></i> 2. Select
                        Tool</div>
                </div>
                <div class="card-body" style="padding:14px;">
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                        @foreach($features as $i => $feature)
                            <label id="tool-{{ $feature->feature_id }}" style="
                                    display:flex;align-items:center;gap:10px;
                                    padding:11px 14px;border-radius:9px;cursor:pointer;
                                    border:2px solid {{ $i === 0 ? 'var(--indigo-500)' : 'var(--gray-200)' }};
                                    background:{{ $i === 0 ? 'var(--indigo-50)' : 'var(--white)' }};
                                    transition:all .15s;
                                " onclick="selectTool('{{ $feature->feature_id }}', this)">
                                <input type="radio" name="tool" value="{{ $feature->feature_id }}" style="display:none;" {{ $i === 0 ? 'checked' : '' }}>
                                <div style="
                                    width:30px;height:30px;border-radius:7px;flex-shrink:0;
                                    background:var(--indigo-100);
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:15px;color:var(--indigo-600);
                                "><i class="ri-magic-line"></i></div>
                                <div>
                                    <div style="font-size:12.5px;font-weight:700;color:var(--gray-800);">{{ $feature->title }}
                                    </div>
                                    <div class="text-xs text-muted">{{ $feature->feature_id }}</div>
                                </div>
                                @if($feature->is_premium)
                                    <span class="badge badge-amber" style="margin-left:auto;font-size:9px;">PRO</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Run Button --}}
            <button id="run-btn" onclick="runTest()" class="btn btn-primary w-full"
                style="font-size:15px;padding:13px;border-radius:10px;" {{ !$hasKey ? 'disabled' : '' }}>
                <i class="ri-play-circle-line"></i> Run AI Test
            </button>

        </div>

        {{-- ═══ RIGHT: Result Panel ═══ --}}
        <div>

            {{-- Status / Progress --}}
            <div id="status-card" class="card mb-3" style="display:none;">
                <div class="card-body" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                        <div id="status-spinner" style="
                            width:36px;height:36px;border-radius:50%;flex-shrink:0;
                            border:3px solid var(--indigo-100);
                            border-top-color:var(--indigo-500);
                            animation:spin 0.8s linear infinite;
                        "></div>
                        <div>
                            <div id="status-text" style="font-size:14px;font-weight:700;color:var(--gray-900);">Uploading
                                image...</div>
                            <div id="status-sub" class="text-xs text-muted">Please wait, this may take up to 2 minutes</div>
                        </div>
                    </div>
                    <div id="poll-log" style="
                        background:var(--gray-25);border:1px solid var(--gray-200);
                        border-radius:8px;padding:10px 14px;
                        font-family:monospace;font-size:11.5px;color:var(--gray-600);
                        max-height:140px;overflow-y:auto;line-height:1.9;
                    "></div>
                </div>
            </div>

            {{-- Error --}}
            <div id="error-card" class="alert alert-danger mb-3" style="display:none;">
                <i class="ri-error-warning-line"></i>
                <span id="error-text"></span>
            </div>

            {{-- Result --}}
            <div id="result-card" class="card" style="display:none;">
                <div class="card-header" style="background:var(--emerald-50);border-color:var(--emerald-100);">
                    <div class="card-title" style="color:var(--emerald-700);">
                        <i class="ri-checkbox-circle-fill" style="color:var(--emerald-500);"></i>
                        AI Output
                    </div>
                    <span id="result-timing" class="badge badge-emerald" style="font-size:11px;"></span>
                </div>
                <div class="card-body" style="padding:0;">
                    <img id="result-img" src="" alt="AI Output"
                        style="width:100%;border-radius:0 0 10px 10px;object-fit:contain;max-height:400px;background:var(--gray-50);">
                </div>

                {{-- Timing breakdown --}}
                <div id="timing-block" style="padding:16px 20px;border-top:1px solid var(--gray-100);">
                    <div style="display:flex;flex-wrap:wrap;gap:20px;">
                        <div>
                            <div class="text-xs text-muted">AI Time</div>
                            <div id="t-ai"
                                style="font-size:15px;font-weight:800;font-family:monospace;color:var(--indigo-600);"></div>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Server Overhead</div>
                            <div id="t-overhead"
                                style="font-size:15px;font-weight:800;font-family:monospace;color:var(--gray-500);"></div>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Total</div>
                            <div id="t-total"
                                style="font-size:15px;font-weight:800;font-family:monospace;color:var(--gray-900);"></div>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Provider</div>
                            <div id="t-provider"
                                style="font-size:15px;font-weight:800;font-family:monospace;color:var(--amber-600);"></div>
                        </div>
                        <div>
                            <div class="text-xs text-muted">Tool</div>
                            <div id="t-tool" style="font-size:14px;font-weight:700;color:var(--gray-700);"></div>
                        </div>
                    </div>

                    {{-- Progress bars --}}
                    <div style="margin-top:14px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span class="text-xs" style="color:var(--indigo-600);">AI Processing</span>
                            <span id="ai-pct" class="text-xs" style="color:var(--indigo-600);font-weight:700;"></span>
                        </div>
                        <div style="background:var(--gray-100);border-radius:99px;height:7px;overflow:hidden;">
                            <div id="ai-bar"
                                style="height:100%;background:var(--indigo-500);border-radius:99px;width:0%;transition:.4s;">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="padding:14px 20px;border-top:1px solid var(--gray-100);display:flex;gap:8px;">
                    <a id="download-btn" href="#" download="ai_result.jpg" class="btn btn-primary btn-sm" target="_blank">
                        <i class="ri-download-line"></i> Download Result
                    </a>
                    <button onclick="runTest()" class="btn btn-white btn-sm">
                        <i class="ri-refresh-line"></i> Test Again
                    </button>
                    <a href="{{ route('admin.api-logs.index') }}" class="btn btn-white btn-sm" style="margin-left:auto;">
                        <i class="ri-terminal-box-line"></i> View in API Logs
                    </a>
                </div>
            </div>

            {{-- Idle placeholder --}}
            <div id="idle-card" class="card">
                <div class="card-body">
                    <div class="empty-state" style="padding:40px 20px;">
                        <i class="ri-flask-line" style="color:var(--indigo-300);"></i>
                        <h3 style="color:var(--gray-500);">Ready to Test</h3>
                        <p>Upload an image, select a tool, then hit <strong>Run AI Test</strong> to see the live output from
                            your AI provider.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('styles')
    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .tool-label-selected {
            border-color: var(--indigo-500) !important;
            background: var(--indigo-50) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let selectedFile = null;
        let selectedTool = '{{ $features->first()?->feature_id ?? "enhance" }}';

        // ── File handling ──────────────────────────────────────────
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                setFile(input.files[0]);
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('drop-zone').style.borderColor = 'var(--gray-300)';
            document.getElementById('drop-zone').style.background = 'var(--gray-25)';
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) setFile(file);
        }

        function setFile(file) {
            selectedFile = file;
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById('preview-img');
                img.src = e.target.result;
                img.style.display = 'block';
                document.getElementById('drop-placeholder').style.display = 'none';
                document.getElementById('clear-btn').style.display = 'inline-flex';
                document.getElementById('file-info').style.display = 'block';
                document.getElementById('file-info').textContent =
                    `${file.name} — ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            };
            reader.readAsDataURL(file);
        }

        function clearImage() {
            selectedFile = null;
            document.getElementById('preview-img').style.display = 'none';
            document.getElementById('drop-placeholder').style.display = 'block';
            document.getElementById('clear-btn').style.display = 'none';
            document.getElementById('file-info').style.display = 'none';
            document.getElementById('file-input').value = '';
        }

        // ── Tool selection ─────────────────────────────────────────
        function selectTool(id, el) {
            selectedTool = id;
            document.querySelectorAll('[id^="tool-"]').forEach(l => {
                l.style.borderColor = 'var(--gray-200)';
                l.style.background = 'var(--white)';
            });
            el.style.borderColor = 'var(--indigo-500)';
            el.style.background = 'var(--indigo-50)';
        }

        // ── Run test ───────────────────────────────────────────────
        async function runTest() {
            if (!selectedFile) {
                alert('Please upload an image first.');
                return;
            }

            // UI: show status
            show('status-card');
            hide('result-card');
            hide('error-card');
            hide('idle-card');
            document.getElementById('poll-log').innerHTML = '';
            setStatus('Uploading image to server...', 'Sending to Laravel backend');
            addLog('→ Uploading image...');

            document.getElementById('run-btn').disabled = true;

            const formData = new FormData();
            formData.append('image', selectedFile);
            formData.append('tool', selectedTool);
            formData.append('_token', '{{ csrf_token() }}');

            const t0 = Date.now();

            // Poll status messages while waiting
            const statusMessages = [
                'Sending to AI provider...',
                'AI is analyzing your image...',
                'Neural network processing...',
                'Upscaling & enhancing details...',
                'Applying final corrections...',
                'Almost done...',
            ];
            let msgIdx = 0;
            const msgTimer = setInterval(() => {
                if (msgIdx < statusMessages.length) {
                    setStatus(statusMessages[msgIdx], `Elapsed: ${((Date.now() - t0) / 1000).toFixed(0)}s`);
                    addLog(`⏳ ${statusMessages[msgIdx]}`);
                    msgIdx++;
                } else {
                    setStatus('Still processing...', `Elapsed: ${((Date.now() - t0) / 1000).toFixed(0)}s`);
                }
            }, 8000);

            try {
                const response = await fetch('{{ route('admin.ai-test.process') }}', {
                    method: 'POST',
                    body: formData,
                });

                clearInterval(msgTimer);
                const data = await response.json();

                if (data.success) {
                    addLog(`✅ Done! Result URL received`);
                    showResult(data);
                } else {
                    showError(data.message || 'Unknown error occurred.');
                }
            } catch (err) {
                clearInterval(msgTimer);
                showError('Network error: ' + err.message);
            } finally {
                document.getElementById('run-btn').disabled = false;
            }
        }

        // ── Result display ─────────────────────────────────────────
        function showResult(data) {
            hide('status-card');
            hide('idle-card');
            show('result-card');

            document.getElementById('result-img').src = data.result_url;
            document.getElementById('download-btn').href = data.result_url;

            const fmtMs = ms => ms >= 1000 ? (ms / 1000).toFixed(2) + 's' : Math.round(ms) + 'ms';

            document.getElementById('t-ai').textContent = fmtMs(data.ai_ms);
            document.getElementById('t-overhead').textContent = fmtMs(data.overhead_ms);
            document.getElementById('t-total').textContent = fmtMs(data.total_ms);
            document.getElementById('t-provider').textContent = data.provider;
            document.getElementById('t-tool').textContent = data.tool;
            document.getElementById('result-timing').textContent = `✓ ${fmtMs(data.total_ms)}`;

            const pct = data.total_ms > 0 ? Math.round((data.ai_ms / data.total_ms) * 100) : 0;
            document.getElementById('ai-bar').style.width = pct + '%';
            document.getElementById('ai-pct').textContent = pct + '%';

            // Poll log from server
            if (data.logs && data.logs.length) {
                data.logs.forEach(l => addLog(`[${l.at}ms] ${l.status} — ${l.msg}`));
            }
        }

        function showError(msg) {
            hide('status-card');
            show('error-card');
            document.getElementById('error-text').textContent = msg;
        }

        // ── Helpers ────────────────────────────────────────────────
        function show(id) { document.getElementById(id).style.display = 'block'; }
        function hide(id) { document.getElementById(id).style.display = 'none'; }

        function setStatus(text, sub) {
            document.getElementById('status-text').textContent = text;
            document.getElementById('status-sub').textContent = sub;
        }

        function addLog(msg) {
            const log = document.getElementById('poll-log');
            const ts = new Date().toLocaleTimeString();
            log.innerHTML += `<div><span style="color:var(--gray-400);">[${ts}]</span> ${msg}</div>`;
            log.scrollTop = log.scrollHeight;
        }
    </script>
@endpush