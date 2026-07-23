<?php

require_once __DIR__ . '/../includes/auth.php';

fam_require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard — FAM Admin</title>
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
  <main class="max-w-3xl mx-auto px-4 md:px-12 py-10">
    <h1 class="text-2xl font-bold text-primary mb-2">Welcome, <?= htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="text-on-surface-variant">Logged in successfully. Content management screens (Stats, Services, Brands, Projects, Contact) are built next.</p>
  </main>
</body>
</html>
