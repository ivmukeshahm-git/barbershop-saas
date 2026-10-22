<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super admin sign in | BarberPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/super-admin.css') }}?v=2" rel="stylesheet">
</head>
<body class="pa-body">
<main class="pa-login-page">
    <section class="pa-login-art">
        <div class="pa-login-copy">
            <p class="pa-eyebrow">Platform control center</p>
            <h1>Every shop.<br>One clear view.</h1>
            <p>Manage BarberPro shop accounts, subscription payments, plans and public website settings from one secure workspace.</p>
        </div>
    </section>
    <section class="pa-login-card-wrap">
        <div class="pa-login-card">
            <div class="pa-login-brand"><i class="bi bi-scissors"></i> BARBERPRO <span style="color:#999;font-weight:500">/ SUPER ADMIN</span></div>
            <h2>Welcome back</h2>
            <p>Sign in with your platform administrator account.</p>
            @if ($errors->any())
                <div class="pa-alert pa-alert-danger" role="alert">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('platform.login.submit') }}">
                @csrf
                <div class="pa-field">
                    <label for="email">Administrator email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                </div>
                <div class="pa-field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <div class="pa-field">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:500">
                        <input type="checkbox" name="remember" value="1" style="width:auto"> Keep me signed in
                    </label>
                </div>
                <button class="pa-button" type="submit"><i class="bi bi-shield-lock"></i> Sign in securely</button>
            </form>
            <div class="pa-login-help"><i class="bi bi-lock"></i> Restricted to authorized platform administrators.</div>
        </div>
    </section>
</main>
</body>
</html>
