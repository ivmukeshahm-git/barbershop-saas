@php
    $about = $siteSetting->footer_about ?? 'At Rosenthal Barbershop, we combine traditional techniques with modern styling — precision, consistency, and care for every generation. Walk-ins welcome.';
    $address = $siteSetting->contact_address ?? '245 King Street, Downtown, Toronto, Ontario, Canada';
    $phone = $siteSetting->contact_phone ?? '+1 (587) 555-2489';
    $phoneTel = optional($siteSetting)->phoneTel() ?: '+15875552489';
    $emailInfo = $siteSetting->contact_email ?? 'info@bluecrownbarbers.com';
    $emailSupport = $siteSetting->support_email ?? 'support@bluecrownbarbers.com';
    $facebook = $siteSetting->facebook_url ?? 'https://www.facebook.com/bluecrownbarbers';
    $instagram = $siteSetting->instagram_url ?? 'https://www.instagram.com/bluecrownbarbers';
    $tiktok = $siteSetting->tiktok_url ?? 'https://www.tiktok.com/@bluecrownbarbers';
    $youtube = $siteSetting->youtube_url ?? 'https://www.youtube.com/@bluecrownbarbers';
    $twitter = $siteSetting->twitter_url ?? 'https://twitter.com/bluecrownbarbers';
    $linkedin = $siteSetting->linkedin_url ?? 'https://www.linkedin.com/company/bluecrownbarbers';

    $socials = array_filter([
        ['url' => $facebook, 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
        ['url' => $instagram, 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        ['url' => $tiktok, 'icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
        ['url' => $youtube, 'icon' => 'fab fa-youtube', 'label' => 'YouTube'],
        ['url' => $twitter, 'icon' => 'fab fa-twitter', 'label' => 'Twitter'],
        ['url' => $linkedin, 'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'],
    ]);

    $footerServices = ['Hair Cut & Styling', 'Beard Trim', 'Hot Towel Shave', 'Hair Coloring', 'Kids Haircut'];
@endphp

<footer class="lx-footer">
    <div class="container">

        {{-- Top: brand promise + CTAs --}}
        <div class="lx-footer-top">
            <div class="lx-footer-top-copy">
                <span class="lx-footer-badge"><i class="fas fa-scissors"></i> Walk-ins Welcome</span>
                <h2>Sharp fades, classic cuts, beard grooming — crafted for every generation.</h2>
                <p>Book your chair in seconds or drop by the shop. We keep the craft traditional and the experience modern.</p>
            </div>
            <div class="lx-footer-cta">
                <a href="{{ route('book') }}" class="btn-lx btn-lx-primary">Book Appointment <i class="fas fa-arrow-right"></i></a>
                <a href="{{ route('services') }}" class="btn-lx btn-lx-outline">View Services</a>
            </div>
        </div>

        {{-- Columns --}}
        <div class="lx-footer-grid">

            {{-- Brand + about + social --}}
            <div>
                <div class="logo-foot">
                    <img src="{{ asset('assets/img/logo.png?id=1') }}" alt="Rosenthal Barbershop">
                    <span class="lx-footer-brand">Rosenthal<br>Barbershop</span>
                </div>
                <p class="lx-footer-about">{{ $about }}</p>
                @if($socials)
                    <div class="lx-social lx-footer-social">
                        @foreach($socials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener" aria-label="{{ $social['label'] }}"><i class="{{ $social['icon'] }}"></i></a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick links --}}
            <div>
                <h4>Quick Links</h4>
                <ul class="lx-footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('services') }}">Services</a></li>
                    <li><a href="{{ route('barbers') }}">Our Barbers</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('book') }}">Book Online</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <h4>Services</h4>
                <ul class="lx-footer-links">
                    @foreach($footerServices as $service)
                        <li><a href="{{ route('services') }}">{{ $service }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact + newsletter --}}
            <div>
                <h4>Get In Touch</h4>
                <ul class="lx-footer-contact">
                    <li>
                        <span class="lx-ic"><i class="fas fa-map-marker-alt"></i></span>
                        <span class="lx-tx">{!! nl2br(e($address)) !!}</span>
                    </li>
                    <li>
                        <span class="lx-ic"><i class="fas fa-phone"></i></span>
                        <span class="lx-tx"><a href="tel:{{ $phoneTel }}">{{ $phone }}</a></span>
                    </li>
                    <li>
                        <span class="lx-ic"><i class="fas fa-envelope"></i></span>
                        <span class="lx-tx"><a href="mailto:{{ $emailInfo }}">{{ $emailInfo }}</a></span>
                    </li>
                    @if($emailSupport)
                        <li>
                            <span class="lx-ic"><i class="fas fa-life-ring"></i></span>
                            <span class="lx-tx"><a href="mailto:{{ $emailSupport }}">{{ $emailSupport }}</a></span>
                        </li>
                    @endif
                </ul>

                <div class="lx-newsletter">
                    <h4>Newsletter</h4>
                    <form class="lx-news-form" onsubmit="event.preventDefault(); this.reset(); this.querySelector('.lx-news-msg').textContent = 'Thanks for joining! We will be in touch soon.';">
                        <input type="email" class="form-control" placeholder="Your email address" aria-label="Email address" required>
                        <button type="submit" class="btn">Join</button>
                    </form>
                    <p class="lx-news-note lx-news-msg">No spam — just offers and shop news.</p>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="lx-footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <p class="mb-0">&copy; {{ date('Y') }} Rosenthal Barbershop. All rights reserved.</p>
            <div class="lx-footer-bottom-links">
                <a href="{{ route('contact') }}">Privacy</a>
                <a href="{{ route('contact') }}">Terms</a>
                <a href="https://ivinfotech.com/" target="_blank" rel="noopener">Designed by IV Infotech</a>
            </div>
        </div>
    </div>
</footer>
