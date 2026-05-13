<?php

namespace App\Http\Controllers;

use App\Jobs\FailingJob;
use App\Jobs\SampleJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class QueueTestController extends Controller
{
    public function show()
    {
        $pending = DB::table('jobs')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($row) {
                $payload = json_decode($row->payload, true);
                return [
                    'id'           => $row->id,
                    'status'       => 'pending',
                    'type'         => class_basename($payload['displayName'] ?? 'Unknown'),
                    'queue'        => $row->queue,
                    'attempts'     => $row->attempts,
                    'dispatched_at' => date('Y-m-d H:i:s', $row->created_at),
                    'available_at' => date('Y-m-d H:i:s', $row->available_at),
                    'error'        => null,
                ];
            });

        $failed = DB::table('failed_jobs')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($row) {
                return [
                    'id'           => $row->uuid,
                    'status'       => 'failed',
                    'type'         => class_basename($row->payload ? (json_decode($row->payload, true)['displayName'] ?? 'Unknown') : 'Unknown'),
                    'queue'        => $row->queue,
                    'attempts'     => '—',
                    'dispatched_at' => $row->failed_at,
                    'available_at' => '—',
                    'error'        => $row->exception ? substr($row->exception, 0, 200) : null,
                ];
            });

        $jobs      = $pending->merge($failed)->sortByDesc('dispatched_at')->values()->all();
        $queueSize = Queue::size('default');

        return view('queue-test', compact('jobs', 'queueSize'));
    }

    public function dispatch()
    {
        SampleJob::dispatch(now()->toDateTimeString());

        return redirect()->route('queue.test')->with('dispatched', 'SampleJob dispatched!');
    }

    public function dispatchFailing()
    {
        FailingJob::dispatch(now()->toDateTimeString());

        return redirect()->route('queue.test')->with('dispatched', 'FailingJob dispatched — expect it to fail!');
    }

    public function clear()
    {
        DB::table('jobs')->delete();
        DB::table('failed_jobs')->delete();

        return redirect()->route('queue.test')->with('dispatched', 'Job history cleared.');
    }
}
