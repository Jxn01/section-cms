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
        <p class="section-404-text">Az oldal nem található.</p>
        <a href="/" class="btn btn-primary">Vissza a kezdőlapra</a>
    </div>
</section>
<?php
$bodyHtml = ob_get_clean();

// Render through the shared base template
require __DIR__ . '/base.php';
