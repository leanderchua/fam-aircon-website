<?php

require_once __DIR__ . '/../includes/auth.php';

fam_require_login();

$famPageTitle = 'Dashboard';
require __DIR__ . '/includes/admin_header.php';

$cards = [
    ['href' => 'settings.php', 'icon' => 'settings', 'title' => 'Settings', 'desc' => 'Hero, about, section copy, contact email'],
    ['href' => 'stats.php', 'icon' => 'chart', 'title' => 'Stats', 'desc' => 'Homepage stat counters'],
    ['href' => 'about_checklist.php', 'icon' => 'check', 'title' => 'About Checklist', 'desc' => 'Checklist items in the About section'],
    ['href' => 'services.php', 'icon' => 'wrench', 'title' => 'Services', 'desc' => 'Service cards'],
    ['href' => 'brands.php', 'icon' => 'tag', 'title' => 'Brands', 'desc' => 'Brand logos carried'],
    ['href' => 'projects.php', 'icon' => 'photo', 'title' => 'Projects', 'desc' => 'Featured deployments gallery'],
    ['href' => 'contact_info.php', 'icon' => 'mail', 'title' => 'Contact Info', 'desc' => 'Address, phone, email, hours'],
    ['href' => 'nav_links.php', 'icon' => 'menu', 'title' => 'Nav Links', 'desc' => 'Header/footer navigation'],
];
?>
<h1 class="text-2xl font-bold text-primary mb-2">Welcome, <?= htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8') ?></h1>
<p class="text-on-surface-variant mb-8">Manage site content below. Changes here will appear on the public site once it's converted to read from the database.</p>

<div class="grid sm:grid-cols-2 gap-4 max-w-2xl">
  <?php foreach ($cards as $card): ?>
    <a href="/admin/<?= $card['href'] ?>" class="flex items-start gap-4 border border-outline-variant bg-surface-bright p-5 cursor-pointer hover:border-secondary hover:shadow-md transition-all duration-200">
      <span class="shrink-0 w-10 h-10 flex items-center justify-center bg-surface-dim text-secondary rounded">
        <?= fam_icon($card['icon'], 'w-5 h-5') ?>
      </span>
      <span>
        <span class="font-semibold text-primary block mb-1"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></span>
        <span class="text-sm text-on-surface-variant"><?= htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8') ?></span>
      </span>
    </a>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
