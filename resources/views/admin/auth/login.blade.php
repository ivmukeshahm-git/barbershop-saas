<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @php
        $shopName = $shop->name ?? 'Infotech';
        $shopLogo = $shop ? $shop->logoUrl() : asset('assets/img/logo.webp');
    @endphp
    <title>{{ $shopName }} | Admin Login</title>
    <meta name="description" content="Sign in to the {{ $shopName }} Admin Dashboard." />
    <link rel="icon" type="image/webp" href="{{ $shopLogo }}" />

    <link href="{{ asset('assets/vendor/auth/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/auth/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/css/admin-auth-fonts.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/css/admin-login.css') }}?v=3" rel="stylesheet" />
    @include('admin.partials.shop-theme', ['currentShop' => $shop ?? null])
</head>

<body class="al-page">
    <div class="al-loader" id="LoginLoader" aria-live="polite" aria-busy="true">
        <div class="al-loader-inner">
            <img class="al-loader-logo" src="{{ $shopLogo }}" alt="{{ $shopName }}">
            <div class="al-spinner" aria-hidden="true"></div>
            <h3 class="al-loader-title">Signing you in...</h3>
            <p class="al-loader-sub">Please wait...</p>
        </div>
    </div>

    <div class="al-shell">
        <aside class="al-right" aria-hidden="true">
            <div class="al-right-overlay"></div>
            <div class="al-right-glow g1"></div>
            <div class="al-right-glow g2"></div>
            <div class="al-right-content">
                <div class="al-right-eyebrow">
                    <i class="bi bi-shield-check"></i> Admin Portal
                </div>
                <h2>Manage {{ $shopName }} with confidence.</h2>
                <p class="lead">Appointments, staff, services, and reports — branded for your shop.</p>
                <div class="al-stats">
                    <div class="al-stat">
                        <strong>Shop</strong>
                        <span>{{ $shop->slug ?? 'admin' }}</span>
                    </div>
                    <div class="al-stat">
                        <strong>Secure</strong>
                        <span>Staff login</span>
                    </div>
                </div>
            </div>
        </aside>

        <section class="al-left">
            <div class="al-deco al-deco-1" aria-hidden="true"></div>
            <div class="al-deco al-deco-2" aria-hidden="true"></div>
            <div class="al-dots" aria-hidden="true"></div>

            <div class="al-card">
                <div class="al-brand">
                    <img class="al-brand-logo" src="{{ $shopLogo }}" alt="{{ $shopName }}">
                    <p class="al-brand-name">{{ $shopName }}</p>
                </div>

                <div class="al-welcome">
                    <h1>Welcome Back</h1>
                    <p>Sign in to access your Admin Dashboard.</p>
                </div>

                @if ($errors->has('email'))
                    <div class="al-alert al-alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="al-alert al-alert-success" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form id="login-form" action="{{ route('post-login') }}" method="POST" novalidate>
                    @csrf

                    <div class="al-field">
                        <span class="al-ico" aria-hidden="true"><i class="bi bi-envelope"></i></span>
                        <input type="text" name="email" id="email" class="al-input"
                            value="{{ old('email') }}" placeholder=" " autocomplete="username" required>
                        <label for="email">Email or contact number</label>
                    </div>

                    <div class="al-field al-field-pass">
                        <span class="al-ico" aria-hidden="true"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="al-input"
                            placeholder=" " autocomplete="current-password" required>
                        <label for="password">Password</label>
                        <button type="button" class="al-eye" id="TogglePassword" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="al-row">
                        <label class="al-check">
                            <input type="checkbox" name="remember" value="1">
                            <span>Remember me</span>
                        </label>
                        <a class="al-link" href="{{ route('admin.forgot-password') }}">Forgot password?</a>
                    </div>

                    <button type="submit" class="al-submit" id="LoginSubmit">
                        Sign In
                    </button>
                </form>
            </div>
        </section>
    </div>

    <script>
        (function () {
            var form = document.getElementById('login-form');
            var loader = document.getElementById('LoginLoader');
            var toggle = document.getElementById('TogglePassword');
            var password = document.getElementById('password');

            if (toggle && password) {
                toggle.addEventListener('click', function () {
                    var show = password.type === 'password';
                    password.type = show ? 'text' : 'password';
                    toggle.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
                    toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            }

            // Keep floating labels correct after autofill
            document.querySelectorAll('.al-input').forEach(function (input) {
                var sync = function () {
                    if (input.value) {
                        input.classList.add('has-value');
                    } else {
                        input.classList.remove('has-value');
                    }
                };
                sync();
                input.addEventListener('input', sync);
                input.addEventListener('blur', sync);
            });
            setTimeout(function () {
                document.querySelectorAll('.al-input').forEach(function (input) {
                    if (input.value) input.classList.add('has-value');
                });
            }, 150);

            if (form && loader) {
                form.addEventListener('submit', function () {
                    loader.classList.add('is-active');
                });
            }
        })();
    </script>
</body>

</html>
