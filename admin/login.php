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
<body class="bg-surface min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-sm bg-surface-bright border border-outline-variant p-8">
    <h1 class="text-xl font-bold text-primary mb-6">FAM Admin Login</h1>
    <?php if ($error): ?>
      <p class="mb-4 text-sm text-red-600"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" class="space-y-4">
      <?= fam_csrf_field() ?>
      <div>
        <label class="block text-sm text-on-surface-variant mb-1">Username</label>
        <input type="text" name="username" required class="w-full border border-outline-variant px-3 py-2">
      </div>
      <div>
        <label class="block text-sm text-on-surface-variant mb-1">Password</label>
        <input type="password" name="password" required class="w-full border border-outline-variant px-3 py-2">
      </div>
      <button type="submit" class="w-full bg-cta hover:bg-cta-hover text-white py-2 font-semibold uppercase text-xs tracking-[0.15em]">Log In</button>
    </form>
  </div>
</body>
</html>
