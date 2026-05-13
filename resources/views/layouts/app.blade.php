<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Laravel Octane Demo')</title>
    <style>
        /* ── Reset & base ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* ── Navbar ── */
        nav {
            background: #1e293b;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .25);
        }

        nav .brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: #f97316;
            letter-spacing: .5px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        nav ul li a {
            color: #cbd5e1;
            font-size: .95rem;
            transition: color .2s;
        }

        nav ul li a:hover,
        nav ul li a.active {
            color: #f97316;
        }

        /* ── Hero banner (home only) ── */
        .hero {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            padding: 5rem 2rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .hero h1 span {
            color: #f97316;
        }

        .hero p {
            font-size: 1.2rem;
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .btn {
            display: inline-block;
            padding: .75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background .2s, transform .15s;
            cursor: pointer;
        }

        .btn-primary {
            background: #f97316;
            color: #fff;
        }

        .btn-primary:hover {
            background: #ea6c0a;
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid #f97316;
            color: #f97316;
            margin-left: 1rem;
        }

        .btn-outline:hover {
            background: #f97316;
            color: #fff;
            transform: translateY(-2px);
        }

        /* ── Sections ── */
        .section {
            padding: 4rem 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: .5rem;
            text-align: center;
        }

        .section-sub {
            text-align: center;
            color: #64748b;
            margin-bottom: 3rem;
        }

        /* ── Cards ── */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
            transition: transform .2s, box-shadow .2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .card p {
            color: #64748b;
            font-size: .9rem;
        }

        /* ── Page header (inner pages) ── */
        .page-header {
            background: #1e293b;
            color: #fff;
            padding: 3rem 2rem;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
        }

        .page-header p {
            color: #94a3b8;
            margin-top: .5rem;
        }

        /* ── About page ── */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .about-grid img {
            width: 100%;
            border-radius: 12px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat {
            background: #1e293b;
            color: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat h2 {
            font-size: 2rem;
            color: #f97316;
        }

        .stat p {
            font-size: .85rem;
            color: #94a3b8;
        }

        /* ── Blog page ── */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .post-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
            transition: transform .2s;
        }

        .post-card:hover {
            transform: translateY(-4px);
        }

        .post-card-body {
            padding: 1.5rem;
        }

        .post-card .tag {
            display: inline-block;
            background: #fff7ed;
            color: #f97316;
            border: 1px solid #fed7aa;
            border-radius: 20px;
            font-size: .75rem;
            padding: .2rem .7rem;
            margin-bottom: .75rem;
            font-weight: 600;
        }

        .post-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .post-card p {
            color: #64748b;
            font-size: .875rem;
        }

        .post-meta {
            margin-top: 1rem;
            font-size: .8rem;
            color: #94a3b8;
        }

        .post-banner {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        /* ── Contact page ── */
        .contact-wrapper {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 3rem;
        }

        .contact-info h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .contact-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: flex-start;
        }

        .contact-item .icon {
            font-size: 1.5rem;
            margin-top: .1rem;
        }

        .contact-item h4 {
            font-weight: 700;
            font-size: .95rem;
        }

        .contact-item p {
            color: #64748b;
            font-size: .875rem;
        }

        form .form-group {
            margin-bottom: 1.25rem;
        }

        form label {
            display: block;
            font-size: .875rem;
            font-weight: 600;
            margin-bottom: .4rem;
        }

        form input,
        form textarea {
            width: 100%;
            padding: .75rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .95rem;
            transition: border-color .2s;
            background: #fff;
        }

        form input:focus,
        form textarea:focus {
            outline: none;
            border-color: #f97316;
        }

        form textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* ── Octane info box ── */
        .octane-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            border-radius: 16px;
            padding: 2.5rem;
            margin: 2rem 0;
        }

        .octane-box h2 {
            font-size: 1.6rem;
            color: #f97316;
            margin-bottom: 1rem;
        }

        .octane-box ul {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
        }

        .octane-box ul li::before {
            content: "✓ ";
            color: #f97316;
            font-weight: 700;
        }

        .octane-box ul li {
            color: #cbd5e1;
            font-size: .95rem;
        }

        code,
        pre {
            font-family: 'Courier New', monospace;
        }

        .code-block {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem;
            overflow-x: auto;
            font-size: .875rem;
            line-height: 1.8;
            margin: 1rem 0;
        }

        .code-block .kw {
            color: #f97316;
        }

        .code-block .cm {
            color: #64748b;
        }

        /* ── Badge ── */
        .badge {
            display: inline-block;
            background: #f97316;
            color: #fff;
            border-radius: 4px;
            padding: .15rem .55rem;
            font-size: .75rem;
            font-weight: 700;
            vertical-align: middle;
            margin-left: .4rem;
        }

        /* ── Footer ── */
        footer {
            background: #0f172a;
            color: #475569;
            text-align: center;
            padding: 2rem;
            font-size: .875rem;
            margin-top: 4rem;
        }

        footer span {
            color: #f97316;
        }

        /* ── Responsive ── */
        @media(max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .about-grid,
            .contact-wrapper {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .octane-box ul {
                grid-template-columns: 1fr;
            }

            .stat-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <nav>
        <a href="{{ route('home') }}" class="brand">⚡ Laravel<span style="color:#fff"> Octane</span></a>
        <ul>
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
            <li><a href="{{ route('blog') }}" class="{{ request()->routeIs('blog') ? 'active' : '' }}">Blog</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            </li>
            <li><a href="{{ route('queue.test') }}"
                    class="{{ request()->routeIs('queue.test') ? 'active' : '' }}">Queue Test</a></li>
            <li><a href="{{ route('performance') }}"
                    class="{{ request()->routeIs('performance') ? 'active' : '' }}">Performance</a></li>
        </ul>
    </nav>

    @yield('content')

    <footer>
        <p>&copy; {{ date('Y') }} <span>Laravel Octane Demo</span>. Built with Laravel {{ app()->version() }}
            &amp;
            Octane.</p>
    </footer>

    @stack('scripts')

</body>

</html>
