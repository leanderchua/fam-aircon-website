<?php

require_once __DIR__ . '/../../includes/auth.php';

/**
 * Generic reorder/add/edit/delete handler + renderer for the simple repeatable
 * content tables (stats, services, brands, projects, about_checklist,
 * contact_info_blocks, nav_links). $config:
 *   table  => string table name (trusted, not user input)
 *   title  => string page heading
 *   fields => ordered [column => ['label'=>, 'type'=>'text'|'textarea'|'number'|'select'|'image', 'required'=>bool, 'options'=>[]]]
 */
function fam_render_crud_page(array $config): void
{
    fam_require_login();

    $table = $config['table'];
    $fields = $config['fields'];
    $pdo = fam_db();
    $error = null;
    $saved = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!fam_verify_csrf()) {
            $error = 'Session expired, please try again.';
        } else {
            $action = $_POST['action'] ?? '';

            if ($action === 'add' || $action === 'edit') {
                $cols = [];
                $vals = [];
                foreach ($fields as $col => $spec) {
                    $raw = trim($_POST[$col] ?? '');
                    if ($spec['type'] === 'number') {
                        $vals[$col] = ($raw === '') ? null : (int) $raw;
                    } else {
                        $vals[$col] = $raw;
                    }
                    if (!empty($spec['required']) && $vals[$col] === '') {
                        $error = "{$spec['label']} is required.";
                    }
                    $cols[] = $col;
                }

                if (!$error) {
                    if ($action === 'add') {
                        $maxOrder = (int) $pdo->query("SELECT COALESCE(MAX(sort_order), -1) FROM {$table}")->fetchColumn();
                        $cols[] = 'sort_order';
                        $vals['sort_order'] = $maxOrder + 1;
                        $placeholders = implode(',', array_fill(0, count($cols), '?'));
                        $stmt = $pdo->prepare("INSERT INTO {$table} (" . implode(',', $cols) . ") VALUES ({$placeholders})");
                        $stmt->execute(array_values($vals));
                        $saved = true;
                    } else {
                        $id = (int) ($_POST['id'] ?? 0);
                        $setSql = implode(', ', array_map(fn($c) => "{$c} = ?", $cols));
                        $stmt = $pdo->prepare("UPDATE {$table} SET {$setSql} WHERE id = ?");
                        $stmt->execute([...array_values($vals), $id]);
                        $saved = true;
                    }
                }
            } elseif ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
                $stmt->execute([$id]);
                $saved = true;
            } elseif ($action === 'move_up' || $action === 'move_down') {
                $id = (int) ($_POST['id'] ?? 0);
                $cur = $pdo->prepare("SELECT id, sort_order FROM {$table} WHERE id = ?");
                $cur->execute([$id]);
                $row = $cur->fetch();
                if ($row) {
                    $cmp = $action === 'move_up' ? '<' : '>';
                    $order = $action === 'move_up' ? 'DESC' : 'ASC';
                    $neighborStmt = $pdo->prepare("SELECT id, sort_order FROM {$table} WHERE sort_order {$cmp} ? ORDER BY sort_order {$order} LIMIT 1");
                    $neighborStmt->execute([$row['sort_order']]);
                    $neighbor = $neighborStmt->fetch();
                    if ($neighbor) {
                        $swap = $pdo->prepare("UPDATE {$table} SET sort_order = ? WHERE id = ?");
                        $swap->execute([$neighbor['sort_order'], $row['id']]);
                        $swap->execute([$row['sort_order'], $neighbor['id']]);
                    }
                }
            }
        }
    }

    $rows = $pdo->query("SELECT * FROM {$table} ORDER BY sort_order")->fetchAll();
    $editRow = null;
    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([(int) $_GET['edit']]);
        $editRow = $stmt->fetch() ?: null;
    }

    $famPageTitle = $config['title'];
    require __DIR__ . '/admin_header.php';
    ?>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-primary"><?= htmlspecialchars($config['title'], ENT_QUOTES, 'UTF-8') ?></h1>
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

    <div class="overflow-x-auto border border-outline-variant bg-surface-bright mb-10">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-surface text-left">
            <?php foreach ($fields as $spec): ?>
              <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap"><?= htmlspecialchars($spec['label'], ENT_QUOTES, 'UTF-8') ?></th>
            <?php endforeach; ?>
            <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr>
              <td colspan="<?= count($fields) + 1 ?>" class="px-3 py-10 text-center text-on-surface-variant">
                No items yet — add one using the form below.
              </td>
            </tr>
          <?php endif; ?>
          <?php foreach ($rows as $row): ?>
            <tr class="border-b border-outline-variant last:border-b-0 hover:bg-surface/60 transition-colors duration-200">
              <?php foreach ($fields as $col => $spec): ?>
                <td class="px-3 py-2 align-top">
                  <?php if ($spec['type'] === 'image'): ?>
                    <?php if ($row[$col] !== ''): ?>
                      <img src="<?= htmlspecialchars((string) $row[$col], ENT_QUOTES, 'UTF-8') ?>" alt="" class="w-12 h-12 object-cover rounded border border-outline-variant bg-surface" onerror="this.style.display='none'">
                    <?php else: ?>
                      <span class="text-on-surface-variant/60 text-xs">No image</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <?= htmlspecialchars((string) $row[$col], ENT_QUOTES, 'UTF-8') ?>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
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
                <form method="post" class="inline" onsubmit="return confirm('Delete this row? This cannot be undone.');">
                  <?= fam_csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button type="submit" aria-label="Delete row" class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors duration-200 cursor-pointer"><?= fam_icon('trash', 'w-4 h-4') ?></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div id="famForm" class="bg-surface-bright border border-outline-variant p-6 max-w-2xl scroll-mt-24">
      <h2 class="text-lg font-semibold text-primary mb-4"><?= $editRow ? 'Edit Row' : 'Add New' ?></h2>
      <form method="post" class="grid gap-4">
        <?= fam_csrf_field() ?>
        <input type="hidden" name="action" value="<?= $editRow ? 'edit' : 'add' ?>">
        <?php if ($editRow): ?>
          <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
        <?php endif; ?>
        <?php foreach ($fields as $col => $spec): ?>
          <div>
            <label for="field_<?= $col ?>" class="block text-sm text-on-surface-variant mb-1">
              <?= htmlspecialchars($spec['label'], ENT_QUOTES, 'UTF-8') ?>
              <?php if (!empty($spec['required'])): ?><span class="text-red-600" aria-hidden="true"> *</span><?php endif; ?>
            </label>
            <?php $val = $editRow[$col] ?? ''; ?>
            <?php if ($spec['type'] === 'textarea'): ?>
              <textarea id="field_<?= $col ?>" name="<?= $col ?>" rows="3" <?= !empty($spec['required']) ? 'required aria-required="true"' : '' ?> class="w-full border border-outline-variant px-3 py-2 focus-visible:border-secondary"><?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php elseif ($spec['type'] === 'select'): ?>
              <select id="field_<?= $col ?>" name="<?= $col ?>" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
                <?php foreach ($spec['options'] as $opt): ?>
                  <option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>" <?= ((string) $val === $opt) ? 'selected' : '' ?>><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            <?php elseif ($spec['type'] === 'image'): ?>
              <div class="flex items-start gap-3">
                <div class="relative w-20 h-20 shrink-0 border border-outline-variant bg-surface flex items-center justify-center overflow-hidden">
                  <img id="preview_<?= $col ?>" src="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" alt="" class="w-full h-full object-cover <?= $val === '' ? 'hidden' : '' ?>">
                  <span data-preview-placeholder class="text-on-surface-variant/50 <?= $val !== '' ? 'hidden' : '' ?>"><?= fam_icon('image-off', 'w-6 h-6') ?></span>
                </div>
                <input type="text" id="field_<?= $col ?>" name="<?= $col ?>" value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" placeholder="images/example.jpg or https://..." data-preview-target="preview_<?= $col ?>" <?= !empty($spec['required']) ? 'required aria-required="true"' : '' ?> class="flex-1 h-10 border border-outline-variant px-3 focus-visible:border-secondary">
              </div>
              <p class="text-xs text-on-surface-variant/70 mt-1">Paste an image path or URL — a real upload button is coming in a later update.</p>
            <?php else: ?>
              <input type="<?= $spec['type'] === 'number' ? 'number' : 'text' ?>" id="field_<?= $col ?>" name="<?= $col ?>" value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" <?= !empty($spec['required']) ? 'required aria-required="true"' : '' ?> class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <div class="flex gap-3 mt-2">
          <button type="submit" class="bg-cta hover:bg-cta-hover text-white px-6 py-2 font-label text-xs uppercase tracking-[0.15em] transition-colors duration-200 cursor-pointer"><?= $editRow ? 'Save Changes' : 'Add' ?></button>
          <?php if ($editRow): ?>
            <a href="?" class="px-6 py-2 border border-outline-variant text-on-surface-variant font-label text-xs uppercase tracking-[0.15em] hover:bg-surface transition-colors duration-200 cursor-pointer">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
    <?php
    require __DIR__ . '/admin_footer.php';
}
