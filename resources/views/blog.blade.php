@extends('layouts.app')

@section('title', 'Blog – Laravel Octane Demo')

@section('content')

    <div class="page-header">
        <h1>Blog</h1>
        <p>Articles about Laravel Octane, performance, and modern PHP development.</p>
    </div>

    <div class="section">
        <div class="blog-grid">
            @foreach ($posts as $post)
                <article class="post-card">
                    <div class="post-banner">{{ $post['emoji'] }}</div>
                    <div class="post-card-body">
                        <span class="tag">{{ $post['tag'] }}</span>
                        <h3>{{ $post['title'] }}</h3>
                        <p>{{ $post['excerpt'] }}</p>
                        <div class="post-meta">
                            ✍️ {{ $post['author'] }} &nbsp;·&nbsp; 📅 {{ $post['date'] }} &nbsp;·&nbsp; ⏱
                            {{ $post['read_time'] }}
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

@endsection
