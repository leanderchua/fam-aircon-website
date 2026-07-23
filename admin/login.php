<?php

require_once __DIR__ . '/../includes/auth.php';

fam_start_session();

if (fam_is_logged_in()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!fam_verify_csrf()) {
        $error = 'Session expired, please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $result = fam_attempt_login($username, $password);
        if ($result['ok']) {
            header('Location: /admin/dashboard.php');
            exit;
        }
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login — FAM Airconditioning Supply</title>
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
<body class="bg-surface min-h-screen flex items-center justify-center px-4 font-sans antialiased">
  <div class="w-full max-w-sm bg-surface-bright border border-outline-variant p-8">
    <h1 class="text-xl font-bold text-primary mb-1">FAM Admin</h1>
    <p class="text-sm text-on-surface-variant mb-6">Sign in to manage site content.</p>

    <?php if ($error): ?>
      <div class="mb-4 flex items-start gap-2 border border-red-300 bg-red-50 text-red-700 px-3 py-2 text-sm" role="alert">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <?= fam_csrf_field() ?>
      <div>
        <label for="username" class="block text-sm text-on-surface-variant mb-1">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
      </div>
      <div>
        <label for="password" class="block text-sm text-on-surface-variant mb-1">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
      </div>
      <button type="submit" class="w-full bg-cta hover:bg-cta-hover text-white py-2.5 font-label font-semibold uppercase text-xs tracking-[0.15em] transition-colors duration-200 cursor-pointer">Log In</button>
    </form>
  </div>
</body>
</html>
