@extends('layouts.app')

@section('title', 'Contact – Laravel Octane Demo')

@section('content')

    <div class="page-header">
        <h1>Contact</h1>
        <p>Have a question about Laravel Octane? Get in touch.</p>
    </div>

    <div class="section">

        @if (session('success'))
            <div
                style="background:#f0fdf4; border:1.5px solid #86efac; color:#166534; padding:1rem 1.5rem; border-radius:8px; margin-bottom:2rem; font-weight:600;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="contact-wrapper">
            {{-- Info --}}
            <div class="contact-info">
                <h3>Get In Touch</h3>
                <p style="color:#64748b; margin-bottom:2rem;">
                    This is a demo contact form for the Laravel Octane setup project.
                    Fill in the form and explore how Laravel handles form submissions.
                </p>

                <div class="contact-item">
                    <div class="icon">📍</div>
                    <div>
                        <h4>Location</h4>
                        <p>GitHub — open source &amp; publicly available</p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="icon">📧</div>
                    <div>
                        <h4>Email</h4>
                        <p>demo@laravel-octane-demo.test</p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="icon">🐙</div>
                    <div>
                        <h4>GitHub</h4>
                        <p>github.com/laravel-octane-setup</p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="icon">⚡</div>
                    <div>
                        <h4>Server</h4>
                        <p>Laravel {{ app()->version() }} + Octane v2</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div>
                <form action="{{ route('contact.submit') }}" method="POST"
                    style="background:#fff; padding:2rem; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08);">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                placeholder="John" required>
                            @error('first_name')
                                <p style="color:#ef4444; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                placeholder="Doe" required>
                            @error('last_name')
                                <p style="color:#ef4444; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="john@example.com" required>
                        @error('email')
                            <p style="color:#ef4444; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                            placeholder="Question about Octane" required>
                        @error('subject')
                            <p style="color:#ef4444; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Write your message here…" required>{{ old('message') }}</textarea>
                        @error('message')
                            <p style="color:#ef4444; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary"
                        style="width:100%; justify-content:center; border:none; font-size:1rem; padding:.9rem;">
                        Send Message ✉️
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
