<?php
$famNavItems = [
    'dashboard.php' => ['label' => 'Dashboard', 'icon' => 'home'],
    'settings.php' => ['label' => 'Settings', 'icon' => 'settings'],
    'stats.php' => ['label' => 'Stats', 'icon' => 'chart'],
    'about_checklist.php' => ['label' => 'About Checklist', 'icon' => 'check'],
    'services.php' => ['label' => 'Services', 'icon' => 'wrench'],
    'brands.php' => ['label' => 'Brands', 'icon' => 'tag'],
    'projects.php' => ['label' => 'Projects', 'icon' => 'photo'],
    'contact_info.php' => ['label' => 'Contact Info', 'icon' => 'mail'],
    'nav_links.php' => ['label' => 'Nav Links', 'icon' => 'menu'],
    'admins.php' => ['label' => 'Admins', 'icon' => 'user'],
];
$famCurrent = basename($_SERVER['SCRIPT_NAME']);
$famPageTitle = $famPageTitle ?? ($famNavItems[$famCurrent]['label'] ?? 'Admin');

function fam_icon(string $name, string $class = 'w-5 h-5'): string
{
    $paths = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.752.43.992l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a7.72 7.72 0 010-.255c.007-.378-.138-.752-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'wrench' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.026 4.026" />',
        'tag' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />',
        'photo' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />',
        'mail' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />',
        'menu' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />',
        'up' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />',
        'down' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />',
        'trash' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />',
        'edit' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5v6a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V8.25A2.25 2.25 0 016 6h6" />',
        'plus' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />',
        'image-off' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M14.25 4.5l7.5 7.5M21.75 4.5l-7.5 7.5M2.25 6v12a1.5 1.5 0 001.5 1.5h16.5" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
    ];
    $d = $paths[$name] ?? $paths['tag'];
    return "<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" class=\"{$class}\" aria-hidden=\"true\">{$d}</svg>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin — FAM Airconditioning Supply</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#00164e',
        secondary: '#0051d5',
        'secondary-light': '#b4c5ff',
        cta: '#f97316',
        'cta-hover': '#ea580c',
        surface: '#f7f9fb',
        'surface-dim': '#e6e8ea',
        'surface-bright': '#ffffff',
        'on-surface': '#191c1e',
        'on-surface-variant': '#45464d',
        'outline-variant': '#c6c6cd',
        outline: '#76777d',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        label: ['JetBrains Mono', 'monospace'],
      },
    },
  },
};
</script>
<style>
  :focus-visible { outline: none; box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #0051d5; }
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration: 0.001ms !important; transition-duration: 0.001ms !important; }
  }
</style>
</head>
<body class="bg-surface min-h-screen font-sans text-on-surface antialiased">
  <a href="#famMain" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:bg-white focus:text-primary focus:px-4 focus:py-2 focus:rounded">Skip to content</a>

  <header class="bg-primary text-white px-4 md:px-8 h-16 flex items-center justify-between sticky top-0 z-40">
    <div class="flex items-center gap-3">
      <button id="famNavToggle" type="button" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="famSidebar" class="md:hidden p-2 -ml-2 cursor-pointer hover:bg-white/10 rounded transition-colors duration-200">
        <?= fam_icon('menu', 'w-6 h-6') ?>
      </button>
      <span class="font-bold tracking-tight">FAM Admin</span>
      <span class="hidden sm:inline text-white/40">/</span>
      <span class="hidden sm:inline text-white/80 text-sm"><?= htmlspecialchars($famPageTitle, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <a href="/admin/logout.php" class="inline-flex items-center gap-2 text-xs uppercase tracking-[0.15em] font-label px-3 py-2 rounded hover:bg-white/10 transition-colors duration-200 cursor-pointer">Log Out</a>
  </header>

  <!-- Mobile backdrop -->
  <div id="famNavBackdrop" class="fixed inset-0 bg-on-surface/50 z-30 hidden md:hidden"></div>

  <div class="flex max-w-6xl mx-auto">
    <nav id="famSidebar" class="w-64 shrink-0 bg-surface-bright border-r border-outline-variant py-6 px-3 fixed md:sticky top-16 md:top-16 left-0 h-[calc(100vh-4rem)] overflow-y-auto -translate-x-full md:translate-x-0 transition-transform duration-200 z-40">
      <ul class="grid gap-1">
        <?php foreach ($famNavItems as $href => $item): $active = $famCurrent === $href; ?>
          <li>
            <a href="/admin/<?= $href ?>" class="flex items-center gap-3 px-3 py-2.5 rounded text-sm transition-colors duration-200 border-l-4 cursor-pointer <?= $active ? 'bg-surface-dim text-primary font-semibold border-cta' : 'text-on-surface-variant border-transparent hover:bg-surface hover:text-secondary' ?>">
              <?= fam_icon($item['icon']) ?>
              <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <main id="famMain" class="flex-1 min-w-0 px-4 md:px-10 py-8 md:py-10">
