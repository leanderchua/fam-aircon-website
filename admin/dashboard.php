<?php

require_once __DIR__ . '/../includes/auth.php';

fam_require_login();

require __DIR__ . '/includes/admin_header.php';
?>
<h1 class="text-2xl font-bold text-primary mb-2">Welcome, <?= htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8') ?></h1>
<p class="text-on-surface-variant mb-8">Manage site content below. Changes here will appear on the public site once it's converted to read from the database.</p>

<div class="grid sm:grid-cols-2 gap-4 max-w-2xl">
  <a href="/admin/settings.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">Settings</span>
    <span class="text-sm text-on-surface-variant">Hero, about, section copy, contact email</span>
  </a>
  <a href="/admin/stats.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">Stats</span>
    <span class="text-sm text-on-surface-variant">Homepage stat counters</span>
  </a>
  <a href="/admin/about_checklist.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">About Checklist</span>
    <span class="text-sm text-on-surface-variant">Checklist items in the About section</span>
  </a>
  <a href="/admin/services.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">Services</span>
    <span class="text-sm text-on-surface-variant">Service cards</span>
  </a>
  <a href="/admin/brands.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">Brands</span>
    <span class="text-sm text-on-surface-variant">Brand logos carried</span>
  </a>
  <a href="/admin/projects.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">Projects</span>
    <span class="text-sm text-on-surface-variant">Featured deployments gallery</span>
  </a>
  <a href="/admin/contact_info.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">Contact Info</span>
    <span class="text-sm text-on-surface-variant">Address, phone, email, hours</span>
  </a>
  <a href="/admin/nav_links.php" class="border border-outline-variant bg-surface-bright p-5 hover:border-secondary transition-colors">
    <span class="font-semibold text-primary block mb-1">Nav Links</span>
    <span class="text-sm text-on-surface-variant">Header/footer navigation</span>
  </a>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
