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

  // Project galleries (lightbox)
  const projectGalleries = {
    'one-wilson-square': {
      title: 'One Wilson Square',
      category: 'Commercial',
      images: [
        { src: 'https://images.unsplash.com/photo-1614447413576-b346c641c128?w=1200&q=80', alt: 'Commercial HVAC installation at One Wilson Square' },
        { src: 'https://images.unsplash.com/photo-1681042803902-f79c240d8f03?w=1200&q=80', alt: 'Rooftop VRF outdoor units at One Wilson Square' },
        { src: 'https://images.unsplash.com/photo-1642749776312-aa42ce20c9f5?w=1200&q=80', alt: 'Installation crew on the rooftop at One Wilson Square' },
      ],
    },
    'feu-nrmf-medical-center': {
      title: 'FEU-NRMF Medical Center',
      category: 'Commercial',
      images: [
        { src: 'https://images.unsplash.com/photo-1667983453881-4992fe86ab1b?w=1200&q=80', alt: 'HVAC system at FEU-NRMF Medical Center' },
        { src: 'https://images.unsplash.com/photo-1558358235-a0a93f68a52c?w=1200&q=80', alt: 'Air vent detail at FEU-NRMF Medical Center' },
        { src: 'https://images.unsplash.com/photo-1621905251918-48416bd8575a?w=1200&q=80', alt: 'Technician inspecting equipment at FEU-NRMF Medical Center' },
      ],
    },
    'st-joseph-building': {
      title: 'St. Joseph Building',
      category: 'Commercial',
      images: [
        { src: 'https://images.unsplash.com/photo-1698479603408-1a66a6d9e80f?w=1200&q=80', alt: 'AC installation at St. Joseph Building' },
        { src: 'https://images.unsplash.com/photo-1615309662243-70f6df917b59?w=1200&q=80', alt: 'Ducting pipework at St. Joseph Building' },
        { src: 'https://images.unsplash.com/photo-1634114582073-c34f96202b65?w=1200&q=80', alt: 'VRF ductwork detail at St. Joseph Building' },
      ],
    },
    'riverside-family-home': {
      title: 'Riverside Family Home',
      category: 'Residential',
      images: [
        { src: 'https://images.unsplash.com/photo-1726614846573-c1ac2e6161d1?w=1200&q=80', alt: 'Split-type AC installed at Riverside Family Home' },
        { src: 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=1200&q=80', alt: 'Electrical work during the Riverside Family Home retrofit' },
        { src: 'https://images.unsplash.com/photo-1612836639523-2ed74bc0209e?w=1200&q=80', alt: 'Exterior of the Riverside Family Home' },
      ],
    },
    'private-residence-portfolio': {
      title: 'Private Residence Portfolio',
      category: 'Residential',
      images: [
        { src: 'https://images.unsplash.com/photo-1583954964358-1bd7215b6f7a?w=1200&q=80', alt: 'Residential split-type AC maintenance' },
        { src: 'https://images.unsplash.com/photo-1615774925655-a0e97fc85c14?w=1200&q=80', alt: 'Technician servicing a residential AC unit' },
        { src: 'https://images.unsplash.com/photo-1757562593192-e15aa89e7876?w=1200&q=80', alt: 'Technician preparing equipment for a residential install' },
      ],
    },
    'hillside-townhomes': {
      title: 'Hillside Townhomes',
      category: 'Residential',
      images: [
        { src: 'https://images.unsplash.com/photo-1718203862467-c33159fdc504?w=1200&q=80', alt: 'Multi-unit AC installation at Hillside Townhomes' },
        { src: 'https://images.unsplash.com/photo-1634114581640-9a1734fae3e5?w=1200&q=80', alt: 'Multi-unit condenser array at Hillside Townhomes' },
        { src: 'https://images.unsplash.com/photo-1660330589827-da8ab7dd3c02?w=1200&q=80', alt: 'Technician working on-site at Hillside Townhomes' },
      ],
    },
  };

  const galleryModal = document.getElementById('galleryModal');
  const galleryImage = document.getElementById('galleryImage');
  const galleryTitle = document.getElementById('galleryTitle');
  const galleryBadge = document.getElementById('galleryBadge');
  const galleryCounter = document.getElementById('galleryCounter');
  const galleryClose = document.getElementById('galleryClose');
  const galleryPrev = document.getElementById('galleryPrev');
  const galleryNext = document.getElementById('galleryNext');

  let activeSlug = null;
  let activeIndex = 0;
  let lastTrigger = null;

  function renderGallery() {
    const project = projectGalleries[activeSlug];
    const photo = project.images[activeIndex];
    galleryImage.src = photo.src;
    galleryImage.alt = photo.alt;
    galleryTitle.textContent = project.title;
    galleryBadge.textContent = project.category;
    galleryCounter.textContent = `${activeIndex + 1} / ${project.images.length}`;
  }

  function openGallery(slug) {
    if (!projectGalleries[slug]) return;
    activeSlug = slug;
    activeIndex = 0;
    renderGallery();
    galleryModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    galleryClose.focus();
  }

  function closeGallery() {
    galleryModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    activeSlug = null;
    if (lastTrigger) lastTrigger.focus();
  }

  function showNext() {
    const total = projectGalleries[activeSlug].images.length;
    activeIndex = (activeIndex + 1) % total;
    renderGallery();
  }

  function showPrev() {
    const total = projectGalleries[activeSlug].images.length;
    activeIndex = (activeIndex - 1 + total) % total;
    renderGallery();
  }

  document.querySelectorAll('[data-project]').forEach(tile => {
    tile.addEventListener('click', () => {
      lastTrigger = tile;
      openGallery(tile.dataset.project);
    });
    tile.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        lastTrigger = tile;
        openGallery(tile.dataset.project);
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
