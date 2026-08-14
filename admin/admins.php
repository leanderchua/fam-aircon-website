<?php
require_once __DIR__ . '/../includes/auth.php';

fam_require_login();

const FAM_MIN_PASSWORD_LENGTH = 8;

$pdo = fam_db();
$error = null;
$saved = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!fam_verify_csrf()) {
        $error = 'Session expired, please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if ($username === '') {
                $error = 'Username is required.';
            } elseif (strlen($password) < FAM_MIN_PASSWORD_LENGTH) {
                $error = 'Password must be at least ' . FAM_MIN_PASSWORD_LENGTH . ' characters.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } else {
                $exists = $pdo->prepare('SELECT id FROM admin_users WHERE username = ?');
                $exists->execute([$username]);
                if ($exists->fetch()) {
                    $error = 'That username is already taken.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
                    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
                    $saved = "Admin \"{$username}\" added.";
                }
            }
        } elseif ($action === 'change_password') {
            $id = (int) ($_POST['id'] ?? 0);
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (strlen($password) < FAM_MIN_PASSWORD_LENGTH) {
                $error = 'Password must be at least ' . FAM_MIN_PASSWORD_LENGTH . ' characters.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } else {
                $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ?, failed_attempts = 0, locked_until = NULL WHERE id = ?');
                $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
                $saved = 'Password updated.';
            }
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $total = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
            if ($total <= 1) {
                $error = 'Cannot delete the only remaining admin account.';
            } elseif ($id === (int) $_SESSION['admin_id']) {
                $error = 'Cannot delete the account you are currently logged in as.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
                $stmt->execute([$id]);
                $saved = 'Admin removed.';
            }
        }
    }
}

$admins = $pdo->query('SELECT id, username, failed_attempts, locked_until, created_at FROM admin_users ORDER BY created_at')->fetchAll();
$famPageTitle = 'Admins';

require __DIR__ . '/includes/admin_header.php';
?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-primary">Admins</h1>
  <a href="#famAddAdmin" class="inline-flex items-center gap-2 bg-cta hover:bg-cta-hover text-white px-4 py-2 font-label text-xs uppercase tracking-[0.15em] transition-colors duration-200 cursor-pointer">
    <?= fam_icon('plus', 'w-4 h-4') ?> Add Admin
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
    <span><?= htmlspecialchars($saved, ENT_QUOTES, 'UTF-8') ?></span>
  </div>
<?php endif; ?>

<div class="overflow-x-auto border border-outline-variant bg-surface-bright mb-10">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-surface text-left">
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Username</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Status</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Created</th>
        <th class="px-3 py-2.5 border-b border-outline-variant font-semibold text-on-surface-variant whitespace-nowrap">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($admins as $admin): $isLocked = $admin['locked_until'] && strtotime($admin['locked_until']) > time(); ?>
        <tr class="border-b border-outline-variant last:border-b-0 hover:bg-surface/60 transition-colors duration-200">
          <td class="px-3 py-2 align-top font-medium">
            <?= htmlspecialchars($admin['username'], ENT_QUOTES, 'UTF-8') ?>
            <?php if ((int) $admin['id'] === (int) $_SESSION['admin_id']): ?><span class="text-xs text-on-surface-variant/60">(you)</span><?php endif; ?>
          </td>
          <td class="px-3 py-2 align-top"><?= $isLocked ? '<span class="text-red-600">Locked</span>' : '<span class="text-on-surface-variant/70">Active</span>' ?></td>
          <td class="px-3 py-2 align-top text-on-surface-variant"><?= htmlspecialchars($admin['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="px-3 py-2 whitespace-nowrap">
            <a class="inline-flex items-center gap-1 text-secondary hover:underline mr-3 cursor-pointer" href="#famPwd<?= (int) $admin['id'] ?>">
              <?= fam_icon('edit', 'w-4 h-4') ?><span>Change Password</span>
            </a>
            <form method="post" class="inline" onsubmit="return confirm('Remove this admin? This cannot be undone.');">
              <?= fam_csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $admin['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" aria-label="Delete admin" class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors duration-200 cursor-pointer"><?= fam_icon('trash', 'w-4 h-4') ?></button>
            </form>
          </td>
        </tr>
        <tr id="famPwd<?= (int) $admin['id'] ?>" class="border-b border-outline-variant last:border-b-0 bg-surface/40 scroll-mt-24">
          <td colspan="4" class="px-3 py-4">
            <form method="post" class="flex flex-wrap items-end gap-3">
              <?= fam_csrf_field() ?>
              <input type="hidden" name="action" value="change_password">
              <input type="hidden" name="id" value="<?= (int) $admin['id'] ?>">
              <div>
                <label for="pwd_<?= (int) $admin['id'] ?>" class="block text-xs text-on-surface-variant mb-1">New password for <?= htmlspecialchars($admin['username'], ENT_QUOTES, 'UTF-8') ?></label>
                <input type="password" id="pwd_<?= (int) $admin['id'] ?>" name="password" minlength="<?= FAM_MIN_PASSWORD_LENGTH ?>" required aria-required="true" class="h-10 border border-outline-variant px-3 focus-visible:border-secondary">
              </div>
              <div>
                <label for="pwd_confirm_<?= (int) $admin['id'] ?>" class="block text-xs text-on-surface-variant mb-1">Confirm</label>
                <input type="password" id="pwd_confirm_<?= (int) $admin['id'] ?>" name="confirm_password" minlength="<?= FAM_MIN_PASSWORD_LENGTH ?>" required aria-required="true" class="h-10 border border-outline-variant px-3 focus-visible:border-secondary">
              </div>
              <button type="submit" class="bg-cta hover:bg-cta-hover text-white px-4 h-10 font-label text-xs uppercase tracking-[0.15em] transition-colors duration-200 cursor-pointer">Update Password</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div id="famAddAdmin" class="bg-surface-bright border border-outline-variant p-6 max-w-lg scroll-mt-24">
  <h2 class="text-lg font-semibold text-primary mb-4">Add Admin</h2>
  <form method="post" class="grid gap-4">
    <?= fam_csrf_field() ?>
    <input type="hidden" name="action" value="add">
    <div>
      <label for="new_username" class="block text-sm text-on-surface-variant mb-1">Username <span class="text-red-600" aria-hidden="true">*</span></label>
      <input type="text" id="new_username" name="username" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
    </div>
    <div>
      <label for="new_password" class="block text-sm text-on-surface-variant mb-1">Password <span class="text-red-600" aria-hidden="true">*</span></label>
      <input type="password" id="new_password" name="password" minlength="<?= FAM_MIN_PASSWORD_LENGTH ?>" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
      <p class="text-xs text-on-surface-variant/70 mt-1">At least <?= FAM_MIN_PASSWORD_LENGTH ?> characters.</p>
    </div>
    <div>
      <label for="new_confirm_password" class="block text-sm text-on-surface-variant mb-1">Confirm Password <span class="text-red-600" aria-hidden="true">*</span></label>
      <input type="password" id="new_confirm_password" name="confirm_password" minlength="<?= FAM_MIN_PASSWORD_LENGTH ?>" required aria-required="true" class="w-full h-10 border border-outline-variant px-3 focus-visible:border-secondary">
    </div>
    <div>
      <button type="submit" class="bg-cta hover:bg-cta-hover text-white px-6 py-2 font-label text-xs uppercase tracking-[0.15em] transition-colors duration-200 cursor-pointer">Add Admin</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
