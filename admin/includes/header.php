<?php
// Admin header partial
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

// Count unread messages for badge
$unreadMsgCount = 0;
try {
    $unreadMsgCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Parkoló ABC</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="/admin/assets/admin.css?v=5">
</head>
<body>

<!-- Mobile top bar -->
<div class="admin-topbar">
    <a href="/admin/" class="topbar-brand">Parkoló ABC</a>
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Menü">☰</button>
</div>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="/admin/">Parkoló ABC</a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="/admin/">🏠 Vezérlőpult</a></li>
                <li><a href="/admin/pages.php">📄 Oldalak</a></li>
                <li><a href="/admin/menus.php">🧭 Menü</a></li>
                <li><a href="/admin/media.php">🖼️ Média</a></li>
                <li><a href="/admin/messages.php">✉️ Üzenetek<?php if ($unreadMsgCount > 0): ?> <span class="sidebar-badge"><?= $unreadMsgCount ?></span><?php endif; ?></a></li>
                <li><a href="/admin/settings.php">⚙️ Beállítások</a></li>
                <li><a href="/admin/password.php">🔑 Jelszó</a></li>
                <li><a href="/admin/components.php">🧩 Komponensek</a></li>
                <li><a href="/" target="_blank">🌐 Weboldal ↗</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <span><?= $h($_SESSION['admin_display'] ?? 'Admin') ?></span>
            <form method="POST" action="/admin/" style="display:inline;">
                <?= csrfField() ?>
                <button type="submit" name="logout" value="1" class="sidebar-logout-btn">Kijelentkezés</button>
            </form>
        </div>
    </aside>
    <main class="admin-main">
