<header class="lx-header {{ request()->routeIs('home') ? '' : 'solid scrolled' }}" id="lxHeader">
    <nav class="navbar navbar-expand-lg">
        <div class="container lx-nav-bar">
            @php
                $shopName = 'Rosenthal Barbershop';
                $logoSrc = asset('assets/img/logo.png?id=1');
            @endphp
            <a class="navbar-brand lx-logo" href="{{ route('home') }}" aria-label="{{ $shopName }} Home">
                <img
                    src="{{ $logoSrc }}"
                    data-logo-light="{{ $logoSrc }}"
                    data-logo-dark="{{ $logoSrc }}"
                    alt="{{ $shopName }}"
                    class="lx-logo-img"
                    id="lxBrandLogo"
                    width="56"
                    height="56"
                >
                <span class="lx-brand-copy">
                    <strong>Rosenthal</strong>
                    <span>Barbershop</span>
                </span>
            </a>

            <button class="lx-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#lxNav" aria-controls="lxNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars lx-menu-open-icon" aria-hidden="true"></i>
                <i class="fas fa-times lx-menu-close-icon" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="lxNav">
                <ul class="navbar-nav mx-auto lx-nav align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('services') ? 'active' : '' }}" href="{{ url('/services') }}">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('barbers') ? 'active' : '' }}" href="{{ url('/barbers') }}">Barbers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('contact') ? 'active' : '' }}" href="{{ url('/contact') }}">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('saas.pricing') ? 'active' : '' }}" href="{{ route('saas.pricing') }}">Pricing</a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-lg-2">
                    @php
                        $navPhone = $siteSetting->contact_phone ?? '+1 (587) 555-2489';
                        $navPhoneTel = optional($siteSetting)->phoneTel() ?: '+15875552489';
                    @endphp
                    <a href="tel:{{ $navPhoneTel }}" class="lx-phone">
                        <i class="fas fa-phone"></i> {{ $navPhone }}
                    </a>
                    <a href="{{ route('saas.find-shop') }}" class="lx-phone" style="margin-right:0.35rem">Shop Admin</a>
                    <a href="{{ route('saas.signup') }}" class="btn-lx btn-lx-primary lx-book-btn">Get Admin Panel</a>
                </div>
            </div>
        </div>
    </nav>
</header>
