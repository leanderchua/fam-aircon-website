document.addEventListener('DOMContentLoaded', () => {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Mobile menu
  const toggle = document.getElementById('navToggle');
  const mobile = document.getElementById('mobileMenu');
  toggle.addEventListener('click', () => {
    mobile.classList.toggle('hidden');
  });

  // Close mobile menu on link click
  mobile.querySelectorAll('a').forEach(a =>
    a.addEventListener('click', () => mobile.classList.add('hidden'))
  );

  // Smooth scroll with fixed navbar offset (RAF-based, bypasses prefers-reduced-motion)
  const nav = document.getElementById('nav');

  function scrollToY(targetY, duration) {
    const startY = window.scrollY;
    const dist = targetY - startY;
    const start = performance.now();
    function step(now) {
      const p = Math.min((now - start) / duration, 1);
      const ease = 1 - Math.pow(1 - p, 3);
      window.scrollTo(0, startY + dist * ease);
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      const target = document.querySelector(href);
      if (!target) return;
      e.preventDefault();
      const top = target.getBoundingClientRect().top + window.scrollY - nav.offsetHeight;
      scrollToY(top, 700);
      history.pushState(null, '', href);
    });
  });

  // Active nav link
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');
  window.addEventListener('scroll', () => {
    const y = scrollY + 200;
    sections.forEach(s => {
      if (y >= s.offsetTop && y < s.offsetTop + s.offsetHeight) {
        navLinks.forEach(l => {
          const isActive = l.getAttribute('href') === '#' + s.id;
          l.classList.toggle('text-secondary', isActive);
          l.classList.toggle('border-b-2', isActive);
          l.classList.toggle('border-secondary', isActive);
          l.classList.toggle('pb-1', isActive);
          l.classList.toggle('text-on-surface-variant', !isActive);
        });
      }
    });
  }, { passive: true });

  // Stats counter
  const vals = document.querySelectorAll('.stat-val');
  let counted = false;
  const cObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting && !counted) {
        counted = true;
        vals.forEach(el => {
          const t = +el.dataset.target;
          if (!t) return;
          const plus = el.querySelector('span');
          if (reduced) { el.textContent = t; if (plus) el.appendChild(plus); return; }
          const s = performance.now();
          (function tick(n) {
            const p = Math.min((n - s) / 1600, 1);
            el.textContent = Math.floor((1 - (1 - p) ** 3) * t);
            if (plus) el.appendChild(plus);
            p < 1 ? requestAnimationFrame(tick) : (el.textContent = t, plus && el.appendChild(plus));
          })(s);
        });
        cObs.disconnect();
      }
    });
  }, { threshold: 0.3 });
  vals.forEach(v => cObs.observe(v));

  // Form
  const form = document.getElementById('contactForm');
  form.addEventListener('submit', e => {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const txt = btn.textContent;
    btn.textContent = 'SENT!';
    btn.classList.replace('bg-cta', 'bg-green-600');
    btn.disabled = true;
    setTimeout(() => {
      btn.textContent = txt;
      btn.classList.replace('bg-green-600', 'bg-cta');
      btn.disabled = false;
      form.reset();
    }, 3000);
  });
});
