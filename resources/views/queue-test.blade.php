@extends('layouts.app')

@section('title', 'Queue Test — Laravel Octane Demo')

@section('content')
    <div class="page-header">
        <h1>🧪 Queue Tester</h1>
        <p>Dispatch jobs and inspect their state via the database queue driver</p>
    </div>

    <div class="section">

        {{-- ── Status bar ── --}}
        <div class="queue-status-bar">
            <div class="status-pill {{ config('queue.default') === 'database' ? 'ok' : 'warn' }}">
                <span class="dot"></span>
                Driver: {{ strtoupper(config('queue.default')) }}
            </div>
            <div class="status-pill info">
                <span class="dot"></span>
                Pending in queue: {{ $queueSize }}
            </div>
        </div>

        @if (session('dispatched'))
            <div class="alert alert-success">✅ {{ session('dispatched') }}</div>
        @endif

        {{-- ── Dispatch buttons ── --}}
        <div class="queue-grid" style="margin-bottom:2rem">

            <div class="queue-card">
                <h2>🚀 SampleJob</h2>
                <p class="card-desc">Runs successfully. Removed from <code>jobs</code> table after execution.</p>
                <form method="POST" action="{{ route('queue.dispatch') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-full">⚡ Dispatch SampleJob</button>
                </form>
                <div class="code-block" style="margin-top:1rem;font-size:.8rem">
                    SELECT * FROM jobs; <span style="color:#86efac">-- appears while pending</span>
                </div>
            </div>

            <div class="queue-card">
                <h2>💥 FailingJob</h2>
                <p class="card-desc">Always throws an exception. Stored permanently in <code>failed_jobs</code> table with
                    full stack trace.</p>
                <form method="POST" action="{{ route('queue.dispatch.fail') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-full">💥 Dispatch FailingJob</button>
                </form>
                <div class="code-block" style="margin-top:1rem;font-size:.8rem">
                    SELECT * FROM failed_jobs; <span style="color:#fca5a5">-- persists with stack trace</span>
                </div>
            </div>

        </div>

        {{-- ── DB table reference ── --}}
        <div class="info-box" style="margin-bottom:2rem">
            <strong>📍 Database tables to watch:</strong>
            <div style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-top:.6rem">
                <span><code>jobs</code> — pending/retrying jobs (row deleted after success)</span>
                <span><code>failed_jobs</code> — permanently stored failed jobs with full stack trace</span>
            </div>
        </div>

        {{-- ── Job tracker table ── --}}
        <div class="queue-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
                <h2 style="margin:0">📊 Job Tracker
                    <span style="font-size:.8rem;font-weight:400;color:#64748b">(pending from <code>jobs</code> + failed
                        from <code>failed_jobs</code>)</span>
                </h2>
                @if (count($jobs))
                    <form method="POST" action="{{ route('queue.clear') }}" style="margin:0">
                        @csrf
                        <button type="submit" class="btn-ghost">🗑 Clear history</button>
                    </form>
                @endif
            </div>

            @if (count($jobs))
                <div style="overflow-x:auto">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Queue</th>
                                <th>Dispatched at</th>
                                <th>Attempts</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jobs as $job)
                                <tr>
                                    <td>
                                        @if (($job['status'] ?? '') === 'failed')
                                            <span class="status-badge failed">✕ failed</span>
                                        @else
                                            <span class="status-badge queued">⏳ pending</span>
                                        @endif
                                    </td>
                                    <td><code>{{ $job['type'] ?? '—' }}</code></td>
                                    <td>{{ $job['queue'] ?? '—' }}</td>
                                    <td>{{ $job['dispatched_at'] ?? '—' }}</td>
                                    <td>{{ $job['attempts'] ?? '—' }}</td>
                                    <td class="error-cell">{{ $job['error'] ? substr($job['error'], 0, 120) . '…' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div style="font-size:3rem;margin-bottom:1rem">📭</div>
                    <p>No jobs dispatched yet.</p>
                    <p style="font-size:.875rem;color:#94a3b8;margin-top:.5rem">
                        Use the buttons above. Pending jobs appear in the <code>jobs</code> table;
                        failed jobs stay permanently in <code>failed_jobs</code> with a full stack trace.
                    </p>
                </div>
            @endif
        </div>

        {{-- ── How it works ── --}}
        <div class="octane-box" style="margin-top:2rem">
            <h2>⚙️ How the database queue driver tracks jobs</h2>
            <ul>
                <li>On dispatch → row inserted into <code>jobs</code> table with serialized payload</li>
                <li>Worker picks up → row is locked, <code>handle()</code> runs, row is deleted on success</li>
                <li>Worker fails → row moved to <code>failed_jobs</code> with full exception + stack trace</li>
                <li>Completed jobs are gone (by design) — query <code>failed_jobs</code> for errors</li>
                <li>Use <code>php artisan queue:retry {uuid}</code> to re-queue a failed job</li>
                <li>Use <code>php artisan queue:failed</code> to list all failed jobs in the terminal</li>
            </ul>
        </div>

    </div>

    @push('styles')
        <style>
            .queue-status-bar {
                display: flex;
                gap: .75rem;
                flex-wrap: wrap;
                margin-bottom: 2rem;
            }

            .status-pill {
                display: flex;
                align-items: center;
                gap: .5rem;
                padding: .45rem 1rem;
                border-radius: 999px;
                font-size: .85rem;
                font-weight: 600;
                border: 1.5px solid transparent;
            }

            .status-pill.ok {
                background: #f0fdf4;
                color: #16a34a;
                border-color: #bbf7d0;
            }

            .status-pill.fail {
                background: #fef2f2;
                color: #dc2626;
                border-color: #fecaca;
            }

            .status-pill.warn {
                background: #fffbeb;
                color: #d97706;
                border-color: #fde68a;
            }

            .status-pill.info {
                background: #f0f9ff;
                color: #0369a1;
                border-color: #bae6fd;
            }

            .dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: currentColor;
                display: inline-block;
            }

            .alert {
                padding: .875rem 1.25rem;
                border-radius: 8px;
                margin-bottom: 1.25rem;
                font-size: .925rem;
                font-weight: 500;
            }

            .alert-success {
                background: #f0fdf4;
                color: #15803d;
                border: 1px solid #bbf7d0;
            }

            .alert-error {
                background: #fef2f2;
                color: #dc2626;
                border: 1px solid #fecaca;
            }

            .queue-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }

            @media(max-width:768px) {
                .queue-grid {
                    grid-template-columns: 1fr;
                }
            }

            .queue-card {
                background: #fff;
                border-radius: 12px;
                padding: 2rem;
                box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
            }

            .queue-card h2 {
                font-size: 1.2rem;
                font-weight: 700;
                margin-bottom: .25rem;
            }

            .card-desc {
                color: #64748b;
                font-size: .875rem;
                margin: .4rem 0 1.25rem;
            }

            .btn-full {
                width: 100%;
                font-size: 1rem;
                padding: .9rem;
                text-align: center;
                display: block;
                border: none;
            }

            .btn-danger {
                background: #dc2626;
                color: #fff;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: background .2s;
            }

            .btn-danger:hover {
                background: #b91c1c;
            }

            .btn-ghost {
                background: none;
                border: 1.5px solid #e2e8f0;
                color: #64748b;
                border-radius: 8px;
                padding: .4rem .9rem;
                font-size: .85rem;
                cursor: pointer;
            }

            .btn-ghost:hover {
                border-color: #dc2626;
                color: #dc2626;
            }

            .info-box {
                background: #f0f9ff;
                border: 1.5px solid #bae6fd;
                border-radius: 10px;
                padding: 1rem 1.25rem;
                font-size: .875rem;
                color: #0369a1;
            }

            .history-table {
                width: 100%;
                border-collapse: collapse;
                font-size: .85rem;
            }

            .history-table th {
                text-align: left;
                padding: .6rem .75rem;
                background: #f1f5f9;
                color: #475569;
                font-weight: 600;
                border-bottom: 2px solid #e2e8f0;
                white-space: nowrap;
            }

            .history-table td {
                padding: .6rem .75rem;
                border-bottom: 1px solid #f1f5f9;
                color: #1e293b;
                vertical-align: top;
            }

            .history-table tr:last-child td {
                border-bottom: none;
            }

            .history-table tr:hover td {
                background: #f8fafc;
            }

            .status-badge {
                display: inline-block;
                padding: .2rem .65rem;
                border-radius: 999px;
                font-size: .78rem;
                font-weight: 700;
                white-space: nowrap;
            }

            .status-badge.executed {
                background: #f0fdf4;
                color: #16a34a;
                border: 1px solid #bbf7d0;
            }

            .status-badge.failed {
                background: #fef2f2;
                color: #dc2626;
                border: 1px solid #fecaca;
            }

            .status-badge.queued {
                background: #fffbeb;
                color: #d97706;
                border: 1px solid #fde68a;
            }

            .error-cell {
                color: #dc2626;
                font-size: .8rem;
                max-width: 200px;
                word-break: break-word;
            }

            .empty-state {
                text-align: center;
                padding: 2.5rem 1rem;
                color: #64748b;
            }
        </style>
    @endpush
@endsection
