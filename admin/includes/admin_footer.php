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
    // Text inputs preview by URL/path; file inputs preview via an object URL and take
    // priority while a file is selected (matches the server's upload-takes-precedence rule).
    var famPreviewGroups = {};
    document.querySelectorAll('[data-preview-target]').forEach(function (input) {
      var key = input.dataset.previewTarget;
      if (!famPreviewGroups[key]) famPreviewGroups[key] = [];
      famPreviewGroups[key].push(input);
    });

    Object.keys(famPreviewGroups).forEach(function (key) {
      var img = document.getElementById(key);
      var placeholder = img ? img.parentElement.querySelector('[data-preview-placeholder]') : null;
      if (!img) return;

      var inputs = famPreviewGroups[key];
      var textInput = inputs.find(function (i) { return i.type !== 'file'; });
      var fileInput = inputs.find(function (i) { return i.type === 'file'; });
      var objectUrl = null;

      function show(src) {
        if (!src) {
          img.classList.add('hidden');
          if (placeholder) placeholder.classList.remove('hidden');
          return;
        }
        img.src = src;
        img.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
      }

      function update() {
        if (fileInput && fileInput.files && fileInput.files[0]) {
          if (objectUrl) URL.revokeObjectURL(objectUrl);
          objectUrl = URL.createObjectURL(fileInput.files[0]);
          show(objectUrl);
          return;
        }
        show(textInput ? textInput.value.trim() : '');
      }

      img.addEventListener('error', function () {
        img.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
      });
      if (textInput) textInput.addEventListener('input', update);
      if (fileInput) fileInput.addEventListener('change', update);
      update();
    });
  </script>
</body>
</html>
