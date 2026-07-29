<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="description" content="FAM Airconditioning Supply — Design, supply, installation, repair, and maintenance of all aircon brands. Home service across Metro Manila and nearby provinces.">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1E5F75',
            'primary-dark': '#0f3344',
            secondary: '#29B5E8',
            'secondary-light': '#b8e8f7',
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
            display: ['Inter', 'system-ui', 'sans-serif'],
            body: ['Inter', 'system-ui', 'sans-serif'],
            label: ['JetBrains Mono', 'monospace'],
          },
          maxWidth: {
            container: '1280px',
          },
          borderRadius: {
            DEFAULT: '0.125rem',
          },
          transitionDuration: {
            '1500': '1500ms',
          },
        },
      },
    };
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .material-symbols-outlined.fill-icon {
      font-variation-settings: 'FILL' 1;
    }
  </style>
  <link rel="stylesheet" href="css/styles.css?v=2">
</head>
<body class="bg-surface text-on-surface font-body text-base antialiased selection:bg-secondary selection:text-white">
