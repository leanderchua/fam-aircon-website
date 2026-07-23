<?php
$famNavItems = [
    'dashboard.php' => 'Dashboard',
    'settings.php' => 'Settings',
    'stats.php' => 'Stats',
    'about_checklist.php' => 'About Checklist',
    'services.php' => 'Services',
    'brands.php' => 'Brands',
    'projects.php' => 'Projects',
    'contact_info.php' => 'Contact Info',
    'nav_links.php' => 'Nav Links',
];
$famCurrent = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin — FAM Airconditioning Supply</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#00164e',
        secondary: '#0051d5',
        cta: '#f97316',
        'cta-hover': '#ea580c',
        surface: '#f7f9fb',
        'surface-bright': '#ffffff',
        'on-surface': '#191c1e',
        'on-surface-variant': '#45464d',
        'outline-variant': '#c6c6cd',
      },
    },
  },
};
</script>
</head>
<body class="bg-surface min-h-screen">
  <header class="bg-primary text-white px-4 md:px-12 py-4 flex items-center justify-between">
    <span class="font-bold">FAM Admin</span>
    <a href="/admin/logout.php" class="text-xs uppercase tracking-[0.15em] hover:text-cta">Log Out</a>
  </header>
  <div class="flex max-w-6xl mx-auto">
    <nav class="w-48 shrink-0 border-r border-outline-variant py-8 pr-4 hidden md:block">
      <ul class="grid gap-1">
        <?php foreach ($famNavItems as $href => $label): ?>
          <li>
            <a href="/admin/<?= $href ?>" class="block px-3 py-2 text-sm <?= $famCurrent === $href ? 'bg-surface-dim text-primary font-semibold' : 'text-on-surface-variant hover:text-secondary' ?>">
              <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <main class="flex-1 px-4 md:px-12 py-10">
