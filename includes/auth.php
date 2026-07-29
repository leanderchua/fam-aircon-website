<?php

require_once __DIR__ . '/db.php';

const FAM_MAX_FAILED_ATTEMPTS = 5;
const FAM_LOCKOUT_MINUTES = 15;

function fam_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $cfg = fam_config();
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_name($cfg['session_name']);
    session_start();

    $timeout = $cfg['session_idle_timeout'];
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        $_SESSION = [];
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

function fam_is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function fam_require_login(): void
{
    fam_start_session();
    if (!fam_is_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function fam_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function fam_csrf_field(): string
{
    $token = htmlspecialchars(fam_csrf_token(), ENT_QUOTES, 'UTF-8');
    return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\">";
}

function fam_verify_csrf(): bool
{
    $submitted = $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $submitted);
}

function fam_attempt_login(string $username, string $password): array
{
    $pdo = fam_db();
    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        // Run password_verify against a dummy hash so this path takes
        // roughly as long as a real failed attempt (timing side-channel).
        password_verify($password, '$2y$10$Q9y2c1z3mN0k5vQ8dJqM4uS7wY2xE6bR1tH9pL0oC3aF5gV8sD1iK');
        return ['ok' => false, 'error' => 'Invalid username or password.'];
    }

    if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
        return ['ok' => false, 'error' => 'Invalid username or password.'];
    }

    if (!password_verify($password, $user['password_hash'])) {
        $attempts = $user['failed_attempts'] + 1;
        $lockedUntil = null;
        if ($attempts >= FAM_MAX_FAILED_ATTEMPTS) {
            $lockedUntil = date('Y-m-d H:i:s', time() + FAM_LOCKOUT_MINUTES * 60);
            $attempts = 0;
        }
        $upd = $pdo->prepare('UPDATE admin_users SET failed_attempts = ?, locked_until = ? WHERE id = ?');
        $upd->execute([$attempts, $lockedUntil, $user['id']]);
        return ['ok' => false, 'error' => 'Invalid username or password.'];
    }

    $reset = $pdo->prepare('UPDATE admin_users SET failed_attempts = 0, locked_until = NULL WHERE id = ?');
    $reset->execute([$user['id']]);

    session_regenerate_id(true);
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];

    return ['ok' => true];
}
