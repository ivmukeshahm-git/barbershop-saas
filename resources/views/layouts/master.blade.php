<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $shopName ?? 'Misha Hair Studio Mumbai' }} — professional haircuts and grooming tailored to your style.">
    <meta name="keywords" content="{{ $shopName ?? 'Misha Hair Studio Mumbai' }}, haircut, barber, grooming">
    <meta name="author" content="{{ $shopName ?? 'Misha Hair Studio Mumbai' }}">
    <meta property="og:site_name" content="{{ $shopName ?? 'Misha Hair Studio Mumbai' }}">
    <meta property="og:title" content="@yield('title', ($shopName ?? 'Misha Hair Studio Mumbai') . ' | Hair Studio')">
    <meta property="og:description" content="Professional haircuts and grooming, tailored to your style and delivered with care.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('assets/img/logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', ($shopName ?? 'Misha Hair Studio Mumbai') . ' | Hair Studio')">
    <meta name="twitter:description" content="Professional haircuts and grooming, tailored to your style and delivered with care.">
    <title>@yield('title', ($shopName ?? 'Misha Hair Studio Mumbai') . ' | Hair Studio')</title>
    <script>window.SHOP_CURRENCY_SYMBOL = @json($shopCurrencySymbol ?? '₹');</script>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo.png') }}" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css?id=5" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('frontend/css/luxury.css?id=5') }}?v=9" rel="stylesheet">
    <link href="{{ asset('frontend/css/brand-animations.css?id=5') }}?v=4" rel="stylesheet">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BarberShop",
      "name": "{{ $shopName ?? 'Misha Hair Studio Mumbai' }}",
      "image": "{{ asset('assets/img/logo.png') }}",
      "url": "{{ url('/') }}",
      "telephone": "{{ optional($siteSetting)->phoneTel() ?: '+15875552489' }}",
      "email": "{{ $siteSetting->contact_email ?? '' }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $siteSetting->contact_address ?? '' }}"
      },
      "openingHoursSpecification": [
        {"@type":"OpeningHoursSpecification","dayOfWeek":["Monday","Tuesday","Wednesday","Thursday","Friday"],"opens":"09:00","closes":"20:00"},
        {"@type":"OpeningHoursSpecification","dayOfWeek":"Saturday","opens":"09:00","closes":"19:00"},
        {"@type":"OpeningHoursSpecification","dayOfWeek":"Sunday","opens":"10:00","closes":"17:00"}
      ]
    }
    </script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17733466405"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-17733466405');
    </script>

    @stack('styles')
</head>
<body class="@yield('body_class')">

    @include('layouts.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    <a href="{{ optional($siteSetting)->whatsappUrl() ?: 'https://wa.me/15875552489' }}" class="whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <button type="button" class="to-top-lx" id="toTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="{{ asset('frontend/js/brand.js') }}?v=2"></script>
    @stack('scripts')
</body>
</html>
