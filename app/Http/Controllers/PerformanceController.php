<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function show()
    {
        $info = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'octane_server'   => config('octane.server', 'swoole'),
            'octane_workers'  => config('octane.swoole.options.worker_num', env('OCTANE_WORKERS', 4)),
            'max_requests'    => env('OCTANE_MAX_REQUESTS', 500),
            'worker_pid'      => getmypid(),
            'hostname'        => gethostname(),
            'memory_mb'       => round(memory_get_usage(true) / 1024 / 1024, 2),
            'memory_peak_mb'  => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
            'swoole_version'  => defined('SWOOLE_VERSION') ? SWOOLE_VERSION : null,
            'db_driver'       => config('database.default'),
            'queue_driver'    => config('queue.default'),
            'cache_driver'    => config('cache.default'),
        ];

        return view('performance', compact('info'));
    }

    /**
     * Lightweight JSON health ping — used by the benchmark runner.
     * Measures server-side processing time in microseconds.
     */
    public function ping(): JsonResponse
    {
        $start = hrtime(true); // nanoseconds

        // Minimal DB ping to include real I/O cost
        try {
            DB::select('SELECT 1');
            $dbOk = true;
        } catch (\Throwable) {
            $dbOk = false;
        }

        $elapsedUs = round((hrtime(true) - $start) / 1000); // nanoseconds → microseconds

        return response()->json([
            'status'          => 'ok',
            'worker_pid'      => getmypid(),
            'hostname'        => gethostname(),
            'server_time'     => now()->toIso8601String(),
            'processing_us'   => $elapsedUs,     // server-side processing time
            'memory_mb'       => round(memory_get_usage(true) / 1024 / 1024, 2),
            'db_ok'           => $dbOk,
            'php_version'     => PHP_VERSION,
            'swoole_version'  => defined('SWOOLE_VERSION') ? SWOOLE_VERSION : null,
        ]);
    }
}
