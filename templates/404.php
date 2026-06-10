<?php
// 404 page — renders inside base.php via $bodyHtml
// Receives: $seo, $settings, $menus, $page (all set by index.php)

// Override robots for 404
$seo['robots'] = 'noindex, nofollow';

// Build the body content
ob_start();
?>
<section class="section section-text section-404">
    <div class="container">
        <h1>404</h1>
        <p class="section-404-text"><?= htmlspecialchars(t('site.404_text'), ENT_QUOTES, 'UTF-8') ?></p>
        <a href="/" class="btn btn-primary"><?= htmlspecialchars(t('site.404_back'), ENT_QUOTES, 'UTF-8') ?></a>
    </div>
</section>
<?php
$bodyHtml = ob_get_clean();

// Render through the shared base template
require __DIR__ . '/base.php';
