/* Floating theme — main JS */
(function () {
  'use strict';

  /* Sticky header shadow on scroll */
  const header = document.getElementById('site-header');
  if (header) {
    const onScroll = () => {
      if (window.scrollY > 8) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* Mobile menu */
  const burger = document.querySelector('.hamburger');
  const mobile = document.getElementById('mobile-menu');
  if (burger && mobile) {
    burger.addEventListener('click', () => {
      const open = burger.getAttribute('aria-expanded') === 'true';
      burger.setAttribute('aria-expanded', String(!open));
      mobile.classList.toggle('open', !open);
      mobile.setAttribute('aria-hidden', String(open));
      document.body.style.overflow = !open ? 'hidden' : '';
    });
    mobile.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      burger.setAttribute('aria-expanded', 'false');
      mobile.classList.remove('open');
      document.body.style.overflow = '';
    }));
  }

  /* Fade-up reveal */
  const targets = document.querySelectorAll('.fade-up');
  if (targets.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('in');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    targets.forEach(t => io.observe(t));
  } else {
    targets.forEach(t => t.classList.add('in'));
  }

  /* Gallery filter (lightweight) */
  document.querySelectorAll('[data-filterable]').forEach(group => {
    const cards = group.querySelectorAll('[data-cat]');
    const chips = group.querySelectorAll('.filter-chip');
    chips.forEach(chip => {
      chip.addEventListener('click', () => {
        chips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        const f = chip.dataset.filter;
        cards.forEach(card => {
          const cats = (card.dataset.cat || '').split(',').map(s => s.trim());
          card.style.display = (f === 'all' || cats.includes(f)) ? '' : 'none';
        });
      });
    });
  });

})();
