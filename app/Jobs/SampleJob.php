<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SampleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly string $dispatchedAt) {}

    public function handle(): void
    {
        Log::info('SampleJob executed', [
            'dispatched_at' => $this->dispatchedAt,
            'executed_at'   => now()->toDateTimeString(),
            'worker_host'   => gethostname(),
            'latency_ms'    => round((microtime(true) - strtotime($this->dispatchedAt)) * 1000),
        ]);
    }
}
