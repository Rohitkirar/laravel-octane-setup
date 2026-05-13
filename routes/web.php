<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QueueTestController;
use App\Http\Controllers\PerformanceController;

Route::get('/',        [PageController::class, 'home'])->name('home');
Route::get('/about',   [PageController::class, 'about'])->name('about');
Route::get('/blog',    [PageController::class, 'blog'])->name('blog');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');

Route::get('/queue-test',               [QueueTestController::class, 'show'])->name('queue.test');
Route::post('/queue-test/dispatch',     [QueueTestController::class, 'dispatch'])->name('queue.dispatch');
Route::post('/queue-test/dispatch-fail', [QueueTestController::class, 'dispatchFailing'])->name('queue.dispatch.fail');
Route::post('/queue-test/clear',        [QueueTestController::class, 'clear'])->name('queue.clear');

Route::get('/performance',  [PerformanceController::class, 'show'])->name('performance');
Route::get('/api/health',   [PerformanceController::class, 'ping'])->name('health.ping');
