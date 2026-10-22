<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $platformSiteName = $platformSiteSettings['site_name'] ?? 'BarberPro';
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('document_title')@yield('document_title')@else @yield('title', 'Barber Shop Management Software') | {{ $platformSiteName }} @endif</title>
    <meta name="description" content="@yield('meta_description', 'BarberPro — modern barber shop management software for appointments, customers, staff, POS and reports.')">
    <link rel="icon" type="image/png" href="{{ asset('frontend/images/brand/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/saas.css') }}?v=9" rel="stylesheet">
    <link href="{{ asset('frontend/css/saas-marketing.css') }}?v=12" rel="stylesheet">
    @stack('head')
</head>
<body class="bp-body @yield('body_class')">
    @if (!empty($platformSiteSettings['announcement']))
        <div class="bp-container" role="status" style="padding-top:8px;padding-bottom:8px;text-align:center;font-size:13px">
            {{ $platformSiteSettings['announcement'] }}
        </div>
    @endif
    <header class="bp-nav @yield('nav_class')" id="bpNav">
        <div class="bp-container bp-nav-inner">
            <a class="bp-logo" href="{{ route('saas.home') }}">
                <span class="bp-logo-mark">
                    <img src="{{ asset('frontend/images/brand/logo-icon-theme.png') }}" alt="" width="32" height="32">
                </span>
                <span class="bp-logo-text">
                    <strong>{{ $platformSiteName }}</strong>
                </span>
            </a>

            <button type="button" class="bp-nav-toggle" id="bpNavToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="bpNavLinks">
                <i class="bi bi-list"></i>
            </button>

            <div class="bp-nav-menu" id="bpNavLinks">
                <nav class="bp-nav-center" aria-label="Primary">
                    <a href="{{ route('saas.home') }}#features">Features</a>
                    <a href="{{ route('saas.home') }}#solution">Solutions</a>
                    <a href="{{ route('saas.home') }}#how-it-works">How It Works</a>
                    <a href="{{ route('saas.home') }}#benefits">Benefits</a>
                    <a href="{{ route('saas.home') }}#pricing">Pricing</a>
                    <a href="{{ route('saas.home') }}#faq">FAQ</a>
                </nav>
                <div class="bp-nav-right">
                    <a class="bp-link" href="{{ route('saas.find-shop') }}">Login</a>
                    <a class="bp-btn bp-btn-primary" href="{{ route('saas.signup') }}">Get Started</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div class="bp-container bp-flash-wrap">
                <div class="bp-alert bp-alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="bp-container bp-flash-wrap">
                <div class="bp-alert bp-alert-danger">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bp-footer">
        <div class="bp-footer-accent" aria-hidden="true"></div>
        <div class="bp-container">
            <div class="bp-footer-band">
                <div>
                    <p class="bp-footer-kicker">One admin for the whole shop</p>
                    <h2>Appointments, staff, customers and bills, together.</h2>
                </div>
                <ul class="bp-footer-points">
                    <li><i class="bi bi-calendar-check" aria-hidden="true"></i> Daily bookings</li>
                    <li><i class="bi bi-people" aria-hidden="true"></i> Customer list</li>
                    <li><i class="bi bi-person-badge" aria-hidden="true"></i> Barber hours</li>
                    <li><i class="bi bi-receipt" aria-hidden="true"></i> GST invoices</li>
                </ul>
                <a class="bp-btn" href="{{ route('saas.signup') }}">Get Started</a>
            </div>
            <div class="bp-footer-grid">
                <div class="bp-footer-brand">
                    <a class="bp-logo" href="{{ route('saas.home') }}">
                        <span class="bp-logo-mark">
                            <img src="{{ asset('frontend/images/brand/logo-icon-theme.png') }}" alt="" width="32" height="32">
                        </span>
                        <span class="bp-logo-text">
                            <strong>{{ $platformSiteName }}</strong>
                        </span>
                    </a>
                            <p>{{ $platformSiteSettings['site_tagline'] ?? 'Simple software for modern barber shops. Appointments, customers, staff and bills in one admin.' }}</p>
                            @if (!empty($platformSiteSettings['support_email']))
                                <a href="mailto:{{ $platformSiteSettings['support_email'] }}">{{ $platformSiteSettings['support_email'] }}</a>
                            @endif
                            @if (!empty($platformSiteSettings['support_phone']))
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $platformSiteSettings['support_phone']) }}">{{ $platformSiteSettings['support_phone'] }}</a>
                            @endif
                    <div class="bp-social" aria-label="Social media">
                        <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                        <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                        <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
                        <a href="https://x.com/" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="bp-footer-col">
                    <h4>Product</h4>
                    <a href="{{ route('saas.home') }}#features">Features</a>
                    <a href="{{ route('saas.home') }}#how-it-works">How It Works</a>
                    <a href="{{ route('saas.home') }}#benefits">Benefits</a>
                    <a href="{{ route('saas.home') }}#pricing">Pricing</a>
                    <a href="{{ route('saas.home') }}#watch">See it in action</a>
                </div>
                <div class="bp-footer-col">
                    <h4>Company</h4>
                    <a href="{{ route('saas.home') }}#solution">About</a>
                    <a href="{{ route('saas.home') }}#faq">Before you buy</a>
                    <a href="{{ route('saas.home') }}#benefits">Benefits</a>
                    <a href="{{ route('saas.home') }}#cta">Get started</a>
                </div>
                <div class="bp-footer-col">
                    <h4>Account</h4>
                    <a href="{{ route('saas.find-shop') }}">Shop login</a>
                    <a href="{{ route('saas.signup') }}">Create a shop</a>
                    <a href="{{ route('saas.home') }}#pricing">Choose a plan</a>
                    <a href="{{ route('saas.home') }}#how-it-works">Setup steps</a>
                </div>
            </div>
            <div class="bp-footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $platformSiteName }}. All rights reserved.</p>
                <p>Built for modern barber shops.</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('frontend/js/saas-marketing.js') }}?v=5" defer></script>
    @stack('scripts')
</body>
</html>
