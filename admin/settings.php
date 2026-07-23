<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

fam_require_login();

$fields = [
    'company_name' => ['label' => 'Company Name', 'type' => 'text'],
    'logo_path' => ['label' => 'Logo Path', 'type' => 'text'],
    'hero_eyebrow' => ['label' => 'Hero Eyebrow', 'type' => 'text'],
    'hero_heading_line1' => ['label' => 'Hero Heading Line 1', 'type' => 'text'],
    'hero_heading_line2' => ['label' => 'Hero Heading Line 2', 'type' => 'text'],
    'hero_subtitle' => ['label' => 'Hero Subtitle', 'type' => 'textarea'],
    'hero_bg_image_path' => ['label' => 'Hero Background Image Path/URL', 'type' => 'text'],
    'hero_cta1_text' => ['label' => 'Hero CTA 1 Text', 'type' => 'text'],
    'hero_cta1_href' => ['label' => 'Hero CTA 1 Href', 'type' => 'text'],
    'hero_cta2_text' => ['label' => 'Hero CTA 2 Text', 'type' => 'text'],
    'hero_cta2_href' => ['label' => 'Hero CTA 2 Href', 'type' => 'text'],
    'about_eyebrow' => ['label' => 'About Eyebrow', 'type' => 'text'],
    'about_heading' => ['label' => 'About Heading', 'type' => 'text'],
    'about_paragraph1' => ['label' => 'About Paragraph 1', 'type' => 'textarea'],
    'about_paragraph2' => ['label' => 'About Paragraph 2', 'type' => 'textarea'],
    'about_image_path' => ['label' => 'About Image Path/URL', 'type' => 'text'],
    'services_eyebrow' => ['label' => 'Services Eyebrow', 'type' => 'text'],
    'services_heading' => ['label' => 'Services Heading', 'type' => 'text'],
    'services_intro' => ['label' => 'Services Intro', 'type' => 'textarea'],
    'brands_eyebrow' => ['label' => 'Brands Eyebrow', 'type' => 'text'],
    'brands_heading' => ['label' => 'Brands Heading', 'type' => 'text'],
    'projects_eyebrow' => ['label' => 'Projects Eyebrow', 'type' => 'text'],
    'projects_heading' => ['label' => 'Projects Heading', 'type' => 'text'],
    'contact_eyebrow' => ['label' => 'Contact Eyebrow', 'type' => 'text'],
    'contact_heading' => ['label' => 'Contact Heading', 'type' => 'text'],
    'contact_intro' => ['label' => 'Contact Intro', 'type' => 'textarea'],
    'contact_recipient_email' => ['label' => 'Contact Recipient Email', 'type' => 'text'],
    'footer_blurb' => ['label' => 'Footer Blurb', 'type' => 'textarea'],
    'copyright_text' => ['label' => 'Copyright Text', 'type' => 'text'],
    'social_facebook_url' => ['label' => 'Facebook URL', 'type' => 'text'],
    'social_linkedin_url' => ['label' => 'LinkedIn URL', 'type' => 'text'],
    'social_email' => ['label' => 'Social/Public Email', 'type' => 'text'],
];

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
        }
        $setSql = implode(', ', array_map(fn($c) => "{$c} = ?", $cols));
        $stmt = fam_db()->prepare("UPDATE site_settings SET {$setSql} WHERE id = 1");
        $stmt->execute(array_values($vals));
        $saved = true;
    }
}

$settings = fetchSettings();

require __DIR__ . '/includes/admin_header.php';
?>
<h1 class="text-2xl font-bold text-primary mb-6">Settings</h1>

<?php if ($error): ?>
  <p class="mb-4 text-sm text-red-600"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<?php if ($saved): ?>
  <p class="mb-4 text-sm text-green-700">Settings saved.</p>
<?php endif; ?>

<form method="post" class="grid gap-4 max-w-2xl">
  <?= fam_csrf_field() ?>
  <?php foreach ($fields as $col => $spec): ?>
    <div>
      <label class="block text-sm text-on-surface-variant mb-1"><?= htmlspecialchars($spec['label'], ENT_QUOTES, 'UTF-8') ?></label>
      <?php $val = $settings[$col] ?? ''; ?>
      <?php if ($spec['type'] === 'textarea'): ?>
        <textarea name="<?= $col ?>" rows="3" class="w-full border border-outline-variant px-3 py-2"><?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?></textarea>
      <?php else: ?>
        <input type="text" name="<?= $col ?>" value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-outline-variant px-3 py-2">
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <button type="submit" class="bg-cta hover:bg-cta-hover text-white px-6 py-2 font-label text-xs uppercase tracking-[0.15em] w-fit">Save Settings</button>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
