<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

fam_start_session();

$contactSuccess = false;
$contactError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    if (!fam_verify_csrf()) {
        $contactError = 'Your session expired — please refresh the page and try again.';
    } else {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $serviceNeeded = trim($_POST['service_needed'] ?? '');
        $projectDetails = trim($_POST['project_details'] ?? '');

        if ($fullName === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $contactError = 'Please provide your name and a valid email address.';
        } else {
            $stmt = fam_db()->prepare(
                'INSERT INTO contact_submissions (full_name, phone, email, service_needed, project_details, ip_address) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$fullName, $phone, $email, $serviceNeeded, $projectDetails, $_SERVER['REMOTE_ADDR'] ?? null]);

            $recipients = array_filter(array_map(
                fn($addr) => filter_var(trim($addr), FILTER_VALIDATE_EMAIL) ?: null,
                explode(',', fetchSettings()['contact_recipient_email'] ?? '')
            ));
            if ($recipients) {
                $subjectSafe = str_replace(["\r", "\n"], '', $fullName);
                $subject = 'New Service Inquiry - ' . $subjectSafe;
                $body = "Name: {$fullName}\nPhone: {$phone}\nEmail: {$email}\nService Needed: {$serviceNeeded}\n\n{$projectDetails}";
                @mail(implode(', ', $recipients), $subject, $body, "From: no-reply@famaircon.com\r\n");
            }

            $contactSuccess = true;
        }
    }
}

$settings = fetchSettings();
$stats = fetchStats();
$aboutChecklist = fetchAboutChecklist();
$services = fetchServices();
$brands = fetchBrands();
$projects = fetchProjects();
$contactBlocks = fetchContactBlocks();
$navLinks = fetchNavLinks();

function fam_slug(string $s): string
{
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9\s-]/', '', $s);
    $s = preg_replace('/[\s-]+/', '-', $s);
    return trim($s, '-');
}

// Bento-grid tile spans for the Projects gallery — positional, not DB-driven (matches the original hand-tuned layout).
$projectSpanClasses = [
    0 => 'aspect-[4/3] sm:aspect-[16/9] sm:col-span-2 md:aspect-auto md:col-span-2 md:row-span-2',
    1 => 'aspect-square md:aspect-auto md:col-span-2',
    5 => 'aspect-video sm:aspect-square sm:col-span-2 md:aspect-auto md:col-span-3',
];
$defaultProjectSpan = 'aspect-square md:aspect-auto';

$pageTitle = $settings['company_name'] . ' - ' . $settings['hero_heading_line1'] . ' ' . $settings['hero_heading_line2'];
$brandChunks = array_chunk($brands, (int) ceil(count($brands) / 2));
?>
<?php include __DIR__ . '/includes/head.php'; ?>

  <!-- NAV -->
  <header class="bg-white border-b border-outline-variant fixed w-full top-0 z-50" id="nav">
    <div class="flex justify-between items-center w-full px-4 md:px-12 max-w-container mx-auto h-20">
      <div class="flex items-center gap-4">
        <img alt="<?= htmlspecialchars($settings['company_name'], ENT_QUOTES, 'UTF-8') ?> Logo" class="h-10 w-auto object-contain" src="<?= htmlspecialchars($settings['logo_path'], ENT_QUOTES, 'UTF-8') ?>">
        <span class="text-lg font-extrabold text-primary-dark hidden lg:block tracking-tight font-display"><?= htmlspecialchars($settings['company_name'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <nav class="hidden lg:flex items-center gap-8 text-base font-body relative" id="navMenu">
        <?php foreach ($navLinks as $link): ?>
          <a class="nav-link text-on-surface-variant hover:text-secondary pb-1 transition-colors duration-200" href="<?= htmlspecialchars($link['href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>
        <span class="absolute left-0 -bottom-1 h-0.5 bg-secondary rounded-full opacity-0 transition-[left,width,opacity] duration-300 ease-out pointer-events-none" id="navIndicator"></span>
      </nav>
      <a class="hidden lg:inline-flex items-center justify-center px-6 py-3 bg-cta text-white font-label text-xs font-semibold uppercase tracking-widest hover:bg-cta-hover transition-colors" href="#contact">Get a Quote</a>
      <button class="lg:hidden text-primary p-2" id="navToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobileMenu">
        <span class="material-symbols-outlined text-3xl">menu</span>
      </button>
    </div>
    <!-- Mobile Menu -->
    <div class="hidden lg:hidden bg-white border-t border-outline-variant px-4 pb-6 pt-2" id="mobileMenu">
      <?php foreach ($navLinks as $link): ?>
        <a class="block py-3 text-on-surface-variant hover:text-secondary font-body" href="<?= htmlspecialchars($link['href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?></a>
      <?php endforeach; ?>
      <a class="block mt-4 text-center px-6 py-3 bg-cta text-white font-label text-xs font-semibold uppercase tracking-widest hover:bg-cta-hover" href="#contact">Get a Quote</a>
    </div>
  </header>

  <main class="pt-20">

    <!-- HERO -->
    <section class="relative min-h-[600px] flex items-center bg-white border-b border-outline-variant" id="home">
      <div class="absolute inset-0 w-full h-full">
        <div class="bg-cover bg-center w-full h-full opacity-30 grayscale" style="background-image: url('<?= htmlspecialchars($settings['hero_bg_image_path'], ENT_QUOTES, 'UTF-8') ?>')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/90 to-transparent"></div>
      </div>
      <div class="relative w-full px-4 md:px-12 max-w-container mx-auto py-24">
        <div class="reveal max-w-2xl grid gap-6">
          <span class="font-label text-xs font-semibold text-secondary tracking-[0.15em] uppercase flex items-center gap-2">
            <span class="w-8 h-px bg-secondary"></span>
            <?= htmlspecialchars($settings['hero_eyebrow'], ENT_QUOTES, 'UTF-8') ?>
          </span>
          <h1 class="font-display text-5xl md:text-[56px] leading-tight font-extrabold tracking-tight">
            <span class="text-primary-dark"><?= htmlspecialchars($settings['hero_heading_line1'], ENT_QUOTES, 'UTF-8') ?></span> <br><span class="text-secondary"><?= htmlspecialchars($settings['hero_heading_line2'], ENT_QUOTES, 'UTF-8') ?></span>
          </h1>
          <p class="text-xl font-medium text-on-surface-variant max-w-xl leading-relaxed">
            <?= htmlspecialchars($settings['hero_subtitle'], ENT_QUOTES, 'UTF-8') ?>
          </p>
          <div class="flex flex-col sm:flex-row gap-4 mt-8">
            <a class="inline-flex items-center justify-center px-8 py-4 bg-cta text-white font-label text-xs font-semibold uppercase tracking-[0.15em] hover:bg-cta-hover transition-colors text-center" href="<?= htmlspecialchars($settings['hero_cta1_href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($settings['hero_cta1_text'], ENT_QUOTES, 'UTF-8') ?></a>
            <a class="inline-flex items-center justify-center px-8 py-4 bg-transparent text-primary font-label text-xs font-semibold uppercase tracking-[0.15em] hover:bg-surface-dim transition-colors border border-outline text-center" href="<?= htmlspecialchars($settings['hero_cta2_href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($settings['hero_cta2_text'], ENT_QUOTES, 'UTF-8') ?></a>
          </div>
        </div>
      </div>
    </section>

    <!-- STATS -->
    <section class="bg-primary-dark text-white py-12 border-b border-outline-variant">
      <div class="px-4 md:px-12 max-w-container mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:divide-x md:divide-white/20">
          <?php foreach ($stats as $stat): ?>
            <div class="text-center px-4">
              <?php if ($stat['count_target'] !== null): ?>
                <div class="text-4xl md:text-[40px] font-extrabold font-display mb-2 stat-val" data-target="<?= (int) $stat['count_target'] ?>">0<span class="text-secondary-light"><?= htmlspecialchars((string) $stat['suffix'], ENT_QUOTES, 'UTF-8') ?></span></div>
              <?php else: ?>
                <div class="text-4xl md:text-[40px] font-extrabold font-display mb-2"><?= htmlspecialchars($stat['value_display'], ENT_QUOTES, 'UTF-8') ?><span class="text-secondary-light"></span></div>
              <?php endif; ?>
              <div class="font-label text-xs font-semibold uppercase tracking-[0.15em] text-white/70"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="py-24 bg-surface border-b border-outline-variant" id="about">
      <div class="px-4 md:px-12 max-w-container mx-auto grid md:grid-cols-2 gap-6 items-center">
        <div class="reveal relative h-[500px] border border-outline-variant p-2 bg-white">
          <div class="grid grid-cols-2 grid-rows-2 gap-2 w-full h-full">
            <div class="row-span-2 relative overflow-hidden group">
              <div class="w-full h-full bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-1000" style="background-image: url('<?= htmlspecialchars($settings['about_image_path'], ENT_QUOTES, 'UTF-8') ?>')"></div>
            </div>
            <div class="relative overflow-hidden group">
              <div class="w-full h-full bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-1000" style="background-image: url('https://images.unsplash.com/photo-1642749776312-aa42ce20c9f5?w=600&h=600&fit=crop&q=80')"></div>
            </div>
            <div class="relative overflow-hidden group">
              <div class="w-full h-full bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-1000" style="background-image: url('https://images.unsplash.com/photo-1634114582073-c34f96202b65?w=600&h=600&fit=crop&q=80')"></div>
            </div>
          </div>
        </div>
        <div class="reveal grid gap-6 md:pl-12" style="transition-delay:120ms">
          <span class="font-label text-xs font-semibold text-secondary tracking-[0.15em] uppercase"><?= htmlspecialchars($settings['about_eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
          <h2 class="text-3xl font-bold font-display text-primary leading-tight"><?= htmlspecialchars($settings['about_heading'], ENT_QUOTES, 'UTF-8') ?></h2>
          <p class="text-lg text-on-surface-variant leading-relaxed">
            <?= htmlspecialchars($settings['about_paragraph1'], ENT_QUOTES, 'UTF-8') ?>
          </p>
          <p class="text-base text-on-surface-variant">
            <?= htmlspecialchars($settings['about_paragraph2'], ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
            <?php foreach ($aboutChecklist as $item): ?>
              <li class="flex items-center gap-3 text-primary"><span class="material-symbols-outlined text-secondary"><?= htmlspecialchars($item['icon_name'], ENT_QUOTES, 'UTF-8') ?></span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </section>

    <!-- SERVICES -->
    <section class="py-24 bg-white border-b border-outline-variant" id="services">
      <div class="px-4 md:px-12 max-w-container mx-auto">
        <div class="reveal text-center mb-16 max-w-3xl mx-auto">
          <span class="font-label text-xs font-semibold text-secondary tracking-[0.15em] uppercase block mb-4"><?= htmlspecialchars($settings['services_eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
          <h2 class="text-3xl font-bold font-display text-primary mb-6"><?= htmlspecialchars($settings['services_heading'], ENT_QUOTES, 'UTF-8') ?></h2>
          <p class="text-lg text-on-surface-variant"><?= htmlspecialchars($settings['services_intro'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($services as $i => $service): ?>
            <div class="reveal group border border-outline-variant bg-surface p-8 hover:border-secondary transition-colors duration-300" style="transition-delay:<?= ($i % 3) * 80 ?>ms">
              <div class="w-14 h-14 bg-surface-dim flex items-center justify-center mb-6 group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                <span class="material-symbols-outlined text-3xl"><?= htmlspecialchars($service['icon_name'], ENT_QUOTES, 'UTF-8') ?></span>
              </div>
              <h3 class="text-xl font-semibold font-display text-primary mb-4"><?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="text-on-surface-variant"><?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- PROJECTS -->
    <section class="py-24 bg-surface border-b border-outline-variant" id="projects">
      <div class="px-4 md:px-12 max-w-container mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
          <div class="reveal max-w-2xl">
            <span class="font-label text-xs font-semibold text-secondary tracking-[0.15em] uppercase block mb-4"><?= htmlspecialchars($settings['projects_eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
            <h2 class="text-3xl font-bold font-display text-primary"><?= htmlspecialchars($settings['projects_heading'], ENT_QUOTES, 'UTF-8') ?></h2>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 md:auto-rows-[220px]">
          <?php foreach ($projects as $i => $project): $slug = fam_slug($project['title']); ?>
            <div class="reveal group relative overflow-hidden bg-surface-dim <?= $projectSpanClasses[$i] ?? $defaultProjectSpan ?> cursor-pointer" style="transition-delay:<?= ($i % 3) * 80 ?>ms" data-project="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>" role="button" tabindex="0" aria-label="View gallery for <?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>">
              <img src="<?= htmlspecialchars($project['photo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($project['photo_alt'], ENT_QUOTES, 'UTF-8') ?>" class="absolute inset-0 w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-1000" loading="lazy">
              <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/10 to-transparent group-hover:from-primary/95 transition-colors duration-300"></div>
              <span class="absolute top-4 left-4 px-3 py-1 bg-white/85 backdrop-blur-sm font-label text-xs font-semibold text-primary uppercase tracking-wider"><?= htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8') ?></span>
              <span class="material-symbols-outlined absolute top-4 right-4 text-white/90 opacity-0 group-hover:opacity-100 transition-opacity duration-300">photo_library</span>
              <div class="absolute bottom-0 left-0 right-0 p-5">
                <h3 class="text-xl font-semibold font-display text-white mb-1"><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="text-sm text-white/80"><?= htmlspecialchars($project['subtitle'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- BRANDS -->
    <section class="py-20 bg-white border-b border-outline-variant" id="brands">
      <div class="px-4 md:px-12 max-w-container mx-auto">
        <div class="reveal text-center mb-12">
          <span class="font-label text-xs font-semibold text-secondary tracking-[0.15em] uppercase block mb-4"><?= htmlspecialchars($settings['brands_eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
          <h2 class="text-3xl font-bold font-display text-primary"><?= htmlspecialchars($settings['brands_heading'], ENT_QUOTES, 'UTF-8') ?></h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <?php foreach ($brands as $i => $brand): $isPng = strtolower(pathinfo($brand['logo_path'], PATHINFO_EXTENSION)) === 'png'; ?>
            <div class="reveal flex items-center justify-center p-8 border border-outline-variant bg-surface hover:border-secondary hover:shadow-md transition-all duration-300 group" style="transition-delay:<?= ($i % 4) * 60 ?>ms">
              <img src="<?= htmlspecialchars($brand['logo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8') ?>" class="<?= $isPng ? 'h-16 max-w-[180px]' : 'h-10 max-w-[140px]' ?> w-auto object-contain grayscale group-hover:grayscale-0 transition-all duration-1000" loading="lazy">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section class="py-24 bg-white" id="contact">
      <div class="px-4 md:px-12 max-w-container mx-auto grid md:grid-cols-2 gap-12">
        <div class="reveal">
          <span class="font-label text-xs font-semibold text-secondary tracking-[0.15em] uppercase block mb-4"><?= htmlspecialchars($settings['contact_eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
          <h2 class="text-3xl font-bold font-display text-primary mb-6"><?= htmlspecialchars($settings['contact_heading'], ENT_QUOTES, 'UTF-8') ?></h2>
          <p class="text-on-surface-variant mb-12"><?= htmlspecialchars($settings['contact_intro'], ENT_QUOTES, 'UTF-8') ?></p>
          <div class="grid gap-8">
            <?php foreach ($contactBlocks as $block): ?>
              <div class="flex items-start gap-4">
                <span class="material-symbols-outlined text-secondary text-3xl mt-1"><?= htmlspecialchars($block['icon_name'], ENT_QUOTES, 'UTF-8') ?></span>
                <div>
                  <h4 class="font-semibold text-primary mb-1"><?= htmlspecialchars($block['label'], ENT_QUOTES, 'UTF-8') ?></h4>
                  <p class="text-on-surface-variant"><?= nl2br(htmlspecialchars($block['value_text'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="reveal bg-surface border border-outline-variant p-8 relative" style="transition-delay:120ms">
          <div class="absolute top-0 left-0 w-full h-1 bg-cta"></div>

          <?php if ($contactSuccess): ?>
            <div class="mb-6 flex items-start gap-3 border border-green-300 bg-green-50 text-green-800 px-4 py-3 text-sm" role="status">
              <span class="material-symbols-outlined text-xl">check_circle</span>
              <span>Thanks — your inquiry has been sent. We'll get back to you shortly.</span>
            </div>
          <?php endif; ?>
          <?php if ($contactError): ?>
            <div class="mb-6 flex items-start gap-3 border border-red-300 bg-red-50 text-red-700 px-4 py-3 text-sm" role="alert">
              <span class="material-symbols-outlined text-xl">error</span>
              <span><?= htmlspecialchars($contactError, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
          <?php endif; ?>

          <form class="grid gap-6" id="contactForm" method="post" action="#contact">
            <?= fam_csrf_field() ?>
            <input type="hidden" name="contact_submit" value="1">
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block font-label text-xs font-semibold text-primary mb-2 uppercase tracking-wider" for="cf-full-name">Full Name</label>
                <input class="w-full bg-white border border-outline-variant px-4 py-3 focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors text-base" id="cf-full-name" placeholder="Juan Dela Cruz" name="full_name" type="text" autocomplete="name" required>
              </div>
              <div>
                <label class="block font-label text-xs font-semibold text-primary mb-2 uppercase tracking-wider" for="cf-phone">Phone</label>
                <input class="w-full bg-white border border-outline-variant px-4 py-3 focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors text-base" id="cf-phone" placeholder="(0917) 123 4567" name="phone" type="tel" autocomplete="tel">
              </div>
            </div>
            <div>
              <label class="block font-label text-xs font-semibold text-primary mb-2 uppercase tracking-wider" for="cf-email">Email Address</label>
              <input class="w-full bg-white border border-outline-variant px-4 py-3 focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors text-base" id="cf-email" placeholder="juan@email.com" name="email" type="email" autocomplete="email" required>
            </div>
            <div>
              <label class="block font-label text-xs font-semibold text-primary mb-2 uppercase tracking-wider" for="cf-service">Service Needed</label>
              <select class="w-full bg-white border border-outline-variant px-4 py-3 focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors text-base" id="cf-service" name="service_needed" required>
                <option value="" disabled selected>Select a service</option>
                <?php foreach ($services as $service): ?>
                  <option value="<?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block font-label text-xs font-semibold text-primary mb-2 uppercase tracking-wider" for="cf-details">Project Details</label>
              <textarea class="w-full bg-white border border-outline-variant px-4 py-3 focus:border-secondary focus:ring-1 focus:ring-secondary transition-colors text-base" id="cf-details" placeholder="Building size, current system, timeline..." name="project_details" rows="4"></textarea>
            </div>
            <button class="w-full bg-cta text-white font-label text-xs font-semibold py-4 uppercase tracking-[0.15em] hover:bg-cta-hover transition-colors mt-2 cursor-pointer" type="submit">Submit Inquiry</button>
          </form>
        </div>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <footer class="bg-primary-dark text-white border-t-4 border-cta">
    <div class="w-full px-4 md:px-12 py-12 max-w-container mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="md:col-span-1">
        <span class="text-xl font-bold font-display block mb-4"><?= htmlspecialchars($settings['company_name'], ENT_QUOTES, 'UTF-8') ?></span>
        <p class="text-sm text-white/70 mb-2"><?= htmlspecialchars($settings['hero_heading_line1'] . ' ' . $settings['hero_heading_line2'], ENT_QUOTES, 'UTF-8') ?></p>
        <p class="text-sm text-white/60"><?= htmlspecialchars($settings['footer_blurb'], ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <div>
        <h4 class="font-label text-xs font-semibold mb-4 uppercase tracking-[0.15em] text-white/80">Navigation</h4>
        <ul class="grid gap-3 text-sm">
          <?php foreach ($navLinks as $link): ?>
            <li><a class="text-white/70 hover:text-cta transition-colors" href="<?= htmlspecialchars($link['href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="font-label text-xs font-semibold mb-4 uppercase tracking-[0.15em] text-white/80">Brands</h4>
        <ul class="grid gap-3 text-sm">
          <li class="text-white/70">All Major Brands</li>
          <?php foreach ($brandChunks as $chunk): ?>
            <li class="text-white/70"><?= htmlspecialchars(implode(', ', array_column($chunk, 'name')), ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="font-label text-xs font-semibold mb-4 uppercase tracking-[0.15em] text-white/80">Connect</h4>
        <div class="flex gap-4 text-white/60">
          <?php if (!empty($settings['social_facebook_url'])): ?>
            <a href="<?= htmlspecialchars($settings['social_facebook_url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Facebook" class="material-symbols-outlined text-3xl hover:text-cta transition-colors cursor-pointer">public</a>
          <?php else: ?>
            <span class="material-symbols-outlined text-3xl">public</span>
          <?php endif; ?>
          <?php if (!empty($settings['social_linkedin_url'])): ?>
            <a href="<?= htmlspecialchars($settings['social_linkedin_url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="LinkedIn" class="material-symbols-outlined text-3xl hover:text-cta transition-colors cursor-pointer">group</a>
          <?php else: ?>
            <span class="material-symbols-outlined text-3xl">group</span>
          <?php endif; ?>
          <?php if (!empty($settings['social_email'])): ?>
            <a href="mailto:<?= htmlspecialchars($settings['social_email'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Email" class="material-symbols-outlined text-3xl hover:text-cta transition-colors cursor-pointer">mail</a>
          <?php else: ?>
            <span class="material-symbols-outlined text-3xl">mail</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="w-full px-4 md:px-12 py-6 max-w-container mx-auto border-t border-white/10 text-center text-xs text-white/40">
      <?= htmlspecialchars($settings['copyright_text'], ENT_QUOTES, 'UTF-8') ?>
    </div>
  </footer>

  <div id="galleryModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-on-surface/95" data-modal-close></div>
    <button id="galleryClose" class="absolute top-6 right-6 md:top-8 md:right-8 text-white/80 hover:text-white z-10" aria-label="Close gallery" type="button">
      <span class="material-symbols-outlined text-3xl">close</span>
    </button>
    <button id="galleryPrev" class="absolute left-2 md:left-6 top-1/2 -translate-y-1/2 text-white/80 hover:text-white z-10" aria-label="Previous photo" type="button">
      <span class="material-symbols-outlined text-4xl">chevron_left</span>
    </button>
    <button id="galleryNext" class="absolute right-2 md:right-6 top-1/2 -translate-y-1/2 text-white/80 hover:text-white z-10" aria-label="Next photo" type="button">
      <span class="material-symbols-outlined text-4xl">chevron_right</span>
    </button>
    <div class="gallery-panel relative h-full flex flex-col items-center justify-center px-4 md:px-20 py-16 pointer-events-none">
      <img id="galleryImage" src="" alt="" class="max-h-[70vh] max-w-full object-contain pointer-events-auto">
      <div class="mt-6 text-center">
        <span id="galleryBadge" class="inline-block mb-3 px-3 py-1 bg-white/10 font-label text-xs font-semibold text-white uppercase tracking-wider"></span>
        <h3 id="galleryTitle" class="text-xl font-semibold font-display text-white"></h3>
        <p id="galleryCounter" class="font-label text-xs text-white/60 uppercase tracking-wider mt-2"></p>
      </div>
    </div>
  </div>

  <script src="js/main.js"></script>
</body>
</html>
