/**
 * Blue Crown Barbers — branding interactions
 * Logo swap, scroll scale class, CountUp counters
 */
(function () {
  'use strict';

  const header = document.getElementById('lxHeader');
  const logo = document.getElementById('lxBrandLogo');
  const toTop = document.getElementById('toTop');
  const onInner = document.body.classList.contains('inner-page');

  function syncLogo() {
    if (!logo || !header) return;
    const light = logo.getAttribute('data-logo-light');
    const dark = logo.getAttribute('data-logo-dark');
    const useLight = header.classList.contains('scrolled') || header.classList.contains('solid') || onInner;
    const next = useLight ? light : dark;
    if (next && logo.getAttribute('src') !== next) {
      logo.setAttribute('src', next);
    }
  }

  function onScroll() {
    if (!header) return;
    if (window.scrollY > 40 || onInner) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
    if (onInner) header.classList.add('solid');
    if (toTop) toTop.classList.toggle('show', window.scrollY > 400);
    syncLogo();
  }

  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
  if (toTop) {
    toTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* CountUp */
  function animateCount(el) {
    const raw = el.getAttribute('data-count');
    if (raw == null) return;
    const target = parseFloat(raw);
    const suffix = el.getAttribute('data-suffix') || '';
    const decimals = (String(raw).split('.')[1] || '').length;
    const duration = 1600;
    const start = performance.now();

    function frame(now) {
      const t = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - t, 3);
      const value = target * eased;
      el.textContent = value.toFixed(decimals) + suffix;
      if (t < 1) requestAnimationFrame(frame);
      else el.textContent = (decimals ? target.toFixed(decimals) : String(Math.round(target))) + suffix;
    }
    requestAnimationFrame(frame);
  }

  const counters = document.querySelectorAll('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        animateCount(entry.target);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.4 });
    counters.forEach(function (el) { io.observe(el); });
  } else {
    counters.forEach(animateCount);
  }

  if (window.AOS) {
    AOS.init({
      duration: 800,
      once: true,
      offset: 80,
      easing: 'ease-out-cubic',
      disable: function () {
        return window.innerWidth < 992;
      }
    });
  }
})();
