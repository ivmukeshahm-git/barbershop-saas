<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Overview') | BarberPro Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/super-admin.css') }}?v=3" rel="stylesheet">
    @stack('head')
</head>
<body class="pa-body">
<div class="pa-shell">
    <aside class="pa-sidebar">
        <a class="pa-brand" href="{{ route('platform.dashboard') }}" style="text-decoration:none;color:inherit">
            <span class="pa-brand-mark"><i class="bi bi-scissors"></i></span>
            <span><strong>BarberPro</strong><small>Platform control</small></span>
        </a>
        <div class="pa-nav-label">Workspace</div>
        <nav class="pa-nav" aria-label="Platform navigation">
            <a href="{{ route('platform.dashboard') }}" class="{{ request()->routeIs('platform.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i><span>Overview</span></a>
            <a href="{{ route('platform.shops') }}" class="{{ request()->routeIs('platform.shops*') ? 'active' : '' }}"><i class="bi bi-shop"></i><span>Shop accounts</span></a>
            <a href="{{ route('platform.payments') }}" class="{{ request()->routeIs('platform.payments') ? 'active' : '' }}"><i class="bi bi-credit-card-2-front"></i><span>Payments</span></a>
            <a href="{{ route('platform.plans') }}" class="{{ request()->routeIs('platform.plans*') ? 'active' : '' }}"><i class="bi bi-tags"></i><span>Plans &amp; pricing</span></a>
            <a href="{{ route('platform.website') }}" class="{{ request()->routeIs('platform.website*') ? 'active' : '' }}"><i class="bi bi-window"></i><span>Website settings</span></a>
        </nav>
        <div class="pa-side-bottom">
            <strong>{{ auth('platform')->user()->name }}</strong>
            Super administrator
        </div>
    </aside>
    <main class="pa-main">
        <header class="pa-topbar">
            <div class="pa-top-title">Platform administration</div>
            <div class="pa-top-right">
                <a href="{{ route('saas.home') }}" target="_blank" rel="noopener">View public website <i class="bi bi-arrow-up-right"></i></a>
                <div class="pa-avatar" aria-label="{{ auth('platform')->user()->name }}">{{ strtoupper(substr(auth('platform')->user()->name, 0, 1)) }}</div>
                <form method="POST" action="{{ route('platform.logout') }}">
                    @csrf
                    <button type="submit" class="pa-mini-button" aria-label="Sign out"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </header>
        <div class="pa-content">
            @if (session('success'))
                <div class="pa-alert pa-alert-success" role="status">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="pa-alert pa-alert-danger" role="alert">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@stack('scripts')
</body>
</html>
