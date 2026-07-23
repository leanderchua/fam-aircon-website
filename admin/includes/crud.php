<?php

require_once __DIR__ . '/../../includes/auth.php';

/**
 * Generic reorder/add/edit/delete handler + renderer for the simple repeatable
 * content tables (stats, services, brands, projects, about_checklist,
 * contact_info_blocks, nav_links). $config:
 *   table  => string table name (trusted, not user input)
 *   title  => string page heading
 *   fields => ordered [column => ['label'=>, 'type'=>'text'|'textarea'|'number'|'select', 'required'=>bool, 'options'=>[]]]
 */
function fam_render_crud_page(array $config): void
{
    fam_require_login();

    $table = $config['table'];
    $fields = $config['fields'];
    $pdo = fam_db();
    $error = null;

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
                    } else {
                        $id = (int) ($_POST['id'] ?? 0);
                        $setSql = implode(', ', array_map(fn($c) => "{$c} = ?", $cols));
                        $stmt = $pdo->prepare("UPDATE {$table} SET {$setSql} WHERE id = ?");
                        $stmt->execute([...array_values($vals), $id]);
                    }
                }
            } elseif ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
                $stmt->execute([$id]);
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

    require __DIR__ . '/admin_header.php';
    ?>
    <h1 class="text-2xl font-bold text-primary mb-6"><?= htmlspecialchars($config['title'], ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($error): ?>
      <p class="mb-4 text-sm text-red-600"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <table class="w-full border border-outline-variant bg-surface-bright mb-10 text-sm">
      <thead>
        <tr class="bg-surface text-left">
          <?php foreach ($fields as $spec): ?>
            <th class="px-3 py-2 border-b border-outline-variant"><?= htmlspecialchars($spec['label'], ENT_QUOTES, 'UTF-8') ?></th>
          <?php endforeach; ?>
          <th class="px-3 py-2 border-b border-outline-variant">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr class="border-b border-outline-variant">
            <?php foreach ($fields as $col => $spec): ?>
              <td class="px-3 py-2 align-top"><?= htmlspecialchars((string) $row[$col], ENT_QUOTES, 'UTF-8') ?></td>
            <?php endforeach; ?>
            <td class="px-3 py-2 whitespace-nowrap">
              <a class="text-secondary hover:underline mr-2" href="?edit=<?= (int) $row['id'] ?>">Edit</a>
              <form method="post" class="inline">
                <?= fam_csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                <input type="hidden" name="action" value="move_up">
                <button type="submit" class="text-on-surface-variant hover:text-secondary mr-2">↑</button>
              </form>
              <form method="post" class="inline">
                <?= fam_csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                <input type="hidden" name="action" value="move_down">
                <button type="submit" class="text-on-surface-variant hover:text-secondary mr-2">↓</button>
              </form>
              <form method="post" class="inline" onsubmit="return confirm('Delete this row?');">
                <?= fam_csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="text-red-600 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="bg-surface-bright border border-outline-variant p-6 max-w-2xl">
      <h2 class="text-lg font-semibold text-primary mb-4"><?= $editRow ? 'Edit Row' : 'Add New' ?></h2>
      <form method="post" class="grid gap-4">
        <?= fam_csrf_field() ?>
        <input type="hidden" name="action" value="<?= $editRow ? 'edit' : 'add' ?>">
        <?php if ($editRow): ?>
          <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
        <?php endif; ?>
        <?php foreach ($fields as $col => $spec): ?>
          <div>
            <label class="block text-sm text-on-surface-variant mb-1"><?= htmlspecialchars($spec['label'], ENT_QUOTES, 'UTF-8') ?></label>
            <?php $val = $editRow[$col] ?? ''; ?>
            <?php if ($spec['type'] === 'textarea'): ?>
              <textarea name="<?= $col ?>" rows="3" class="w-full border border-outline-variant px-3 py-2"><?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php elseif ($spec['type'] === 'select'): ?>
              <select name="<?= $col ?>" class="w-full border border-outline-variant px-3 py-2">
                <?php foreach ($spec['options'] as $opt): ?>
                  <option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>" <?= ((string) $val === $opt) ? 'selected' : '' ?>><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <input type="<?= $spec['type'] === 'number' ? 'number' : 'text' ?>" name="<?= $col ?>" value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-outline-variant px-3 py-2">
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <div class="flex gap-3 mt-2">
          <button type="submit" class="bg-cta hover:bg-cta-hover text-white px-6 py-2 font-label text-xs uppercase tracking-[0.15em]"><?= $editRow ? 'Save Changes' : 'Add' ?></button>
          <?php if ($editRow): ?>
            <a href="?" class="px-6 py-2 border border-outline-variant text-on-surface-variant font-label text-xs uppercase tracking-[0.15em]">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
    <?php
    require __DIR__ . '/admin_footer.php';
}
