<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FailingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Only try once — no retries so it fails immediately
    public int $tries = 1;

    public function __construct(public readonly string $dispatchedAt) {}

    public function handle(): void
    {
        throw new \RuntimeException(
            'Intentional failure at ' . now()->toDateTimeString() . ' — used to demonstrate failed job tracking.'
        );
    }

    public function failed(\Throwable $e): void
    {
        Log::error('FailingJob failed', ['error' => $e->getMessage()]);
    }
}
