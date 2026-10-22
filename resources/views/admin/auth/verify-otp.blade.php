<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $shopName ?? ($currentShop->name ?? 'Infotech') }} | Verify code</title>
    <meta name="description" content="Enter the verification code sent to your email." />
    <link rel="icon" type="image/webp" href="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" />
    <link href="{{ asset('assets/vendor/auth/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/auth/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/css/admin-auth-fonts.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/css/admin-login.css') }}?v=2" rel="stylesheet" />
    @include('admin.partials.shop-theme')
</head>

<body class="al-page">
    <div class="al-loader" id="AuthLoader" aria-live="polite" aria-busy="true">
        <div class="al-loader-inner">
            <img class="al-loader-logo" src="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" alt="{{ $shopName ?? ($currentShop->name ?? 'Infotech') }}">
            <div class="al-spinner" aria-hidden="true"></div>
            <h3 class="al-loader-title" id="AuthLoaderTitle">Verifying code...</h3>
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
                <h2>Confirm it’s really you.</h2>
                <p class="lead">Enter the 6-digit code we sent to your registered email to continue resetting your password.</p>
                <div class="al-stats">
                    <div class="al-stat">
                        <strong>6 Digits</strong>
                        <span>One-time Code</span>
                    </div>
                    <div class="al-stat">
                        <strong>{{ (int) ($expireMinutes ?? 10) }} Min</strong>
                        <span>Expiry Window</span>
                    </div>
                    <div class="al-stat">
                        <strong>Secure</strong>
                        <span>Single-use verification code</span>
                    </div>
                    <div class="al-stat">
                        <strong>Private</strong>
                        <span>Never Share Codes</span>
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
                    <p class="al-brand-name">Rosenthal<br>Barbershop</p>
                </div>

                <div class="al-welcome">
                    <h1>Verify your code</h1>
                    <p>Enter the 6-digit verification code sent to <strong>{{ $maskedEmail }}</strong>.</p>
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

                <div class="al-otp-timer" id="otpTimerWrap" data-expires="{{ (int) ($expiresAt ?? 0) }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Code expires in <strong id="otpCountdown">--:--</strong></span>
                </div>

                <form id="otp-form" action="{{ route('admin.forgot-password.verify-otp.submit') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="otp" id="otp" value="{{ old('otp') }}">

                    <div class="al-otp-group" role="group" aria-label="One-time password">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" class="al-otp-digit" inputmode="numeric" autocomplete="one-time-code"
                                maxlength="1" pattern="[0-9]*" data-index="{{ $i }}" aria-label="Digit {{ $i + 1 }}" />
                        @endfor
                    </div>

                    <button class="al-submit" id="otpSubmitBtn" type="submit">
                        <span class="al-submit-text">Verify code</span>
                        <i class="bi bi-arrow-right al-submit-arrow"></i>
                    </button>
                </form>

                <div class="al-resend-row">
                    <form id="resend-form" action="{{ route('admin.forgot-password.resend-otp') }}" method="POST">
                        @csrf
                        <button type="submit" class="al-resend-btn" id="resendBtn">
                            Resend verification code
                        </button>
                    </form>
                </div>

                <div class="al-auth-links">
                    <a href="{{ route('admin.forgot-password') }}" class="al-back-link">
                        <i class="bi bi-arrow-left"></i> Change Email
                    </a>
                    <a href="{{ route('login') }}" class="al-back-link">
                        Back to Login
                    </a>
                </div>

                <div class="al-footer">
                    <div>© {{ date('Y') }} {{ $shopName ?? ($currentShop->name ?? 'Infotech') }}</div>
                    <div>Powered by <strong>IV Infotech</strong></div>
                </div>
            </div>
        </section>
    </div>

    <script>
        (function() {
            var digits = Array.prototype.slice.call(document.querySelectorAll(".al-otp-digit"));
            var hidden = document.getElementById("otp");
            var form = document.getElementById("otp-form");
            var btn = document.getElementById("otpSubmitBtn");
            var loader = document.getElementById("AuthLoader");
            var loaderTitle = document.getElementById("AuthLoaderTitle");
            var timerWrap = document.getElementById("otpTimerWrap");
            var countdownEl = document.getElementById("otpCountdown");
            var expiresAt = parseInt(timerWrap.getAttribute("data-expires"), 10) || 0;

            function syncHidden() {
                hidden.value = digits.map(function(el) { return el.value.replace(/\D/g, ""); }).join("");
            }

            function fillFromString(value) {
                var clean = String(value || "").replace(/\D/g, "").slice(0, 6);
                digits.forEach(function(el, idx) {
                    el.value = clean.charAt(idx) || "";
                });
                syncHidden();
                if (clean.length >= 6) {
                    digits[5].focus();
                } else if (clean.length > 0) {
                    digits[clean.length].focus();
                }
            }

            if (hidden.value) {
                fillFromString(hidden.value);
            }

            digits.forEach(function(input, index) {
                input.addEventListener("input", function() {
                    var val = this.value.replace(/\D/g, "");
                    if (val.length > 1) {
                        fillFromString(val);
                        return;
                    }
                    this.value = val;
                    syncHidden();
                    if (val && index < digits.length - 1) {
                        digits[index + 1].focus();
                    }
                });

                input.addEventListener("keydown", function(e) {
                    if (e.key === "Backspace" && !this.value && index > 0) {
                        digits[index - 1].focus();
                    }
                });

                input.addEventListener("paste", function(e) {
                    e.preventDefault();
                    var text = (e.clipboardData || window.clipboardData).getData("text");
                    fillFromString(text);
                });

                input.addEventListener("focus", function() {
                    this.select();
                });
            });

            form.addEventListener("submit", function(e) {
                syncHidden();
                if (!/^\d{6}$/.test(hidden.value)) {
                    e.preventDefault();
                    alert("Please enter the complete 6-digit verification code.");
                    return;
                }
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span>Verifying code...</span>';
                loaderTitle.textContent = "Verifying code...";
                loader.classList.add("is-active");
            });

            document.getElementById("resend-form").addEventListener("submit", function() {
                loaderTitle.textContent = "Sending a new verification code...";
                loader.classList.add("is-active");
                document.getElementById("resendBtn").disabled = true;
            });

            function tick() {
                var now = Math.floor(Date.now() / 1000);
                var remaining = Math.max(0, expiresAt - now);
                var mins = Math.floor(remaining / 60);
                var secs = remaining % 60;
                countdownEl.textContent = String(mins).padStart(2, "0") + ":" + String(secs).padStart(2, "0");
                if (remaining <= 0) {
                    timerWrap.classList.add("is-expired");
                    countdownEl.textContent = "Expired";
                    return;
                }
                requestAnimationFrame(function() {
                    setTimeout(tick, 250);
                });
            }

            if (expiresAt > 0) {
                tick();
            } else {
                countdownEl.textContent = "--:--";
            }

            if (digits[0]) {
                digits[0].focus();
            }
        })();
    </script>
</body>

</html>
