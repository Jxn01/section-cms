<?php
// ─── Admin Panel — Entry / Dashboard / Login ───

require_once __DIR__ . '/auth.php';

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    csrfVerify();
    logout();
}

// Handle login POST
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (attemptLogin($pdo, $username, $password)) {
        header('Location: /admin/');
        exit;
    }
    $loginError = t('admin.login.error');
}

// Site name (used as the admin brand). Falls back to "Section CMS".
$siteName = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name'")->fetchColumn() ?: 'Section CMS';

// If not logged in, show login page
if (!isLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html lang="<?= I18n::htmlLang() ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= te('admin.login.title') ?></title>
        <meta name="robots" content="noindex, nofollow">
        <link rel="stylesheet" href="/admin/assets/admin.css">
    </head>
    <body class="login-page">
        <div class="login-box">
            <h1><?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="login-subtitle"><?= te('admin.login.subtitle') ?></p>
            <?php if ($loginError): ?>
                <div class="alert alert-error"><?= htmlspecialchars($loginError) ?></div>
            <?php endif; ?>
            <form method="POST" action="/admin/">
                <div class="form-group">
                    <label for="username"><?= te('admin.login.username') ?></label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password"><?= te('admin.login.password') ?></label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login" value="1" class="btn btn-primary btn-full"><?= te('admin.login.submit') ?></button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ─── Dashboard ───
$pageCount    = $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$draftCount   = $pdo->query("SELECT COUNT(*) FROM pages WHERE status = 'draft'")->fetchColumn();
$articleCount = 0;
try { $articleCount = $pdo->query("SELECT COUNT(*) FROM pages WHERE page_type = 'article'")->fetchColumn(); } catch (Exception $e) {}
$sectionCount = $pdo->query("SELECT COUNT(*) FROM sections")->fetchColumn();
$mediaCount   = $pdo->query("SELECT COUNT(*) FROM media")->fetchColumn();
$msgCount     = 0;
try { $msgCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn(); } catch (Exception $e) {}

require __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <div class="welcome-banner" id="welcomeBanner">
        <button class="welcome-dismiss" onclick="this.parentElement.style.display='none'; localStorage.setItem('hideWelcome','1');" aria-label="<?= te('common.close') ?>">✕</button>
        <h2><?= te('admin.dash.welcome_title') ?></h2>
        <p><?= te('admin.dash.welcome_text') ?></p>
    </div>
    <script>if(localStorage.getItem('hideWelcome')==='1'){document.getElementById('welcomeBanner').style.display='none';}</script>

    <h1><?= te('admin.dash.title') ?></h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $pageCount ?></div>
            <div class="stat-label"><?= te('admin.dash.stat_pages') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= te('admin.help_aria') ?>">?</button><span class="tooltip-bubble"><?= te('admin.dash.stat_pages_tip') ?></span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $articleCount ?></div>
            <div class="stat-label"><?= te('admin.dash.stat_articles') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= te('admin.help_aria') ?>">?</button><span class="tooltip-bubble"><?= te('admin.dash.stat_articles_tip') ?></span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $draftCount ?></div>
            <div class="stat-label"><?= te('admin.dash.stat_drafts') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= te('admin.help_aria') ?>">?</button><span class="tooltip-bubble"><?= te('admin.dash.stat_drafts_tip') ?></span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $sectionCount ?></div>
            <div class="stat-label"><?= te('admin.dash.stat_sections') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= te('admin.help_aria') ?>">?</button><span class="tooltip-bubble"><?= te('admin.dash.stat_sections_tip') ?></span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $mediaCount ?></div>
            <div class="stat-label"><?= te('admin.dash.stat_media') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= te('admin.help_aria') ?>">?</button><span class="tooltip-bubble"><?= te('admin.dash.stat_media_tip') ?></span></span>
            </div>
        </div>
        <?php if ($msgCount > 0): ?>
        <div class="stat-card">
            <div class="stat-number" style="color:var(--admin-danger)"><?= $msgCount ?></div>
            <div class="stat-label"><?= te('admin.dash.stat_messages') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= te('admin.help_aria') ?>">?</button><span class="tooltip-bubble"><?= te('admin.dash.stat_messages_tip') ?></span></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="tip-cards">
        <div class="tip-card">
            <span class="tip-icon">📄</span>
            <strong><?= te('admin.dash.tip_pages_title') ?></strong>
            <p><?= te('admin.dash.tip_pages_text') ?></p>
        </div>
        <div class="tip-card">
            <span class="tip-icon">🔍</span>
            <strong><?= te('admin.dash.tip_seo_title') ?></strong>
            <p><?= te('admin.dash.tip_seo_text') ?></p>
        </div>
        <div class="tip-card">
            <span class="tip-icon">🖼️</span>
            <strong><?= te('admin.dash.tip_images_title') ?></strong>
            <p><?= te('admin.dash.tip_images_text') ?></p>
        </div>
        <div class="tip-card">
            <span class="tip-icon">🧭</span>
            <strong><?= te('admin.dash.tip_nav_title') ?></strong>
            <p><?= te('admin.dash.tip_nav_text') ?></p>
        </div>
    </div>

    <div class="quick-links">
        <h2><?= te('admin.dash.quick_title') ?></h2>
        <ul>
            <li><a href="/admin/pages.php">📄 <?= te('admin.dash.ql_pages') ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_pages_desc') ?></span></li>
            <li><a href="/admin/page-edit.php?new=1">➕ <?= te('admin.dash.ql_new') ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_new_desc') ?></span></li>
            <li><a href="/admin/menus.php">🧭 <?= te('admin.dash.ql_menus') ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_menus_desc') ?></span></li>
            <li><a href="/admin/media.php">🖼️ <?= te('admin.dash.ql_media') ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_media_desc') ?></span></li>
            <li><a href="/admin/messages.php">✉️ <?= te('admin.dash.ql_messages') ?> <?php if ($msgCount > 0): ?><span class="badge badge-published"><?= $msgCount ?> <?= te('admin.dash.new_badge') ?></span><?php endif; ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_messages_desc') ?></span></li>
            <li><a href="/admin/settings.php">⚙️ <?= te('admin.dash.ql_settings') ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_settings_desc') ?></span></li>
            <li><a href="/admin/password.php">🔑 <?= te('admin.dash.ql_password') ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_password_desc') ?></span></li>
            <li><a href="/" target="_blank">🌐 <?= te('admin.dash.ql_view') ?></a> <span style="color:#94A3B8;font-size:0.8rem;"><?= te('admin.dash.ql_view_desc') ?></span></li>
        </ul>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
