@extends('layouts.master')





@section('title', 'About Us | Rosenthal Barbershop')


@section('body_class', 'inner-page')





@section('content')


<section class="lx-page-banner" style="background-image:url('{{ asset('frontend/images/luxury/page-banner.jpg') }}')">
    <div class="banner-inner">
        <h1>About Us</h1>

        <ul class="lx-breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li>About</li>
        </ul>
    </div>
</section>





<section class="section-pad bg-white-lx">


    <div class="container">


        <div class="row align-items-center g-5">


            <div class="col-lg-6" data-aos="zoom-in">


                <div class="lx-collage">
                    <img src="{{ asset('frontend/images/luxury/about-1.jpg') }}" alt="About Rosenthal Barbershop" class="img-main">
                    <img src="{{ asset('frontend/images/luxury/about-3.jpg') }}" alt="Interior" class="img-float">


                    <div class="exp-badge">
                        <strong>25+</strong>
                        <span>Years</span>
                    </div>
                </div>


            </div>


            <div class="col-lg-6" data-aos="fade-up">


                <span class="section-eyebrow">About Us</span>


                <h2 class="section-title">Rosenthal Barbershop</h2>


                <p>At Rosenthal Barbershop, we provide a professional barbering experience that combines traditional techniques with modern styling. Each service is delivered with precision, consistency, and careful attention to the individual needs of men of all ages.</p>


                <p>Our services cover the full range: modern style haircuts, sharp fades, precise line-ups, classic cuts, expert beard sculpting, traditional hot towel shaves, waxing, facial detox treatments, hair and beard darkening, and carefully finished styling. We also welcome younger clients with professional kids’ haircuts in a family-friendly environment that feels welcoming from the first visit.</p>


                <p>The shop is built on three clear principles: skill, consistency, and respect for the craft. Quality products, high standards, and real attention to the person in the chair guide every appointment. Owned and operated by Kyle and Mike, Rosenthal Barbershop reflects their shared commitment to getting each cut exactly right. Walk-ins are always welcome.</p>


                <p>Whether you’re after a fresh modern look, a timeless classic, or bringing your son in for his next cut, you’ll leave looking sharper and feeling taken care of.</p>


                <p class="mb-4" style="font-weight:700;color:var(--lx-navy);letter-spacing:.02em;">Precision. Style. For Every Generation.</p>


                <a href="{{ route('book') }}" class="btn-lx btn-lx-primary" target="_blank">Book Your Visit</a>


            </div>


        </div>


    </div>


</section>





<section class="section-pad bg-soft-lx">


    <div class="container">


        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Our Principles</span>
            <h2 class="section-title">What Drives Us</h2>
            <p class="section-sub">Three clear principles guide every appointment at Rosenthal Barbershop.</p>
        </div>


        <div class="row g-4">


            @foreach([


                ['fa-cut', 'Skill', 'Every cut is delivered with precision — modern fades, classic styles, and finishing that lasts.'],


                ['fa-sync-alt', 'Consistency', 'High standards, quality products, and the same careful attention on every visit.'],


                ['fa-handshake', 'Respect for the Craft', 'Real attention to the person in the chair, rooted in tradition and done right.'],


            ] as $i => $v)


            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">


                <div class="lx-card lx-feature-card">


                    <div class="lx-icon-circle"><i class="fas {{ $v[0] }}"></i></div>


                    <h3>{{ $v[1] }}</h3>


                    <p>{{ $v[2] }}</p>


                </div>


            </div>


            @endforeach


        </div>





        <div class="lx-counters mt-5" data-aos="fade-up">
            <div class="row">
                <div class="col-6 col-md-3"><div class="counter-item"><strong data-count="4" data-suffix="+">0</strong><span>Expert Barbers</span></div></div>
                <div class="col-6 col-md-3"><div class="counter-item"><strong data-count="3000" data-suffix="+">0</strong><span>Happy Clients</span></div></div>
                <div class="col-6 col-md-3"><div class="counter-item"><strong data-count="25" data-suffix="+">0</strong><span>Years Experience</span></div></div>
                <div class="col-6 col-md-3"><div class="counter-item"><strong data-count="4.9">0</strong><span>Rating</span></div></div>
            </div>
        </div>


    </div>


</section>





<section class="section-pad bg-white-lx" id="gallery">


    <div class="container">


        <div class="section-head" data-aos="fade-up">


            <span class="section-eyebrow">Portfolio</span>


            <h2 class="section-title">Cuts & Style Showcase</h2>


            <p class="section-sub">Browse our latest work — sharp fades, classic styles, and refined finishes.</p>


        </div>





        <div class="lx-gallery" data-aos="fade-up">


            @php $fallback = ['cut.jpg','beard.jpg','style.jpg','shave.jpg','wash.jpg','chair.jpg','gallery-1.jpg','gallery-2.jpg','interior.jpg']; @endphp





            @forelse($gallary ?? [] as $image)


                <div class="lx-gallery-item gallery-trigger">


                    <img src="{{ asset('storage/'.$image->image) }}" alt="Gallery work" class="gallery-img">


                    <div class="g-overlay"><i class="fas fa-search-plus fa-lg"></i></div>


                </div>


            @empty


                @foreach($fallback as $f)


                <div class="lx-gallery-item gallery-trigger">


                    <img src="{{ asset('frontend/images/luxury/'.$f) }}" alt="Gallery work" class="gallery-img">


                    <div class="g-overlay"><i class="fas fa-search-plus fa-lg"></i></div>


                </div>


                @endforeach


            @endforelse


        </div>


    </div>


</section>





<div class="lx-lightbox" id="lxLightbox">


    <button type="button" class="close-lb" id="lbClose" aria-label="Close">&times;</button>


    <img src="" alt="Preview" id="lbImage">


</div>


@endsection





@push('scripts')


<script>


(function () {


    const lb = document.getElementById('lxLightbox');


    const lbImg = document.getElementById('lbImage');


    const close = document.getElementById('lbClose');


    if (!lb || !lbImg || !close) return;


    document.querySelectorAll('.gallery-trigger').forEach(item => {


        item.addEventListener('click', () => {


            const img = item.querySelector('img');


            if (!img) return;


            lbImg.src = img.src;


            lb.classList.add('show');


            document.body.style.overflow = 'hidden';


        });


    });


    function hide() {


        lb.classList.remove('show');


        document.body.style.overflow = '';


    }


    close.addEventListener('click', hide);


    lb.addEventListener('click', e => { if (e.target === lb) hide(); });


    document.addEventListener('keydown', e => { if (e.key === 'Escape') hide(); });


})();


</script>


@endpush


