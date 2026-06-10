<?php
// ─── Base Template ───
// HTML skeleton wrapping all pages. Receives: $seo, $settings, $menus, $page, $bodyHtml
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$siteName    = $settings['site_name'] ?? 'Section CMS';
$currentSlug = $page['slug'] ?? '';
$logoUrl     = $settings['logo_url'] ?? '';
$logoMode    = $settings['logo_display_mode'] ?? 'none'; // 'replace', 'beside', 'none'

// Recursive menu rendering (supports unlimited nesting levels)
function renderMenuItems($items, $currentSlug, $h, $depth = 0) {
    foreach ($items as $item):
        $url   = $h($item['url'] ?? '/');
        $label = $h($item['label']);
        $isActive = ($item['url'] === '/' && $currentSlug === 'home')
                 || ($item['url'] !== '/' && '/' . $currentSlug === $item['url']);
        $hasChildren = !empty($item['children']);
        ?>
        <li class="nav-item<?= $isActive ? ' active' : '' ?><?= $hasChildren ? ' has-dropdown' : '' ?> depth-<?= $depth ?>">
            <a href="<?= $url ?>"<?= $isActive ? ' aria-current="page"' : '' ?>><?= $label ?><?php if ($hasChildren): ?> <span class="dropdown-arrow" aria-hidden="true">▾</span><?php endif; ?></a>
            <?php if ($hasChildren): ?>
                <ul class="dropdown-menu dropdown-depth-<?= $depth + 1 ?>">
                    <?php renderMenuItems($item['children'], $currentSlug, $h, $depth + 1); ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach;
}

// Recursive footer sitemap rendering
function renderFooterSitemap($items, $h, $depth = 0) {
    echo '<ul class="sitemap-level sitemap-level-' . $depth . '">';
    foreach ($items as $item):
        $url   = $h($item['url'] ?? '/');
        $label = $h($item['label']);
        $hasChildren = !empty($item['children']);
        ?>
        <li>
            <a href="<?= $url ?>"><?= $label ?></a>
            <?php if ($hasChildren): renderFooterSitemap($item['children'], $h, $depth + 1); endif; ?>
        </li>
    <?php endforeach;
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="<?= I18n::htmlLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="<?= htmlspecialchars($seo['robots'] ?? 'index, follow', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="theme-color" content="<?= $h($settings['primary_color'] ?? '#0067FF') ?>">
    <link rel="alternate" hreflang="<?= I18n::htmlLang() ?>" href="<?= SITE_BASE_URL ?>/<?= $page['slug'] === 'home' ? '' : htmlspecialchars($page['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <?= SEO::renderMetaTags($seo) ?>
    <?= SEO::renderJsonLd($page, $settings) ?>

    <link rel="icon" href="/assets/images/favicon.ico" sizes="32x32">
    <link rel="icon" href="/assets/images/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=2">
</head>
<body>

<a href="#content" class="skip-link"><?= $h(t('site.skip_to_content')) ?></a>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="site-logo" aria-label="<?= $h(t('site.home_aria', ['site' => $siteName])) ?>">
            <?php if ($logoUrl && $logoMode === 'replace'): ?>
                <img src="<?= $h($logoUrl) ?>" alt="<?= $h($siteName) ?>" class="logo-img">
            <?php elseif ($logoUrl && $logoMode === 'beside'): ?>
                <img src="<?= $h($logoUrl) ?>" alt="" class="logo-img logo-beside">
                <span class="logo-text"><?= $h($siteName) ?></span>
            <?php else: ?>
                <span class="logo-text"><?= $h($siteName) ?></span>
            <?php endif; ?>
        </a>

        <button class="menu-toggle" aria-label="<?= $h(t('site.open_menu')) ?>" aria-expanded="false">
            <span class="hamburger"></span>
        </button>

        <nav class="main-nav" aria-label="<?= $h(t('site.main_nav')) ?>">
            <ul class="nav-list">
                <?php renderMenuItems($menus, $currentSlug, $h); ?>
            </ul>
        </nav>
    </div>
</header>

<main id="content">
    <?= $bodyHtml ?>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-info">
            <p class="footer-name"><?= $h($siteName) ?></p>
            <?php if (!empty($settings['contact_address'])): ?>
                <p><?= $h($settings['contact_address']) ?></p>
            <?php endif; ?>
            <?php if (!empty($settings['contact_phone'])): ?>
                <p><?= $h(t('site.phone_label')) ?>: <a href="tel:<?= preg_replace('/[^+0-9]/', '', $settings['contact_phone'] ?? '') ?>"><?= $h($settings['contact_phone']) ?></a></p>
            <?php endif; ?>
            <?php if (!empty($settings['contact_email'])): ?>
                <p><?= $h(t('site.email_label')) ?>: <a href="mailto:<?= $h($settings['contact_email']) ?>"><?= $h($settings['contact_email']) ?></a></p>
            <?php endif; ?>
        </div>
        <nav class="footer-nav" aria-label="<?= $h(t('site.footer_nav')) ?>">
            <details class="footer-sitemap-details">
                <summary class="footer-sitemap-toggle">🗺️ <?= $h(t('site.sitemap')) ?></summary>
                <div class="footer-sitemap-content">
                    <?php renderFooterSitemap($menus, $h); ?>
                </div>
            </details>
        </nav>
        <div class="footer-copy">
            <p>&copy; <?= date('Y') ?> <?= $h($siteName) ?>. <?= $h(t('site.rights_reserved')) ?></p>
        </div>
    </div>
</footer>

<script>
// Mobile hamburger menu
document.querySelector('.menu-toggle')?.addEventListener('click', function () {
    const nav = document.querySelector('.main-nav');
    const expanded = this.getAttribute('aria-expanded') === 'true';
    this.setAttribute('aria-expanded', !expanded);
    nav.classList.toggle('open');
});

// Mobile dropdown toggles (multi-level support)
document.querySelectorAll('.has-dropdown > a').forEach(link => {
    link.addEventListener('click', function (e) {
        if (window.innerWidth <= 768) {
            const parent = this.parentElement;
            const isOpen = parent.classList.contains('dropdown-open');
            // Close sibling dropdowns only (preserve parent chain)
            parent.parentElement.querySelectorAll(':scope > .has-dropdown.dropdown-open').forEach(el => {
                if (el !== parent) {
                    el.classList.remove('dropdown-open');
                    el.querySelectorAll('.dropdown-open').forEach(sub => sub.classList.remove('dropdown-open'));
                }
            });
            if (!isOpen) {
                e.preventDefault();
                parent.classList.add('dropdown-open');
            }
        }
    });
});

// ─── Image download protection ───
// Disable right-click, drag, and long-press save on all public images
document.addEventListener('contextmenu', function (e) {
    if (e.target.tagName === 'IMG') e.preventDefault();
});
document.addEventListener('dragstart', function (e) {
    if (e.target.tagName === 'IMG') e.preventDefault();
});
</script>
</body>
</html>
