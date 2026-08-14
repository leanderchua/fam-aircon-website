<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

fam_require_login();

// Grouped into sections purely for admin readability — all columns still live on the single site_settings row.
$sections = [
    'Company' => [
        'company_name' => ['label' => 'Company Name', 'type' => 'text', 'required' => true],
        'logo_path' => ['label' => 'Logo', 'type' => 'image'],
    ],
    'Hero Section' => [
        'hero_eyebrow' => ['label' => 'Hero Eyebrow', 'type' => 'text'],
        'hero_heading_line1' => ['label' => 'Hero Heading Line 1', 'type' => 'text'],
        'hero_heading_line2' => ['label' => 'Hero Heading Line 2', 'type' => 'text'],
        'hero_subtitle' => ['label' => 'Hero Subtitle', 'type' => 'textarea'],
        'hero_bg_image_path' => ['label' => 'Hero Background Image', 'type' => 'image'],
        'hero_cta1_text' => ['label' => 'Hero CTA 1 Text', 'type' => 'text'],
        'hero_cta1_href' => ['label' => 'Hero CTA 1 Href', 'type' => 'text'],
        'hero_cta2_text' => ['label' => 'Hero CTA 2 Text', 'type' => 'text'],
        'hero_cta2_href' => ['label' => 'Hero CTA 2 Href', 'type' => 'text'],
    ],
    'About Section' => [
        'about_eyebrow' => ['label' => 'About Eyebrow', 'type' => 'text'],
        'about_heading' => ['label' => 'About Heading', 'type' => 'text'],
        'about_paragraph1' => ['label' => 'About Paragraph 1', 'type' => 'textarea'],
        'about_paragraph2' => ['label' => 'About Paragraph 2', 'type' => 'textarea'],
        'about_image_path' => ['label' => 'About Image', 'type' => 'image'],
    ],
    'Services Section' => [
        'services_eyebrow' => ['label' => 'Services Eyebrow', 'type' => 'text'],
        'services_heading' => ['label' => 'Services Heading', 'type' => 'text'],
        'services_intro' => ['label' => 'Services Intro', 'type' => 'textarea'],
    ],
    'Brands Section' => [
        'brands_eyebrow' => ['label' => 'Brands Eyebrow', 'type' => 'text'],
        'brands_heading' => ['label' => 'Brands Heading', 'type' => 'text'],
    ],
    'Projects Section' => [
        'projects_eyebrow' => ['label' => 'Projects Eyebrow', 'type' => 'text'],
        'projects_heading' => ['label' => 'Projects Heading', 'type' => 'text'],
    ],
    'Contact Section' => [
        'contact_eyebrow' => ['label' => 'Contact Eyebrow', 'type' => 'text'],
        'contact_heading' => ['label' => 'Contact Heading', 'type' => 'text'],
        'contact_intro' => ['label' => 'Contact Intro', 'type' => 'textarea'],
        'contact_recipient_email' => ['label' => 'Contact Recipient Email(s)', 'type' => 'text', 'required' => true, 'hint' => 'Comma-separate multiple addresses, e.g. sales@famaircon.com, owner@famaircon.com'],
    ],
    'Footer & Social' => [
        'footer_blurb' => ['label' => 'Footer Blurb', 'type' => 'textarea'],
        'copyright_text' => ['label' => 'Copyright Text', 'type' => 'text'],
        'social_facebook_url' => ['label' => 'Facebook URL', 'type' => 'url'],
        'social_linkedin_url' => ['label' => 'LinkedIn URL', 'type' => 'url'],
        'social_email' => ['label' => 'Social/Public Email', 'type' => 'email'],
    ],
];

// Flatten for POST handling.
$fields = array_merge(...array_values($sections));

$error = null;
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!fam_verify_csrf()) {
        $error = 'Session expired, please try again.';
    } else {
        $cols = [];
        $vals = [];
        foreach ($fields as $col => $spec) {
            $cols[] = $col;
            $vals[$col] = trim($_POST[$col] ?? '');
            if (!empty($spec['required']) && $vals[$col] === '') {
                $error = "{$spec['label']} is required.";
            }
        }
        if (!$error && $vals['contact_recipient_email'] !== '') {
            $addrs = array_map('trim', explode(',', $vals['contact_recipient_email']));
            foreach ($addrs as $addr) {
                if (!filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                    $error = "\"{$addr}\" is not a valid email address in Contact Recipient Email(s).";
                    break;
                }
            }
            $vals['contact_recipient_email'] = implode(', ', $addrs);
        }
        if (!$error) {
            $setSql = implode(', ', array_map(fn($c) => "{$c} = ?", $cols));
            $stmt = fam_db()->prepare("UPDATE site_settings SET {$setSql} WHERE id = 1");
            $stmt->execute(array_values($vals));
            $saved = true;
        }
    }
}

$settings = fetchSettings();
$famPageTitle = 'Settings';

require __DIR__ . '/includes/admin_header.php';
?>
<h1 class="text-2xl font-bold text-primary mb-6">Settings</h1>

<?php if ($error): ?>
  <div class="mb-6 flex items-start gap-3 border border-red-300 bg-red-50 text-red-700 px-4 py-3 text-sm" role="alert">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
    <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
  </div>
<?php elseif ($saved): ?>
  <div class="mb-6 flex items-start gap-3 border border-green-300 bg-green-50 text-green-800 px-4 py-3 text-sm" role="status">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    <span>Settings saved.</span>
  </div>
<?php endif; ?>

<form method="post" class="max-w-2xl">
  <?= fam_csrf_field() ?>

  <?php foreach ($sections as $sectionTitle => $sectionFields): ?>
    <fieldset class="mb-8 border border-outline-variant bg-surface-bright p-6">
      <legend class="text-lg font-semibold text-primary px-1 -ml-1 mb-2"><?= htmlspecialchars($sectionTitle, ENT_QUOTES, 'UTF-8') ?></legend>
      <div class="grid gap-4 mt-3">
        <?php foreach ($sectionFields as $col => $spec): ?>
          <div>
            <label for="field_<?= $col ?>" class="block text-sm text-on-surface-variant mb-1">
              <?= htmlspecialchars($spec['label'], ENT_QUOTES, 'UTF-8') ?>
              <?php if (!empty($spec['required'])): ?><span class="text-red-600" aria-hidden="true"> *</span><?php endif; ?>
            </label>
            <?php $val = $settings[$col] ?? ''; ?>
            <?php if ($spec['type'] === 'textarea'): ?>
              <textarea id="field_<?= $col ?>" name="<?= $col ?>" rows="3" <?= !empty($spec['required']) ? 'required aria-required="true"' : '' ?> class="w-full border border-outline-variant px-3 py-2 focus-visible:border-secondary"><?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php elseif ($spec['type'] === 'image'): ?>
              <div class="flex items-start gap-3">
                <div class="relative w-20 h-20 shrink-0 border border-outline-variant bg-surface flex items-center justify-center overflow-hidden">
                  <img id="preview_<?= $col ?>" src="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" alt="" class="w-full h-full object-cover <?= $val === '' ? 'hidden' : '' ?>">
                  <span data-preview-placeholder class="text-on-surface-variant/50 <?= $val !== '' ? 'hidden' : '' ?>"><?= fam_icon('image-off', 'w-6 h-6') ?></span>
                </div>
                <input type="text" id="field_<?= $col ?>" name="<?= $col ?>" value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" placeholder="images/example.jpg or https://..." data-preview-target="preview_<?= $col ?>" class="flex-1 h-10 border border-outline-variant px-3 focus-visible:border-secondary">
              </div>
              <p class="text-xs text-on-surface-variant/70 mt-1">Paste an image path or URL — a real upload button is coming in a later update.</p>
            <?php else: ?>
              <input type="<?= htmlspecialchars($spec['type'], ENT_QUOTES, 'UTF-8') ?>" id="field_<?= $col ?>" name="<?= $col ?>" value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" <?= !empty($spec['required']) ? 'required aria-required="true"' : '' ?> class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
              <?php if (!empty($spec['hint'])): ?><p class="text-xs text-on-surface-variant/70 mt-1"><?= htmlspecialchars($spec['hint'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </fieldset>
  <?php endforeach; ?>

  <button type="submit" class="bg-cta hover:bg-cta-hover text-white px-6 py-2 font-label text-xs uppercase tracking-[0.15em] transition-colors duration-200 cursor-pointer">Save Settings</button>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
