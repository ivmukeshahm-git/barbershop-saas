(function () {
  'use strict';

  var nav = document.getElementById('bpNav');
  var toggle = document.getElementById('bpNavToggle');
  var menu = document.getElementById('bpNavLinks');

  function onScroll() {
    if (!nav) return;
    if (window.scrollY > 24) {
      nav.classList.add('is-scrolled');
    } else {
      nav.classList.remove('is-scrolled');
    }
  }

  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  function closeMenu() {
    if (!menu || !menu.classList.contains('is-open')) return;
    menu.classList.remove('is-open');
    if (toggle) {
      toggle.setAttribute('aria-expanded', 'false');
      toggle.innerHTML = '<i class="bi bi-list"></i>';
    }
  }

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = menu.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.innerHTML = open ? '<i class="bi bi-x-lg"></i>' : '<i class="bi bi-list"></i>';
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMenu();
    });
  }

  document.querySelectorAll('[data-reveal]').forEach(function (el) {
    if (!('IntersectionObserver' in window)) {
      el.classList.add('is-visible');
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    io.observe(el);
  });

  /* Feature tabs */
  var featureTabs = Array.prototype.slice.call(document.querySelectorAll('[data-feature]'));

  function activateFeature(tab) {
    var key = tab.getAttribute('data-feature');
    featureTabs.forEach(function (t) {
      var on = t === tab;
      t.classList.toggle('is-active', on);
      t.setAttribute('aria-selected', on ? 'true' : 'false');
      t.tabIndex = on ? 0 : -1;
    });
    document.querySelectorAll('[data-panel]').forEach(function (panel) {
      var on = panel.getAttribute('data-panel') === key;
      panel.classList.toggle('is-active', on);
      if (on) panel.removeAttribute('hidden');
      else panel.setAttribute('hidden', '');
    });
  }

  featureTabs.forEach(function (tab, index) {
    tab.addEventListener('click', function () {
      activateFeature(tab);
    });
    tab.addEventListener('keydown', function (e) {
      var nextKey = e.key === 'ArrowRight' || e.key === 'ArrowDown';
      var prevKey = e.key === 'ArrowLeft' || e.key === 'ArrowUp';
      if (!nextKey && !prevKey) return;
      e.preventDefault();
      var next = featureTabs[(index + (nextKey ? 1 : -1) + featureTabs.length) % featureTabs.length];
      next.focus();
      activateFeature(next);
    });
  });

  document.querySelectorAll('[data-open]').forEach(function (link) {
    link.addEventListener('click', function () {
      var key = link.getAttribute('data-open');
      var tab = document.querySelector('[data-feature="' + key + '"]');
      if (tab) activateFeature(tab);
    });
  });

  var landing = document.querySelector('.lp-page');
  if (landing && 'IntersectionObserver' in window) {
    var linkMap = {};
    document.querySelectorAll('.bp-nav-center a').forEach(function (a) {
      var href = a.getAttribute('href') || '';
      var hash = href.split('#')[1];
      if (hash) linkMap[hash] = a;
    });
    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting || !linkMap[entry.target.id]) return;
        Object.keys(linkMap).forEach(function (key) {
          linkMap[key].classList.toggle('is-current', key === entry.target.id);
          if (key === entry.target.id) linkMap[key].setAttribute('aria-current', 'true');
          else linkMap[key].removeAttribute('aria-current');
        });
      });
    }, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });
    Object.keys(linkMap).forEach(function (id) {
      var section = document.getElementById(id);
      if (section) spy.observe(section);
    });
  }
  document.querySelectorAll('.lp-faq details').forEach(function (item) {
    item.addEventListener('toggle', function () {
      if (!item.open) return;
      document.querySelectorAll('.lp-faq details').forEach(function (other) {
        if (other !== item) other.open = false;
      });
    });
  });

  /* Pricing toggle */
  var billing = 'monthly';
  var billingBtns = document.querySelectorAll('.bp-billing-toggle button');
  var prices = document.querySelectorAll('.bp-pricing-grid .js-price');
  var cycles = document.querySelectorAll('.bp-pricing-grid .js-cycle');
  var ctas = document.querySelectorAll('.bp-pricing-grid .js-cta');
  var signupBase = (window.BP_PRICING && window.BP_PRICING.signupBase) || '/get-started';

  function syncBilling() {
    billingBtns.forEach(function (btn) {
      btn.classList.toggle('is-active', btn.getAttribute('data-billing') === billing);
    });
    prices.forEach(function (el) {
      var raw = billing === 'yearly' ? el.getAttribute('data-yearly') : el.getAttribute('data-monthly');
      var isFree = Number(String(raw).replace(/,/g, '')) === 0;
      el.textContent = isFree ? 'Free' : ('₹' + raw);
      var cycle = el.parentElement && el.parentElement.querySelector('.js-cycle');
      if (cycle) cycle.textContent = isFree ? 'to start' : (billing === 'yearly' ? '/ year' : '/ month');
    });
    ctas.forEach(function (a) {
      var plan = a.getAttribute('data-plan');
      a.href = signupBase + '?plan=' + encodeURIComponent(plan) + '&billing=' + billing;
    });
  }

  billingBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      billing = btn.getAttribute('data-billing');
      syncBilling();
    });
  });

  /* Smooth in-page anchors */
  document.querySelectorAll('a[href*="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var href = a.getAttribute('href') || '';
      var hashIndex = href.indexOf('#');
      if (hashIndex === -1) return;
      var id = href.slice(hashIndex + 1);
      if (!id) return;
      var target = document.getElementById(id);
      if (!target) return;
      var samePage = href.charAt(0) === '#' || href.indexOf(window.location.pathname) !== -1 || href.indexOf(window.location.origin) === 0;
      if (!samePage && href.charAt(0) !== '#') return;
      e.preventDefault();
      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
      closeMenu();
    });
  });
})();
