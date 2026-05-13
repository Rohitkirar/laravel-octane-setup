@extends('layouts.app')

@section('title', 'About – Laravel Octane Demo')

@section('content')

    <div class="page-header">
        <h1>About This Project</h1>
        <p>A demonstration of Laravel 12 powered by Octane for maximum performance.</p>
    </div>

    <div class="section">
        <div class="about-grid">
            <div>
                <h2 style="font-size:1.8rem; font-weight:800; margin-bottom:1rem;">
                    What is Laravel Octane? <span class="badge">v2</span>
                </h2>
                <p style="color:#475569; margin-bottom:1rem;">
                    Laravel Octane supercharges your application's performance by serving your application using
                    high-powered application servers,
                    including <strong>Swoole</strong> and <strong>FrankenPHP</strong>. Octane boots your application once,
                    keeps it in memory,
                    and then feeds it requests at supersonic speeds.
                </p>
                <p style="color:#475569; margin-bottom:1rem;">
                    Unlike traditional PHP-FPM setups where the framework bootstraps on every request, Octane keeps the
                    application state alive
                    between requests — dramatically reducing latency and increasing throughput.
                </p>
                <p style="color:#475569;">
                    This project was set up following best practices outlined in the official Laravel Octane documentation
                    and demonstrates
                    routes, Blade templates, controllers, and the Octane configuration.
                </p>
            </div>
            <div style="background:#1e293b; border-radius:12px; padding:2rem; color:#fff;">
                <h3 style="color:#f97316; margin-bottom:1.5rem;">Project Tech Stack</h3>
                <ul style="list-style:none; display:flex; flex-direction:column; gap:.9rem;">
                    @foreach ($stack as $item)
                        <li style="display:flex; align-items:center; gap:.75rem; color:#cbd5e1;">
                            <span style="font-size:1.4rem;">{{ $item['icon'] }}</span>
                            <div>
                                <strong style="color:#f1f5f9;">{{ $item['name'] }}</strong>
                                <span
                                    style="color:#64748b; font-size:.8rem; margin-left:.5rem;">{{ $item['version'] }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stat-grid">
            <div class="stat">
                <h2>10×</h2>
                <p>Faster than traditional PHP-FPM</p>
            </div>
            <div class="stat">
                <h2>~1ms</h2>
                <p>Average response time</p>
            </div>
            <div class="stat">
                <h2>∞</h2>
                <p>Concurrent requests (Swoole)</p>
            </div>
        </div>

        {{-- How it works --}}
        <div style="margin-top:3rem;">
            <h2 class="section-title">How Octane Works</h2>
            <p class="section-sub">The request lifecycle with Octane vs traditional PHP-FPM.</p>
            <div class="cards" style="grid-template-columns: 1fr 1fr;">
                <div class="card" style="border-left: 4px solid #94a3b8;">
                    <h3 style="color:#64748b;">Traditional PHP-FPM</h3>
                    <ol style="color:#64748b; font-size:.875rem; padding-left:1.2rem; margin-top:.75rem; line-height:2;">
                        <li>HTTP request arrives</li>
                        <li>PHP-FPM spawns a process</li>
                        <li>Laravel bootstraps (autoloader, config, providers…)</li>
                        <li>Request handled</li>
                        <li>Process dies — memory freed</li>
                        <li><strong>Repeat for every request</strong></li>
                    </ol>
                </div>
                <div class="card" style="border-left: 4px solid #f97316;">
                    <h3 style="color:#f97316;">Laravel Octane</h3>
                    <ol style="color:#475569; font-size:.875rem; padding-left:1.2rem; margin-top:.75rem; line-height:2;">
                        <li>Application boots <strong>once</strong></li>
                        <li>Swoole/RoadRunner server listens</li>
                        <li>HTTP request arrives</li>
                        <li>Cloned app instance handles request</li>
                        <li>State reset — worker stays alive</li>
                        <li><strong>Reuses the same process</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

@endsection
