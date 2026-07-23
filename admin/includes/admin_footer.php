    </main>
  </div>
  <script>
    (function () {
      var toggle = document.getElementById('famNavToggle');
      var sidebar = document.getElementById('famSidebar');
      var backdrop = document.getElementById('famNavBackdrop');
      if (!toggle || !sidebar || !backdrop) return;

      function closeNav() {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
      }
      function openNav() {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
      }
      toggle.addEventListener('click', function () {
        var isOpen = toggle.getAttribute('aria-expanded') === 'true';
        isOpen ? closeNav() : openNav();
      });
      backdrop.addEventListener('click', closeNav);
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeNav();
      });
    })();

    // Disable + relabel submit buttons on submit for clear async feedback.
    document.querySelectorAll('form').forEach(function (form) {
      form.addEventListener('submit', function () {
        var btn = form.querySelector('button[type="submit"]');
        if (btn && !btn.disabled) {
          btn.dataset.originalText = btn.textContent;
          btn.disabled = true;
          btn.classList.add('opacity-60', 'cursor-not-allowed');
          btn.textContent = 'Saving…';
        }
      });
    });

    // Live image preview: any input with data-preview-target updates the matching <img>.
    document.querySelectorAll('[data-preview-target]').forEach(function (input) {
      var img = document.getElementById(input.dataset.previewTarget);
      var placeholder = img ? img.parentElement.querySelector('[data-preview-placeholder]') : null;
      if (!img) return;

      function update() {
        var val = input.value.trim();
        if (!val) {
          img.classList.add('hidden');
          if (placeholder) placeholder.classList.remove('hidden');
          return;
        }
        img.src = val;
        img.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
      }
      img.addEventListener('error', function () {
        img.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
      });
      input.addEventListener('input', update);
      update();
    });
  </script>
</body>
</html>
