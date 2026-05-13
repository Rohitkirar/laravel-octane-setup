<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function about()
    {
        $stack = [
            ['icon' => '🐘', 'name' => 'PHP',            'version' => '8.2'],
            ['icon' => '🎯', 'name' => 'Laravel',        'version' => '^12.0'],
            ['icon' => '⚡', 'name' => 'Laravel Octane', 'version' => '^2.0'],
            ['icon' => '🏎️', 'name' => 'Swoole',         'version' => '5.x (optional)'],
            ['icon' => '🛣️', 'name' => 'RoadRunner',     'version' => '2023.x (optional)'],
            ['icon' => '🎨', 'name' => 'Blade Templates', 'version' => 'built-in'],
        ];

        return view('about', compact('stack'));
    }

    public function blog()
    {
        $posts = [
            [
                'emoji'     => '⚡',
                'tag'       => 'Performance',
                'title'     => 'Getting Started with Laravel Octane',
                'excerpt'   => 'Learn how to install and configure Laravel Octane to dramatically speed up your application by keeping it booted between requests.',
                'author'    => 'Taylor Otwell',
                'date'      => 'Jan 15, 2025',
                'read_time' => '5 min read',
            ],
            [
                'emoji'     => '🦅',
                'tag'       => 'Swoole',
                'title'     => 'Swoole vs RoadRunner: Which Should You Choose?',
                'excerpt'   => 'A deep dive into the two main server adapters for Laravel Octane — comparing performance, features, and ease of setup.',
                'author'    => 'Nuno Maduro',
                'date'      => 'Feb 3, 2025',
                'read_time' => '8 min read',
            ],
            [
                'emoji'     => '🔄',
                'tag'       => 'Concurrency',
                'title'     => 'Concurrent Tasks in Laravel Octane',
                'excerpt'   => 'How to use Octane\'s powerful concurrency API to run multiple operations in parallel and dramatically reduce latency.',
                'author'    => 'Mohamed Said',
                'date'      => 'Mar 10, 2025',
                'read_time' => '6 min read',
            ],
            [
                'emoji'     => '🛡️',
                'tag'       => 'Best Practices',
                'title'     => 'Memory Leaks in Long-Running PHP Processes',
                'excerpt'   => 'Understand common pitfalls with persistent application state and learn patterns to avoid memory leaks in Octane applications.',
                'author'    => 'Freek Van der Herten',
                'date'      => 'Apr 22, 2025',
                'read_time' => '10 min read',
            ],
            [
                'emoji'     => '🚀',
                'tag'       => 'Deployment',
                'title'     => 'Zero-Downtime Deployments with Octane',
                'excerpt'   => 'Deploy your Laravel Octane application to production with zero downtime using rolling restarts and health checks.',
                'author'    => 'Caleb Porzio',
                'date'      => 'May 5, 2025',
                'read_time' => '7 min read',
            ],
            [
                'emoji'     => '📊',
                'tag'       => 'Monitoring',
                'title'     => 'Monitoring Octane with Laravel Pulse',
                'excerpt'   => 'Set up Laravel Pulse to monitor your Octane server in real time — track request throughput, memory usage, and slow queries.',
                'author'    => 'Tim MacDonald',
                'date'      => 'May 11, 2025',
                'read_time' => '4 min read',
            ],
        ];

        return view('blog', compact('posts'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255'],
            'subject'    => ['required', 'string', 'max:200'],
            'message'    => ['required', 'string', 'max:2000'],
        ]);

        // In a real app you would dispatch a mail job here.
        // e.g. Mail::to('admin@example.com')->send(new ContactMail($request->validated()));

        return redirect()->route('contact')->with('success', 'Thank you! Your message has been received.');
    }
}
