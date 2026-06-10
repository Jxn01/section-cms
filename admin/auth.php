<?php
// ─── Admin Authentication ───
// Session-based auth. Include this at the top of every admin page.

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/i18n.php';
require_once __DIR__ . '/includes/csrf.php';

// ─── Admin UI language ───
// The admin panel can be used in any supported language. "?lang=xx"
// switches it and persists the choice in the session; otherwise it
// defaults to the site's configured language, then to English.
if (isset($_GET['lang'])) {
    $_SESSION['admin_lang'] = I18n::normalize($_GET['lang']);
}
if (empty($_SESSION['admin_lang'])) {
    try {
        $configured = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_language'")->fetchColumn();
        $_SESSION['admin_lang'] = I18n::normalize((string) ($configured ?: 'en'));
    } catch (Throwable $e) {
        $_SESSION['admin_lang'] = 'en';
    }
}
I18n::init($_SESSION['admin_lang']);

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /admin/');
        exit;
    }
}

function attemptLogin(PDO $pdo, string $username, string $password): bool {
    $stmt = $pdo->prepare("SELECT id, username, password_hash, display_name FROM users WHERE username = :u");
    $stmt->execute(['u' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_user_id']   = $user['id'];
        $_SESSION['admin_username']  = $user['username'];
        $_SESSION['admin_display']   = $user['display_name'] ?: $user['username'];
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: /admin/');
    exit;
}
