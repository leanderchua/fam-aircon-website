<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/uploads.php';

fam_require_login();

$pdo = fam_db();
$error = null;
$warning = null;
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!fam_verify_csrf()) {
        $error = 'Session expired, please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {
            $oldRow = null;
            if ($action === 'edit') {
                $oldStmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
                $oldStmt->execute([(int) ($_POST['id'] ?? 0)]);
                $oldRow = $oldStmt->fetch() ?: null;
            }

            $title = trim($_POST['title'] ?? '');
            if ($error === null && $title === '') {
                $error = 'Title is required.';
            }

            $subtitle = trim($_POST['subtitle'] ?? '');
            if ($error === null && $subtitle === '') {
                $error = 'Subtitle is required.';
            }

            $category = trim($_POST['category'] ?? '');
            if ($error === null && !in_array($category, ['Commercial', 'Residential'], true)) {
                $error = 'Category is required.';
            }

            $upload = fam_handle_image_upload('img_upload__photo_path');
            if ($upload['error'] !== null) {
                $photoPath = null;
                if ($error === null) {
                    $error = "Card Image: {$upload['error']}";
                }
            } else {
                $photoPath = $upload['path'] ?? trim($_POST['photo_path'] ?? '');
            }
            if ($error === null && ($photoPath === null || $photoPath === '')) {
                $error = 'Card Image is required.';
            }

            $photoAlt = trim($_POST['photo_alt'] ?? '');
            if ($error === null && $photoAlt === '') {
                $error = 'Photo Alt Text is required.';
            }

            if (!$error) {
                if ($action === 'add') {
                    $maxOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), -1) FROM projects')->fetchColumn();
                    $stmt = $pdo->prepare('INSERT INTO projects (title, subtitle, category, photo_path, photo_alt, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$title, $subtitle, $category, $photoPath, $photoAlt, $maxOrder + 1]);
                    $projectId = (int) $pdo->lastInsertId();
                } else {
                    $projectId = (int) ($_POST['id'] ?? 0);
                    $stmt = $pdo->prepare('UPDATE projects SET title = ?, subtitle = ?, category = ?, photo_path = ?, photo_alt = ? WHERE id = ?');
                    $stmt->execute([$title, $subtitle, $category, $photoPath, $photoAlt, $projectId]);

                    if ($oldRow) {
                        fam_cleanup_old_upload($oldRow['photo_path'] ?? null, $photoPath);
                    }

                    foreach ($_POST['remove_photo_ids'] ?? [] as $removeId) {
                        $removeId = (int) $removeId;
                        $photoStmt = $pdo->prepare('SELECT photo_path FROM project_photos WHERE id = ? AND project_id = ?');
                        $photoStmt->execute([$removeId, $projectId]);
                        $photoRow = $photoStmt->fetch();
                        if ($photoRow) {
                            $del = $pdo->prepare('DELETE FROM project_photos WHERE id = ?');
                            $del->execute([$removeId]);
                            fam_cleanup_old_upload($photoRow['photo_path'], null);
                        }
                    }
                }

                $multi = fam_handle_multiple_image_uploads('gallery_photos');
                if (!empty($multi['paths'])) {
                    $orderStmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM project_photos WHERE project_id = ?');
                    $orderStmt->execute([$projectId]);
                    $nextOrder = (int) $orderStmt->fetchColumn();
                    $insertPhoto = $pdo->prepare('INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order) VALUES (?, ?, ?, ?)');
                    foreach ($multi['paths'] as $n => $path) {
                        $alt = "{$title} — photo " . ($n + 1);
                        $insertPhoto->execute([$projectId, $path, $alt, $nextOrder + 1 + $n]);
                    }
                }
                if (!empty($multi['errors'])) {
                    $warning = 'Some gallery photos were skipped: ' . implode('; ', $multi['errors']);
                }

                $saved = true;
            }
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $projStmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
            $projStmt->execute([$id]);
            $project = $projStmt->fetch();

            $photosStmt = $pdo->prepare('SELECT * FROM project_photos WHERE project_id = ?');
            $photosStmt->execute([$id]);
            $photos = $photosStmt->fetchAll();

            if ($project) {
                fam_cleanup_old_upload($project['photo_path'] ?? null, null);
            }
            foreach ($photos as $photo) {
                fam_cleanup_old_upload($photo['photo_path'] ?? null, null);
            }

            $del = $pdo->prepare('DELETE FROM projects WHERE id = ?');
            $del->execute([$id]);
            $saved = true;
        } elseif ($action === 'move_up' || $action === 'move_down') {
            $id = (int) ($_POST['id'] ?? 0);
            $cur = $pdo->prepare('SELECT id, sort_order FROM projects WHERE id = ?');
            $cur->execute([$id]);
            $row = $cur->fetch();
            if ($row) {
                $cmp = $action === 'move_up' ? '<' : '>';
                $order = $action === 'move_up' ? 'DESC' : 'ASC';
                $neighborStmt = $pdo->prepare("SELECT id, sort_order FROM projects WHERE sort_order {$cmp} ? ORDER BY sort_order {$order} LIMIT 1");
                $neighborStmt->execute([$row['sort_order']]);
                $neighbor = $neighborStmt->fetch();
                if ($neighbor) {
                    $swap = $pdo->prepare('UPDATE projects SET sort_order = ? WHERE id = ?');
                    $swap->execute([$neighbor['sort_order'], $row['id']]);
                    $swap->execute([$row['sort_order'], $neighbor['id']]);
                }
            }
        }
    }
}

$projects = $pdo->query('SELECT * FROM projects ORDER BY sort_order')->fetchAll();

$photoCounts = [];
foreach ($pdo->query('SELECT project_id, COUNT(*) AS c FROM project_photos GROUP BY project_id') as $countRow) {
    $photoCounts[(int) $countRow['project_id']] = (int) $countRow['c'];
}

$editRow = null;
$editPhotos = [];
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch() ?: null;
    if ($editRow) {
        $photosStmt = $pdo->prepare('SELECT * FROM project_photos WHERE project_id = ? ORDER BY sort_order');
        $photosStmt->execute([$editRow['id']]);
        $editPhotos = $photosStmt->fetchAll();
    }
}

$famPageTitle = 'Projects';
require __DIR__ . '/includes/admin_header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-primary">Projects</h1>
  <a href="#famForm" class="inline-flex items-center gap-2 bg-cta hover:bg-cta-hover text-white px-4 py-2 font-label text-xs uppercase tracking-[0.15em] transition-colors duration-200 cursor-pointer">
    <?= fam_icon('plus', 'w-4 h-4') ?> Add New
  </a>
</div>

<?php if ($error): ?>
  <div class="mb-6 flex items-start gap-3 border border-red-300 bg-red-50 text-red-700 px-4 py-3 text-sm" role="alert">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
    <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
  </div>
<?php elseif ($saved): ?>
  <div class="mb-6 flex items-start gap-3 border border-green-300 bg-green-50 text-green-800 px-4 py-3 text-sm" role="status">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    <span>Saved successfully.</span>
  </div>
<?php endif; ?>

<?php if ($warning): ?>
  <div class="mb-6 flex items-start gap-3 border border-amber-300 bg-amber-50 text-amber-800 px-4 py-3 text-sm" role="status">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
    <span><?= htmlspecialchars($warning, ENT_QUOTES, 'UTF-8') ?></span>
  </div>
<?php endif; ?>

<div class="overflow-x-auto border border-outline-variant bg-surface-bright mb-10">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-surface text-left">
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Card Image</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Title</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Subtitle</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Category</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Gallery</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($projects)): ?>
        <tr>
          <td colspan="6" class="px-3 py-10 text-center text-on-surface-variant">No projects yet — add one using the form below.</td>
        </tr>
      <?php endif; ?>
      <?php foreach ($projects as $row): $count = $photoCounts[(int) $row['id']] ?? 0; ?>
        <tr class="border-b border-outline-variant last:border-b-0 hover:bg-surface/60 transition-colors duration-200">
          <td class="px-3 py-2 align-top">
            <?php if ($row['photo_path'] !== ''): ?>
              <img src="<?= htmlspecialchars($row['photo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="w-12 h-12 object-cover rounded border border-outline-variant bg-surface" onerror="this.style.display='none'">
            <?php else: ?>
              <span class="text-on-surface-variant/60 text-xs">No image</span>
            <?php endif; ?>
          </td>
          <td class="px-3 py-2 align-top font-medium"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="px-3 py-2 align-top text-on-surface-variant"><?= htmlspecialchars($row['subtitle'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="px-3 py-2 align-top"><?= htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="px-3 py-2 align-top">
            <?php if ($count > 0): ?>
              <span class="text-on-surface-variant"><?= $count ?> photo<?= $count === 1 ? '' : 's' ?></span>
            <?php else: ?>
              <span class="text-on-surface-variant/60 text-xs">Not clickable — no gallery photos</span>
            <?php endif; ?>
          </td>
          <td class="px-3 py-2 whitespace-nowrap">
            <a class="inline-flex items-center gap-1 text-secondary hover:underline mr-3 cursor-pointer" href="?edit=<?= (int) $row['id'] ?>">
              <?= fam_icon('edit', 'w-4 h-4') ?><span>Edit</span>
            </a>
            <form method="post" class="inline">
              <?= fam_csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
              <input type="hidden" name="action" value="move_up">
              <button type="submit" aria-label="Move up" class="p-1 text-on-surface-variant hover:text-secondary hover:bg-surface rounded transition-colors duration-200 cursor-pointer"><?= fam_icon('up', 'w-4 h-4') ?></button>
            </form>
            <form method="post" class="inline">
              <?= fam_csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
              <input type="hidden" name="action" value="move_down">
              <button type="submit" aria-label="Move down" class="p-1 text-on-surface-variant hover:text-secondary hover:bg-surface rounded transition-colors duration-200 cursor-pointer"><?= fam_icon('down', 'w-4 h-4') ?></button>
            </form>
            <form method="post" class="inline" onsubmit="return confirm('Delete this project? Its Card Image and all gallery photos will also be removed. This cannot be undone.');">
              <?= fam_csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" aria-label="Delete project" class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors duration-200 cursor-pointer"><?= fam_icon('trash', 'w-4 h-4') ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div id="famForm" class="bg-surface-bright border border-outline-variant p-6 max-w-2xl scroll-mt-24">
  <h2 class="text-lg font-semibold text-primary mb-4"><?= $editRow ? 'Edit Project' : 'Add New Project' ?></h2>
  <form method="post" enctype="multipart/form-data" class="grid gap-4">
    <?= fam_csrf_field() ?>
    <input type="hidden" name="action" value="<?= $editRow ? 'edit' : 'add' ?>">
    <?php if ($editRow): ?>
      <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
    <?php endif; ?>

    <div>
      <label for="field_title" class="block text-sm text-on-surface-variant mb-1">Title <span class="text-red-600" aria-hidden="true">*</span></label>
      <input type="text" id="field_title" name="title" value="<?= htmlspecialchars((string) ($editRow['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
    </div>

    <div>
      <label for="field_subtitle" class="block text-sm text-on-surface-variant mb-1">Subtitle <span class="text-red-600" aria-hidden="true">*</span></label>
      <input type="text" id="field_subtitle" name="subtitle" value="<?= htmlspecialchars((string) ($editRow['subtitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
    </div>

    <div>
      <label for="field_category" class="block text-sm text-on-surface-variant mb-1">Category <span class="text-red-600" aria-hidden="true">*</span></label>
      <select id="field_category" name="category" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
        <?php foreach (['Commercial', 'Residential'] as $opt): ?>
          <option value="<?= $opt ?>" <?= (($editRow['category'] ?? '') === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="field_photo_path" class="block text-sm text-on-surface-variant mb-1">Card Image <span class="text-red-600" aria-hidden="true">*</span></label>
      <?php $cardVal = (string) ($editRow['photo_path'] ?? ''); ?>
      <div class="flex items-start gap-3">
        <div class="relative w-20 h-20 shrink-0 border border-outline-variant bg-surface flex items-center justify-center overflow-hidden">
          <img id="preview_photo_path" src="<?= htmlspecialchars($cardVal, ENT_QUOTES, 'UTF-8') ?>" alt="" class="w-full h-full object-cover <?= $cardVal === '' ? 'hidden' : '' ?>">
          <span data-preview-placeholder class="text-on-surface-variant/50 <?= $cardVal !== '' ? 'hidden' : '' ?>"><?= fam_icon('image-off', 'w-6 h-6') ?></span>
        </div>
        <input type="text" id="field_photo_path" name="photo_path" value="<?= htmlspecialchars($cardVal, ENT_QUOTES, 'UTF-8') ?>" placeholder="images/example.jpg or https://..." data-preview-target="preview_photo_path" class="flex-1 h-10 border border-outline-variant px-3 focus-visible:border-secondary">
      </div>
      <input type="file" name="img_upload__photo_path" accept="image/png,image/jpeg,image/webp" data-preview-target="preview_photo_path" class="mt-2 block w-full text-sm text-on-surface-variant file:mr-3 file:py-2 file:px-3 file:border-0 file:bg-surface-dim file:text-on-surface file:cursor-pointer">
      <p class="text-xs text-on-surface-variant/70 mt-1">Shown in the Projects grid on the main site. Upload a JPEG, PNG, or WebP (max 5 MB) — or paste a path/URL above.</p>
    </div>

    <div>
      <label for="field_photo_alt" class="block text-sm text-on-surface-variant mb-1">Photo Alt Text <span class="text-red-600" aria-hidden="true">*</span></label>
      <input type="text" id="field_photo_alt" name="photo_alt" value="<?= htmlspecialchars((string) ($editRow['photo_alt'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
    </div>

    <fieldset class="border border-outline-variant p-4">
      <legend class="text-sm font-semibold text-primary px-1">Gallery Images</legend>

      <?php if ($editRow): ?>
        <?php if (!empty($editPhotos)): ?>
          <div class="grid grid-cols-4 gap-3 mb-4">
            <?php foreach ($editPhotos as $photo): ?>
              <label class="relative block cursor-pointer">
                <img src="<?= htmlspecialchars($photo['photo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="w-full aspect-square object-cover border border-outline-variant bg-surface" onerror="this.style.display='none'">
                <span class="mt-1 flex items-center gap-1 text-xs text-red-600">
                  <input type="checkbox" name="remove_photo_ids[]" value="<?= (int) $photo['id'] ?>" class="cursor-pointer">
                  Remove
                </span>
              </label>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-xs text-on-surface-variant/70 mb-3">No gallery photos yet.</p>
        <?php endif; ?>
      <?php endif; ?>

      <label for="field_gallery_photos" class="block text-sm text-on-surface-variant mb-1">Add Gallery Photos</label>
      <input type="file" id="field_gallery_photos" name="gallery_photos[]" multiple accept="image/png,image/jpeg,image/webp" class="block w-full text-sm text-on-surface-variant file:mr-3 file:py-2 file:px-3 file:border-0 file:bg-surface-dim file:text-on-surface file:cursor-pointer">
      <p class="text-xs text-on-surface-variant/70 mt-1">Shown when a visitor clicks this project. A project with no gallery photos isn't clickable on the site.</p>
    </fieldset>

    <div class="flex gap-3 mt-2">
      <button type="submit" class="bg-cta hover:bg-cta-hover text-white px-6 py-2 font-label text-xs uppercase tracking-[0.15em] transition-colors duration-200 cursor-pointer"><?= $editRow ? 'Save Changes' : 'Add' ?></button>
      <?php if ($editRow): ?>
        <a href="?" class="px-6 py-2 border border-outline-variant text-on-surface-variant font-label text-xs uppercase tracking-[0.15em] hover:bg-surface transition-colors duration-200 cursor-pointer">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
