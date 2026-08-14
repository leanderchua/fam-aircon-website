document.addEventListener('DOMContentLoaded', () => {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Mobile menu
  const toggle = document.getElementById('navToggle');
  const mobile = document.getElementById('mobileMenu');
  const toggleIcon = toggle.querySelector('.material-symbols-outlined');
  let mobileCloseTimer = null;
  function setMobileMenuOpen(open) {
    clearTimeout(mobileCloseTimer);
    toggle.setAttribute('aria-expanded', String(open));
    if (toggleIcon) toggleIcon.textContent = open ? 'close' : 'menu';
    if (open) {
      mobile.classList.remove('hidden');
      if (reduced) {
        mobile.classList.add('menu-open');
      } else {
        requestAnimationFrame(() => mobile.classList.add('menu-open'));
      }
    } else {
      mobile.classList.remove('menu-open');
      if (reduced) {
        mobile.classList.add('hidden');
      } else {
        mobileCloseTimer = setTimeout(() => mobile.classList.add('hidden'), 300);
      }
    }
  }
  toggle.addEventListener('click', e => {
    e.stopPropagation();
    setMobileMenuOpen(mobile.classList.contains('hidden'));
  });

  // Close mobile menu on link click
  mobile.querySelectorAll('a').forEach(a =>
    a.addEventListener('click', () => setMobileMenuOpen(false))
  );

  // Close mobile menu on outside click / Escape
  document.addEventListener('click', e => {
    if (mobile.classList.contains('hidden')) return;
    if (mobile.contains(e.target) || toggle.contains(e.target)) return;
    setMobileMenuOpen(false);
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !mobile.classList.contains('hidden')) setMobileMenuOpen(false);
  });

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

  // Active nav link + sliding indicator
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');
  const navIndicator = document.getElementById('navIndicator');
  const navMenu = document.getElementById('navMenu');
  let navScrollTicking = false;

  function positionNavIndicator(link) {
    if (!navIndicator || !navMenu || !link || navMenu.offsetParent === null) return;
    const menuRect = navMenu.getBoundingClientRect();
    const linkRect = link.getBoundingClientRect();
    navIndicator.style.left = `${linkRect.left - menuRect.left}px`;
    navIndicator.style.width = `${linkRect.width}px`;
    navIndicator.style.opacity = '1';
  }

  function updateActiveNavLink() {
    const y = scrollY + 200;
    sections.forEach(s => {
      if (y >= s.offsetTop && y < s.offsetTop + s.offsetHeight) {
        navLinks.forEach(l => {
          const isActive = l.getAttribute('href') === '#' + s.id;
          l.classList.toggle('text-secondary', isActive);
          l.classList.toggle('text-on-surface-variant', !isActive);
          if (isActive) {
            l.setAttribute('aria-current', 'page');
            positionNavIndicator(l);
          } else {
            l.removeAttribute('aria-current');
          }
        });
      }
    });
    navScrollTicking = false;
  }
  window.addEventListener('scroll', () => {
    if (navScrollTicking) return;
    navScrollTicking = true;
    requestAnimationFrame(updateActiveNavLink);
  }, { passive: true });
  window.addEventListener('resize', () => {
    positionNavIndicator(document.querySelector('.nav-link[aria-current="page"]'));
  });
  updateActiveNavLink();

  // Scroll reveal
  const revealEls = document.querySelectorAll('.reveal');
  if (reduced) {
    revealEls.forEach(el => el.classList.add('is-visible'));
  } else {
    const revealObs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          revealObs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(el => revealObs.observe(el));
  }

  // Hero reveals immediately — already in the initial viewport
  const heroReveal = document.querySelector('#home .reveal');
  if (heroReveal) heroReveal.classList.add('is-visible');

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

  // Project galleries (lightbox) — image data comes from data-gallery JSON
  // rendered server-side per tile (index.php), not a hardcoded lookup here.
  const galleryModal = document.getElementById('galleryModal');
  const galleryImage = document.getElementById('galleryImage');
  const galleryTitle = document.getElementById('galleryTitle');
  const galleryBadge = document.getElementById('galleryBadge');
  const galleryCounter = document.getElementById('galleryCounter');
  const galleryClose = document.getElementById('galleryClose');
  const galleryPrev = document.getElementById('galleryPrev');
  const galleryNext = document.getElementById('galleryNext');

  let activeImages = null;
  let activeTitle = '';
  let activeCategory = '';
  let activeIndex = 0;
  let lastTrigger = null;

  function renderGallery() {
    const photo = activeImages[activeIndex];
    galleryImage.src = photo.src;
    galleryImage.alt = photo.alt;
    galleryTitle.textContent = activeTitle;
    galleryBadge.textContent = activeCategory;
    galleryCounter.textContent = `${activeIndex + 1} / ${activeImages.length}`;
  }

  let galleryCloseTimer = null;
  function openGallery(tile) {
    let images;
    try {
      images = JSON.parse(tile.dataset.gallery);
    } catch (e) {
      return;
    }
    if (!Array.isArray(images) || images.length === 0) return;
    clearTimeout(galleryCloseTimer);
    activeImages = images;
    activeTitle = tile.dataset.galleryTitle || '';
    activeCategory = tile.dataset.galleryCategory || '';
    activeIndex = 0;
    renderGallery();
    galleryModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    if (reduced) {
      galleryModal.classList.add('modal-open');
    } else {
      requestAnimationFrame(() => galleryModal.classList.add('modal-open'));
    }
    galleryClose.focus();
  }

  function closeGallery() {
    galleryModal.classList.remove('modal-open');
    document.body.classList.remove('overflow-hidden');
    activeImages = null;
    if (lastTrigger) lastTrigger.focus();
    if (reduced) {
      galleryModal.classList.add('hidden');
    } else {
      galleryCloseTimer = setTimeout(() => galleryModal.classList.add('hidden'), 200);
    }
  }

  function showNext() {
    const total = activeImages.length;
    activeIndex = (activeIndex + 1) % total;
    renderGallery();
  }

  function showPrev() {
    const total = activeImages.length;
    activeIndex = (activeIndex - 1 + total) % total;
    renderGallery();
  }

  document.querySelectorAll('[data-gallery]').forEach(tile => {
    tile.addEventListener('click', () => {
      lastTrigger = tile;
      openGallery(tile);
    });
    tile.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        lastTrigger = tile;
        openGallery(tile);
      }
    });
  });

  galleryClose.addEventListener('click', closeGallery);
  galleryModal.querySelector('[data-modal-close]').addEventListener('click', closeGallery);
  galleryNext.addEventListener('click', showNext);
  galleryPrev.addEventListener('click', showPrev);

  document.addEventListener('keydown', e => {
    if (galleryModal.classList.contains('hidden')) return;
    if (e.key === 'Escape') closeGallery();
    if (e.key === 'ArrowRight') showNext();
    if (e.key === 'ArrowLeft') showPrev();
  });

  let touchStartX = null;
  galleryImage.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].clientX;
  }, { passive: true });
  galleryImage.addEventListener('touchend', e => {
    if (touchStartX === null) return;
    const delta = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(delta) > 40) delta < 0 ? showNext() : showPrev();
    touchStartX = null;
  }, { passive: true });

  // Form
  const form = document.getElementById('contactForm');
  const isServerBacked = form.querySelector('input[name="contact_submit"]') !== null;
  if (!isServerBacked) {
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
  }
});
