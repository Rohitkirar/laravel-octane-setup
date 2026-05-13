@extends('layouts.app')

@section('title', 'Home – Laravel Octane Demo')

@section('content')

    {{-- Hero --}}
    <section class="hero">
        <h1>Supercharge Your App with <span>Laravel Octane</span></h1>
        <p>Laravel Octane keeps your application booted between requests, delivering blazing-fast response times with Swoole
            or RoadRunner.</p>
        <a href="{{ route('about') }}" class="btn btn-primary">Learn More</a>
        <a href="{{ route('blog') }}" class="btn btn-outline">Read the Blog</a>
    </section>

    {{-- Features --}}
    <div class="section">
        <h2 class="section-title">Why Laravel Octane?</h2>
        <p class="section-sub">Everything you need to run Laravel at warp speed.</p>

        <div class="cards">
            <div class="card">
                <div class="card-icon">🚀</div>
                <h3>Persistent Application State</h3>
                <p>Octane boots your application once and keeps it in memory, eliminating cold-start overhead on every
                    request.</p>
            </div>
            <div class="card">
                <div class="card-icon">⚙️</div>
                <h3>Concurrent Tasks</h3>
                <p>Run multiple operations simultaneously using Octane's built-in concurrency helpers powered by Swoole
                    coroutines.</p>
            </div>
            <div class="card">
                <div class="card-icon">🔒</div>
                <h3>Tick &amp; Interval Callbacks</h3>
                <p>Schedule lightweight recurring callbacks that execute inside the long-running server process without cron
                    overhead.</p>
            </div>
            <div class="card">
                <div class="card-icon">📦</div>
                <h3>Laravel Ecosystem</h3>
                <p>Works seamlessly with Queues, Cache, Broadcasting, Horizon, Telescope, and every other first-party
                    Laravel package.</p>
            </div>
            <div class="card">
                <div class="card-icon">🌐</div>
                <h3>Swoole &amp; RoadRunner</h3>
                <p>Choose between Swoole for coroutine-based concurrency or RoadRunner for a Go-powered high-performance
                    server.</p>
            </div>
            <div class="card">
                <div class="card-icon">📊</div>
                <h3>Request Lifecycle Insights</h3>
                <p>Integrated with Laravel Pulse and Telescope to give you real-time visibility into request performance and
                    errors.</p>
            </div>
        </div>
    </div>

    {{-- Octane Info Box --}}
    <div class="section" style="padding-top:0">
        <div class="octane-box">
            <h2>Quick Start Commands</h2>
            <div class="code-block">
                <span class="cm"># Install Octane</span><br>
                <span class="kw">composer</span> require laravel/octane<br><br>
                <span class="cm"># Publish config &amp; choose server</span><br>
                <span class="kw">php artisan</span> octane:install --server=swoole<br><br>
                <span class="cm"># Start the Octane server</span><br>
                <span class="kw">php artisan</span> octane:start --workers=4 --task-workers=2<br><br>
                <span class="cm"># Watch for file changes in development</span><br>
                <span class="kw">php artisan</span> octane:start --watch
            </div>
            <ul>
                <li>Zero-downtime deployments</li>
                <li>Shared memory between workers</li>
                <li>Configurable worker count</li>
                <li>Hot reload in development</li>
                <li>WebSocket support via Swoole</li>
                <li>Built-in task workers</li>
            </ul>
        </div>
    </div>

@endsection
