<?php
// ─── Keyword Results Template ───
// Shows pages matching a keyword. Renders inside base.php via $bodyHtml.
// Receives: $keyword, $matchingPages, $settings, $menus

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$siteName = $settings['site_name'] ?? 'Parkoló ABC';

// Build SEO data for keyword results
$seo = [
    'title'            => $keyword . ' – Kulcsszó – ' . $siteName,
    'meta_description' => 'Oldalak a következő kulcsszóval: ' . $keyword,
    'meta_keywords'    => $keyword,
    'canonical'        => SITE_BASE_URL . '/kulcsszo/' . urlencode($keyword),
    'og_title'         => $keyword . ' – ' . $siteName,
    'og_description'   => 'Oldalak a következő kulcsszóval: ' . $keyword,
    'og_image'         => '',
    'og_url'           => SITE_BASE_URL . '/kulcsszo/' . urlencode($keyword),
    'og_type'          => 'website',
    'og_site_name'     => $siteName,
    'robots'           => 'noindex, follow',
];

// Page data for base.php (slug, title, etc.)
$page = [
    'slug'             => 'kulcsszo/' . $keyword,
    'title'            => $keyword,
    'meta_title'       => '',
    'meta_description' => '',
    'updated_at'       => date('Y-m-d H:i:s'),
];

// Build the body content
ob_start();
?>
<section class="section section-text">
    <div class="container">
        <nav class="breadcrumb" aria-label="Útvonal">
            <a href="/">Kezdőlap</a> <span aria-hidden="true">›</span>
            <span>Kulcsszó: <?= $h($keyword) ?></span>
        </nav>
        <h1>Kulcsszó: <?= $h($keyword) ?></h1>

        <?php if (!empty($matchingPages)): ?>
            <div class="page-list-grid" style="margin-top:2rem;">
                <?php foreach ($matchingPages as $p): ?>
                    <article class="page-list-card">
                        <?php if (!empty($p['featured_image'])): ?>
                            <a href="/<?= $h($p['slug']) ?>" class="page-list-image">
                                <img src="<?= $h($p['featured_image']) ?>"
                                     alt="<?= $h($p['title']) ?>" loading="lazy">
                            </a>
                        <?php endif; ?>
                        <div class="page-list-content">
                            <h3><a href="/<?= $h($p['slug']) ?>"><?= $h($p['title']) ?></a></h3>
                            <?php if (!empty($p['meta_description'])): ?>
                                <p><?= $h(mb_strimwidth($p['meta_description'], 0, 200, '…')) ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="keyword-no-results">Nincs találat erre a kulcsszóra.</p>
        <?php endif; ?>
    </div>
</section>
<?php
$bodyHtml = ob_get_clean();

// Render through the shared base template
require __DIR__ . '/base.php';
