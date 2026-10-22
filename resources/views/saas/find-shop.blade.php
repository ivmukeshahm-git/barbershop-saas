@extends('saas.layout')

@section('title', 'Login')
@section('meta_description', 'Find your BarberPro shop admin panel by entering your shop URL slug.')

@section('content')
<section class="bp-page bp-page-light">
    <div class="bp-container bp-find-wrap">
        <header class="bp-section-head" data-reveal>
            <p class="bp-eyebrow">Shop login</p>
            <h2>Open your BarberPro admin</h2>
            <p class="bp-lead">Enter your shop slug to continue to <strong>/{slug}/admin/login</strong>.</p>
        </header>

        <div class="bp-find-card" data-reveal>
            @if ($errors->any())
                <div class="bp-alert bp-alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('saas.find-shop.resolve') }}">
                @csrf
                <div class="bp-field">
                    <label for="shop">Shop slug *</label>
                    <div class="bp-field-prefix">
                        <span>/</span>
                        <input id="shop" name="shop" type="text" value="{{ old('shop') }}" required maxlength="80" placeholder="your-shop" autofocus>
                    </div>
                    <p class="bp-field-hint">Example admin URL: <code>{{ url('/your-shop/admin/login') }}</code></p>
                </div>
                <button type="submit" class="bp-btn bp-btn-primary bp-btn-lg bp-btn-block">Continue to login</button>
            </form>

            <p class="bp-find-foot">
                New shop?
                <a href="{{ route('saas.pricing') }}">View pricing</a>
                ·
                <a href="{{ route('saas.signup') }}">Get started</a>
            </p>
        </div>
    </div>
</section>
@endsection
