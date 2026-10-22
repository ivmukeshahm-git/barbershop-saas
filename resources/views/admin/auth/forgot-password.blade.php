<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $shopName ?? ($currentShop->name ?? 'Infotech') }} | Forgot Password</title>
    <meta name="description" content="Reset your {{ $shopName ?? ($currentShop->name ?? 'Infotech') }} Admin account password." />
    <link rel="icon" type="image/webp" href="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" />
    <link href="{{ asset('assets/vendor/auth/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/auth/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/css/admin-login.css') }}?v=2" rel="stylesheet" />
    @include('admin.partials.shop-theme')
</head>

<body class="al-page">
    <div class="al-loader" id="AuthLoader" aria-live="polite" aria-busy="true">
        <div class="al-loader-inner">
            <img class="al-loader-logo" src="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" alt="{{ $shopName ?? ($currentShop->name ?? 'Infotech') }}">
            <div class="al-spinner" aria-hidden="true"></div>
            <h3 class="al-loader-title">Sending verification code...</h3>
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
                <h2>Secure password recovery for your admin account.</h2>
                <p class="lead">Verify your email, confirm with a one-time code, and create a new password — safely and quickly.</p>
                <div class="al-stats">
                    <div class="al-stat">
                        <strong>Code</strong>
                        <span>Email Verification</span>
                    </div>
                    <div class="al-stat">
                        <strong>Secure</strong>
                        <span>Encrypted Reset</span>
                    </div>
                    <div class="al-stat">
                        <strong>Fast</strong>
                        <span>3 Simple Steps</span>
                    </div>
                    <div class="al-stat">
                        <strong>Safe</strong>
                        <span>Time-limited Codes</span>
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
                    <img class="al-brand-logo" src="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" alt="{{ $shopName ?? ($currentShop->name ?? 'Infotech') }}">
                    <p class="al-brand-name">{{ $shopName ?? ($currentShop->name ?? 'Infotech') }}</p>
                </div>

                <div class="al-welcome">
                    <h1>Forgot Password?</h1>
                    <p>Enter your registered email address to continue.</p>
                </div>

                @if ($errors->any())
                    <div class="al-alert al-alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="al-alert al-alert-success" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form id="forgot-form" action="{{ route('admin.forgot-password.submit') }}" method="POST" novalidate>
                    @csrf
                    <div class="al-field">
                        <i class="bi bi-envelope al-ico" aria-hidden="true"></i>
                        <input type="email" class="form-control{{ old('email') ? ' has-value' : '' }}" id="email"
                            name="email" value="{{ old('email') }}" placeholder=" " autocomplete="email" required />
                        <label for="email">Email</label>
                    </div>

                    <button class="al-submit" id="forgotSubmitBtn" type="submit">
                        <span class="al-submit-text">Send verification code</span>
                        <i class="bi bi-arrow-right al-submit-arrow"></i>
                    </button>
                </form>

                <div class="al-auth-links">
                    <a href="{{ route('login') }}" class="al-back-link">
                        <i class="bi bi-arrow-left"></i> Back to Login
                    </a>
                </div>

                <div class="al-footer">
                    <div>© {{ date('Y') }} {{ $shopName ?? ($currentShop->name ?? 'Infotech') }}</div>
                    <div>Powered by <strong>IV Infotech</strong></div>
                </div>
            </div>
        </section>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script>
        (function() {
            var $form = $("#forgot-form");
            var $btn = $("#forgotSubmitBtn");
            var $loader = $("#AuthLoader");
            var submitButtonHtml = $btn.html();

            $form.find(".form-control").on("input blur change", function() {
                $(this).toggleClass("has-value", !!this.value);
            });

            $form.on("submit", function() {
                $btn.prop("disabled", true);
                $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span>Sending verification code...</span>');
                $loader.addClass("is-active");
            });

            window.addEventListener("pageshow", function() {
                $loader.removeClass("is-active");
                $btn.prop("disabled", false).html(submitButtonHtml);
            });
        })();
    </script>
</body>

</html>
