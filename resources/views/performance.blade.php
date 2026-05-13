@extends('layouts.app')

@section('title', 'Performance — Laravel Octane Demo')

@section('content')

    <div class="page-header">
        <h1>⚡ Performance Monitor</h1>
        <p>Live latency benchmarks against the Octane health endpoint</p>
    </div>

    <div class="section">

        {{-- ── Server info cards ── --}}
        <div class="info-grid">

            <div class="info-card">
                <div class="info-label">Server</div>
                <div class="info-value">{{ strtoupper($info['octane_server']) }}</div>
                <div class="info-sub">{{ $info['swoole_version'] ? 'Swoole v' . $info['swoole_version'] : 'N/A' }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Workers</div>
                <div class="info-value">{{ $info['octane_workers'] }}</div>
                <div class="info-sub">max {{ number_format($info['max_requests']) }} req/restart</div>
            </div>

            <div class="info-card">
                <div class="info-label">Worker PID</div>
                <div class="info-value">{{ $info['worker_pid'] }}</div>
                <div class="info-sub">{{ $info['hostname'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Memory</div>
                <div class="info-value">{{ $info['memory_mb'] }} MB</div>
                <div class="info-sub">peak {{ $info['memory_peak_mb'] }} MB</div>
            </div>

            <div class="info-card">
                <div class="info-label">OPcache</div>
                <div class="info-value" style="color: {{ $info['opcache_enabled'] ? '#16a34a' : '#dc2626' }}">
                    {{ $info['opcache_enabled'] ? 'Enabled' : 'Disabled' }}
                </div>
                <div class="info-sub">PHP {{ $info['php_version'] }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Drivers</div>
                <div class="info-value" style="font-size:1rem">{{ strtoupper($info['db_driver']) }}</div>
                <div class="info-sub">queue: {{ $info['queue_driver'] }} · cache: {{ $info['cache_driver'] }}</div>
            </div>

        </div>

        {{-- ── Benchmark runner ── --}}
        <div class="bench-card">
            <div class="bench-header">
                <h2>🎯 Latency Benchmark</h2>
                <p style="color:#64748b;font-size:.9rem;margin-top:.25rem">
                    Fires repeated requests to <code>GET /api/health</code> and measures round-trip time from the browser.
                </p>
            </div>

            <div class="bench-controls">
                <label>
                    Requests
                    <select id="reqCount">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                    </select>
                </label>
                <label>
                    Mode
                    <select id="reqMode">
                        <option value="sequential" selected>Sequential</option>
                        <option value="concurrent">Concurrent (all at once)</option>
                    </select>
                </label>
                <button id="runBtn" class="btn btn-primary" onclick="runBenchmark()">▶ Run Benchmark</button>
                <button class="btn-ghost" onclick="resetBenchmark()">↺ Reset</button>
            </div>

            {{-- Stats row --}}
            <div class="stats-row" id="statsRow" style="display:none">
                <div class="stat-box">
                    <div class="stat-label">Min</div>
                    <div class="stat-value" id="statMin">—</div>
                    <div class="stat-unit">ms</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Avg</div>
                    <div class="stat-value" id="statAvg">—</div>
                    <div class="stat-unit">ms</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Max</div>
                    <div class="stat-value" id="statMax">—</div>
                    <div class="stat-unit">ms</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">p95</div>
                    <div class="stat-value" id="statP95">—</div>
                    <div class="stat-unit">ms</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">p99</div>
                    <div class="stat-value" id="statP99">—</div>
                    <div class="stat-unit">ms</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Errors</div>
                    <div class="stat-value" id="statErrors" style="color:#dc2626">0</div>
                    <div class="stat-unit">req</div>
                </div>
            </div>

            {{-- Progress bar --}}
            <div id="progressWrap" style="display:none;margin:1.5rem 0 .5rem">
                <div style="display:flex;justify-content:space-between;font-size:.8rem;color:#64748b;margin-bottom:.4rem">
                    <span id="progressLabel">Running…</span>
                    <span id="progressPct">0%</span>
                </div>
                <div style="background:#e2e8f0;border-radius:999px;height:8px;overflow:hidden">
                    <div id="progressBar"
                        style="height:100%;width:0%;background:linear-gradient(90deg,#6366f1,#8b5cf6);transition:width .1s ease;border-radius:999px">
                    </div>
                </div>
            </div>

            {{-- Chart area --}}
            <div id="chartWrap" style="display:none;margin-top:1.5rem">
                <div style="font-size:.8rem;color:#94a3b8;margin-bottom:.5rem">Round-trip latency per request (ms)</div>
                <div id="chart"
                    style="display:flex;align-items:flex-end;gap:2px;height:100px;background:#f8fafc;border-radius:8px;padding:8px;overflow:hidden">
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.75rem;color:#94a3b8;margin-top:.3rem">
                    <span>req 1</span>
                    <span id="chartEnd">req 50</span>
                </div>
            </div>

            {{-- Live log --}}
            <div id="logWrap" style="margin-top:1.5rem;display:none">
                <div style="font-size:.8rem;color:#94a3b8;margin-bottom:.5rem">Live results</div>
                <div id="logTable"
                    style="font-family:monospace;font-size:.8rem;max-height:220px;overflow-y:auto;background:#0f172a;color:#e2e8f0;border-radius:8px;padding:1rem">
                </div>
            </div>
        </div>

        {{-- ── Raw health JSON ── --}}
        <div class="bench-card" style="margin-top:1.5rem">
            <h2 style="margin-bottom:1rem">🔍 Health Endpoint Response</h2>
            <p style="color:#64748b;font-size:.875rem;margin-bottom:1rem">
                A single call to <code>GET /api/health</code> — shows what each benchmark request returns.
            </p>
            <button class="btn-ghost" onclick="fetchSingle()">Fetch once</button>
            <div id="singleResult"
                style="margin-top:1rem;font-family:monospace;font-size:.825rem;background:#0f172a;color:#86efac;border-radius:8px;padding:1.25rem;display:none;white-space:pre-wrap">
            </div>
        </div>

    </div>

    @push('styles')
        <style>
            .page-header {
                padding: 2.5rem 2rem 1rem;
                max-width: 1100px;
                margin: 0 auto;
            }

            .page-header h1 {
                font-size: 2rem;
                font-weight: 800;
                margin-bottom: .4rem;
            }

            .page-header p {
                color: #64748b;
            }

            .section {
                max-width: 1100px;
                margin: 0 auto;
                padding: 1rem 2rem 3rem;
            }

            /* Info grid */
            .info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .info-card {
                background: #fff;
                border-radius: 12px;
                padding: 1.25rem 1rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
                text-align: center;
            }

            .info-label {
                font-size: .75rem;
                font-weight: 600;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: .4rem;
            }

            .info-value {
                font-size: 1.4rem;
                font-weight: 800;
                color: #1e293b;
            }

            .info-sub {
                font-size: .75rem;
                color: #64748b;
                margin-top: .25rem;
            }

            /* Bench card */
            .bench-card {
                background: #fff;
                border-radius: 12px;
                padding: 2rem;
                box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
            }

            .bench-card h2 {
                font-size: 1.2rem;
                font-weight: 700;
            }

            .bench-header {
                margin-bottom: 1.5rem;
            }

            .bench-controls {
                display: flex;
                gap: 1rem;
                align-items: flex-end;
                flex-wrap: wrap;
                margin-bottom: 1.5rem;
            }

            .bench-controls label {
                display: flex;
                flex-direction: column;
                gap: .3rem;
                font-size: .8rem;
                font-weight: 600;
                color: #475569;
            }

            .bench-controls select {
                padding: .45rem .75rem;
                border: 1.5px solid #e2e8f0;
                border-radius: 8px;
                font-size: .9rem;
                background: #f8fafc;
                cursor: pointer;
            }

            /* Stats */
            .stats-row {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: .75rem;
                margin: 1.5rem 0;
            }

            @media(max-width:640px) {
                .stats-row {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            .stat-box {
                background: #f8fafc;
                border-radius: 10px;
                padding: .9rem .5rem;
                text-align: center;
                border: 1.5px solid #e2e8f0;
            }

            .stat-label {
                font-size: .7rem;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .stat-value {
                font-size: 1.5rem;
                font-weight: 800;
                color: #6366f1;
                margin: .2rem 0;
            }

            .stat-unit {
                font-size: .7rem;
                color: #94a3b8;
            }

            /* Buttons */
            .btn {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                padding: .6rem 1.4rem;
                border-radius: 8px;
                font-size: .9rem;
                font-weight: 600;
                cursor: pointer;
                border: none;
                transition: opacity .15s;
            }

            .btn:disabled {
                opacity: .5;
                cursor: not-allowed;
            }

            .btn-primary {
                background: #6366f1;
                color: #fff;
            }

            .btn-primary:hover:not(:disabled) {
                background: #4f46e5;
            }

            .btn-ghost {
                background: transparent;
                border: 1.5px solid #e2e8f0;
                color: #475569;
                padding: .55rem 1.2rem;
                border-radius: 8px;
                font-size: .875rem;
                font-weight: 600;
                cursor: pointer;
            }

            .btn-ghost:hover {
                background: #f1f5f9;
            }

            code {
                background: #f1f5f9;
                padding: .1em .4em;
                border-radius: 4px;
                font-size: .875em;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const PING_URL = '{{ route('health.ping') }}';
            let results = [];

            function percentile(sorted, p) {
                const idx = Math.ceil((p / 100) * sorted.length) - 1;
                return sorted[Math.max(0, idx)];
            }

            function updateStats(times, errors) {
                if (!times.length) return;
                const sorted = [...times].sort((a, b) => a - b);
                document.getElementById('statMin').textContent = sorted[0].toFixed(1);
                document.getElementById('statAvg').textContent = (times.reduce((a, b) => a + b, 0) / times.length).toFixed(1);
                document.getElementById('statMax').textContent = sorted[sorted.length - 1].toFixed(1);
                document.getElementById('statP95').textContent = percentile(sorted, 95).toFixed(1);
                document.getElementById('statP99').textContent = percentile(sorted, 99).toFixed(1);
                document.getElementById('statErrors').textContent = errors;
                document.getElementById('statsRow').style.display = 'grid';
            }

            function renderChart(times) {
                const chart = document.getElementById('chart');
                const max = Math.max(...times);
                chart.innerHTML = '';
                times.forEach((t, i) => {
                    const bar = document.createElement('div');
                    const pct = max > 0 ? (t / max) * 100 : 0;
                    // colour: green < 10ms, orange < 50ms, red >= 50ms
                    const color = t < 10 ? '#22c55e' : t < 50 ? '#f97316' : '#ef4444';
                    bar.style.cssText =
                        `flex:1;min-width:2px;height:${Math.max(4,pct)}%;background:${color};border-radius:2px 2px 0 0;transition:height .1s`;
                    bar.title = `req ${i+1}: ${t.toFixed(1)} ms`;
                    chart.appendChild(bar);
                });
                document.getElementById('chartWrap').style.display = 'block';
                document.getElementById('chartEnd').textContent = `req ${times.length}`;
            }

            function appendLog(i, ms, data) {
                const el = document.getElementById('logTable');
                const line = document.createElement('div');
                const color = ms < 10 ? '#86efac' : ms < 50 ? '#fbbf24' : '#f87171';
                line.innerHTML = `<span style="color:#64748b">#${String(i+1).padStart(3,'0')}</span>  ` +
                    `<span style="color:${color}">${ms.toFixed(1).padStart(7)} ms</span>  ` +
                    `pid:<span style="color:#c084fc">${data?.worker_pid ?? '?'}</span>  ` +
                    `server:<span style="color:#67e8f9">${(data?.processing_us ?? 0)/1000}ms</span>  ` +
                    `mem:<span style="color:#a5b4fc">${data?.memory_mb ?? '?'}MB</span>`;
                el.appendChild(line);
                el.scrollTop = el.scrollHeight;
            }

            async function pingOnce(i) {
                const t0 = performance.now();
                const res = await fetch(PING_URL, {
                    cache: 'no-store'
                });
                const ms = performance.now() - t0;
                const data = res.ok ? await res.json() : null;
                return {
                    ms,
                    data,
                    ok: res.ok
                };
            }

            async function runBenchmark() {
                const n = parseInt(document.getElementById('reqCount').value);
                const mode = document.getElementById('reqMode').value;
                const btn = document.getElementById('runBtn');

                resetBenchmark();
                btn.disabled = true;
                btn.textContent = '⏳ Running…';

                document.getElementById('progressWrap').style.display = 'block';
                document.getElementById('logWrap').style.display = 'block';
                document.getElementById('chartEnd').textContent = `req ${n}`;

                const times = [];
                let errors = 0;

                function setProgress(done) {
                    const pct = Math.round((done / n) * 100);
                    document.getElementById('progressBar').style.width = pct + '%';
                    document.getElementById('progressPct').textContent = pct + '%';
                    document.getElementById('progressLabel').textContent = `${done} / ${n} requests`;
                }

                if (mode === 'sequential') {
                    for (let i = 0; i < n; i++) {
                        try {
                            const {
                                ms,
                                data,
                                ok
                            } = await pingOnce(i);
                            if (ok) {
                                times.push(ms);
                                appendLog(i, ms, data);
                            } else {
                                errors++;
                                appendLog(i, ms, null);
                            }
                        } catch {
                            errors++;
                        }
                        setProgress(i + 1);
                        updateStats(times, errors);
                        renderChart(times);
                    }
                } else {
                    // concurrent — all fired simultaneously
                    const promises = Array.from({
                        length: n
                    }, (_, i) => pingOnce(i));
                    const all = await Promise.allSettled(promises);
                    all.forEach(({
                        status,
                        value
                    }, i) => {
                        if (status === 'fulfilled' && value.ok) {
                            times.push(value.ms);
                            appendLog(i, value.ms, value.data);
                        } else {
                            errors++;
                        }
                        setProgress(i + 1);
                    });
                    updateStats(times, errors);
                    renderChart(times);
                }

                document.getElementById('progressLabel').textContent = `✅ Done — ${n} requests`;
                btn.disabled = false;
                btn.textContent = '▶ Run Again';
            }

            function resetBenchmark() {
                results = [];
                ['statMin', 'statAvg', 'statMax', 'statP95', 'statP99'].forEach(id => document.getElementById(id).textContent =
                    '—');
                document.getElementById('statErrors').textContent = '0';
                document.getElementById('statsRow').style.display = 'none';
                document.getElementById('progressWrap').style.display = 'none';
                document.getElementById('chartWrap').style.display = 'none';
                document.getElementById('logWrap').style.display = 'none';
                document.getElementById('logTable').innerHTML = '';
                document.getElementById('chart').innerHTML = '';
                document.getElementById('progressBar').style.width = '0%';
                document.getElementById('runBtn').disabled = false;
                document.getElementById('runBtn').textContent = '▶ Run Benchmark';
            }

            async function fetchSingle() {
                const el = document.getElementById('singleResult');
                el.style.display = 'block';
                el.textContent = 'Fetching…';
                try {
                    const t0 = performance.now();
                    const res = await fetch(PING_URL, {
                        cache: 'no-store'
                    });
                    const ms = (performance.now() - t0).toFixed(2);
                    const data = await res.json();
                    el.textContent = JSON.stringify({
                        ...data,
                        _round_trip_ms: parseFloat(ms)
                    }, null, 2);
                } catch (e) {
                    el.textContent = 'Error: ' + e.message;
                }
            }
        </script>
    @endpush

@endsection
