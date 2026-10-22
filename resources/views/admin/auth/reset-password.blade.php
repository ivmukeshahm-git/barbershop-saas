<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $shopName ?? ($currentShop->name ?? 'Infotech') }} | Create New Password</title>
    <meta name="description" content="Create a strong new password for your admin account." />
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
            <h3 class="al-loader-title">Updating password...</h3>
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
                <h2>Create a strong new password.</h2>
                <p class="lead">Choose a secure password to protect your {{ $shopName ?? ($currentShop->name ?? 'Infotech') }} admin dashboard.</p>
                <div class="al-stats">
                    <div class="al-stat">
                        <strong>Strong</strong>
                        <span>Password Policy</span>
                    </div>
                    <div class="al-stat">
                        <strong>Hashed</strong>
                        <span>Secure Storage</span>
                    </div>
                    <div class="al-stat">
                        <strong>Private</strong>
                        <span>Never Share It</span>
                    </div>
                    <div class="al-stat">
                        <strong>Ready</strong>
                        <span>Login After Reset</span>
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
                    <h1>Create New Password</h1>
                    <p>Create a strong new password for your admin account.</p>
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

                <form id="reset-form" action="{{ route('admin.forgot-password.update') }}" method="POST" novalidate
                    data-min-length="{{ (int) ($passwordMinLength ?? 6) }}">
                    @csrf

                    <div class="al-field">
                        <i class="bi bi-lock al-ico" aria-hidden="true"></i>
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder=" " autocomplete="new-password" required />
                        <label for="password">New Password</label>
                        <button type="button" class="al-toggle-pass" data-target="password" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="al-strength" id="passwordStrength" aria-live="polite">
                        <div class="al-strength-bar"><span></span></div>
                        <div class="al-strength-label">Password strength: <strong>Too weak</strong></div>
                        <ul class="al-strength-hints">
                            <li data-rule="length">At least {{ (int) ($passwordMinLength ?? 6) }} characters</li>
                            <li data-rule="letter">Contains a letter</li>
                            <li data-rule="number">Contains a number</li>
                            <li data-rule="case">Upper &amp; lowercase recommended</li>
                        </ul>
                    </div>

                    <div class="al-field">
                        <i class="bi bi-shield-lock al-ico" aria-hidden="true"></i>
                        <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
                            placeholder=" " autocomplete="new-password" required />
                        <label for="password_confirmation">Confirm Password</label>
                        <button type="button" class="al-toggle-pass" data-target="password_confirmation" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <button class="al-submit" id="resetSubmitBtn" type="submit">
                        <span class="al-submit-text">Reset Password</span>
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

    <script>
        (function() {
            var form = document.getElementById("reset-form");
            var password = document.getElementById("password");
            var confirm = document.getElementById("password_confirmation");
            var btn = document.getElementById("resetSubmitBtn");
            var loader = document.getElementById("AuthLoader");
            var strength = document.getElementById("passwordStrength");
            var minLength = parseInt(form.getAttribute("data-min-length"), 10) || 6;

            document.querySelectorAll(".al-toggle-pass").forEach(function(toggle) {
                toggle.addEventListener("click", function() {
                    var input = document.getElementById(this.getAttribute("data-target"));
                    var icon = this.querySelector("i");
                    var show = input.type === "password";
                    input.type = show ? "text" : "password";
                    icon.className = show ? "bi bi-eye-slash" : "bi bi-eye";
                    this.setAttribute("aria-label", show ? "Hide password" : "Show password");
                });
            });

            function markFilled(el) {
                el.classList.toggle("has-value", !!el.value);
            }

            [password, confirm].forEach(function(el) {
                el.addEventListener("input", function() {
                    markFilled(el);
                    if (el === password) {
                        updateStrength(el.value);
                    }
                });
                markFilled(el);
            });

            function updateStrength(value) {
                var score = 0;
                var rules = {
                    length: value.length >= minLength,
                    letter: /[A-Za-z]/.test(value),
                    number: /\d/.test(value),
                    case: /[a-z]/.test(value) && /[A-Z]/.test(value)
                };

                Object.keys(rules).forEach(function(key) {
                    var item = strength.querySelector('[data-rule="' + key + '"]');
                    if (item) {
                        item.classList.toggle("is-met", !!rules[key]);
                    }
                    if (rules[key]) {
                        score += 1;
                    }
                });

                var label = "Too weak";
                var level = "weak";
                if (score >= 4) {
                    label = "Strong";
                    level = "strong";
                } else if (score === 3) {
                    label = "Good";
                    level = "good";
                } else if (score === 2) {
                    label = "Fair";
                    level = "fair";
                }

                strength.setAttribute("data-level", level);
                strength.querySelector(".al-strength-label strong").textContent = label;
            }

            updateStrength(password.value);

            form.addEventListener("submit", function(e) {
                if (password.value.length < minLength) {
                    e.preventDefault();
                    alert("Password must be at least " + minLength + " characters.");
                    return;
                }
                if (!/[A-Za-z]/.test(password.value) || !/\d/.test(password.value)) {
                    e.preventDefault();
                    alert("Password must include at least one letter and one number.");
                    return;
                }
                if (password.value !== confirm.value) {
                    e.preventDefault();
                    alert("Confirm password does not match.");
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span>Resetting...</span>';
                loader.classList.add("is-active");
            });
        })();
    </script>
</body>

</html>
