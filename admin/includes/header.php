<?php
// Admin header partial
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

// Count unread messages for badge
$unreadMsgCount = 0;
try {
    $unreadMsgCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
} catch (Exception $e) {}

// Site name = admin brand (falls back to "Section CMS").
$adminBrand = 'Section CMS';
try {
    $adminBrand = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name'")->fetchColumn() ?: 'Section CMS';
} catch (Exception $e) {}

// Preserve the current page when switching language.
$langSwitchBase = strtok($_SERVER['REQUEST_URI'], '?');
$currentLang    = I18n::locale();
?>
<!DOCTYPE html>
<html lang="<?= I18n::htmlLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $h($adminBrand) ?> – <?= te('admin.admin') ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="/admin/assets/admin.css?v=5">
</head>
<body>

<!-- Mobile top bar -->
<div class="admin-topbar">
    <a href="/admin/" class="topbar-brand"><?= $h($adminBrand) ?></a>
    <button class="topbar-toggle" id="sidebarToggle" aria-label="<?= te('admin.menu_aria') ?>">☰</button>
</div>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="/admin/"><?= $h($adminBrand) ?></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="/admin/">🏠 <?= te('admin.nav.dashboard') ?></a></li>
                <li><a href="/admin/pages.php">📄 <?= te('admin.nav.pages') ?></a></li>
                <li><a href="/admin/menus.php">🧭 <?= te('admin.nav.menus') ?></a></li>
                <li><a href="/admin/media.php">🖼️ <?= te('admin.nav.media') ?></a></li>
                <li><a href="/admin/messages.php">✉️ <?= te('admin.nav.messages') ?><?php if ($unreadMsgCount > 0): ?> <span class="sidebar-badge"><?= $unreadMsgCount ?></span><?php endif; ?></a></li>
                <li><a href="/admin/settings.php">⚙️ <?= te('admin.nav.settings') ?></a></li>
                <li><a href="/admin/password.php">🔑 <?= te('admin.nav.password') ?></a></li>
                <li><a href="/admin/components.php">🧩 <?= te('admin.nav.components') ?></a></li>
                <li><a href="/" target="_blank">🌐 <?= te('admin.nav.view_site') ?> ↗</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-lang" aria-label="<?= te('admin.language') ?>">
                <?php foreach (I18n::SUPPORTED as $lang): ?>
                    <a href="<?= $h($langSwitchBase) ?>?lang=<?= $lang ?>" class="sidebar-lang-link<?= $lang === $currentLang ? ' active' : '' ?>"><?= strtoupper($lang) ?></a>
                <?php endforeach; ?>
            </div>
            <span><?= $h($_SESSION['admin_display'] ?? 'Admin') ?></span>
            <form method="POST" action="/admin/" style="display:inline;">
                <?= csrfField() ?>
                <button type="submit" name="logout" value="1" class="sidebar-logout-btn"><?= te('admin.logout') ?></button>
            </form>
        </div>
    </aside>
    <main class="admin-main">
