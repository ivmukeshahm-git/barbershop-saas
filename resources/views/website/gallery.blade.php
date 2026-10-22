@extends('layouts.master')

@section('title', 'Gallery | Rosenthal Barbershop')
@section('body_class', 'inner-page')

@section('content')
<section class="lx-page-banner" style="background-image:url('{{ asset('frontend/images/luxury/page-banner.jpg') }}')">
    <div class="banner-inner">
        <h1>Gallery</h1>
        <ul class="lx-breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>/</li>
            <li>Gallery</li>
        </ul>
    </div>
</section>

<section class="section-pad bg-soft-lx">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-eyebrow">Portfolio</span>
            <h2 class="section-title">Cuts & Style Showcase</h2>
            <p class="section-sub">Browse our latest work — sharp fades, classic styles, and refined finishes.</p>
        </div>

        <div class="lx-gallery" data-aos="fade-up">
            @php $fallback = ['cut.jpg','beard.jpg','style.jpg','shave.jpg','wash.jpg','chair.jpg','gallery-1.jpg','gallery-2.jpg','interior.jpg']; @endphp

            @forelse($gallary as $image)
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
