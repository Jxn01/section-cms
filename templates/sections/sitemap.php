<?php
// ─── Sitemap Section ───
// Renders a hierarchical sitemap of all published pages.
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$heading = !empty($content['heading']) ? $content['heading'] : t('site.sitemap');

// Fetch all published pages
$sitemapPages = [];
if ($pdo) {
    try {
        $sitemapPages = $pdo->query(
            "SELECT title, slug, meta_description FROM pages WHERE status = 'published' ORDER BY sort_order ASC, title ASC"
        )->fetchAll();
    } catch (Exception $e) {}
}

// Also fetch menu tree for hierarchical display
$sitemapMenus = [];
if ($pdo) {
    try {
        $allMenuItems = $pdo->query(
            "SELECT m.*, p.slug as page_slug, p.title as page_title
             FROM menus m LEFT JOIN pages p ON m.page_id = p.id
             ORDER BY m.sort_order ASC"
        )->fetchAll();
        // Build tree
        $byId = [];
        foreach ($allMenuItems as &$mi) {
            $mi['children'] = [];
            if ($mi['page_slug']) {
                $mi['url'] = $mi['page_slug'] === 'home' ? '/' : '/' . $mi['page_slug'];
            }
            $byId[$mi['id']] = &$mi;
        }
        unset($mi);
        foreach ($allMenuItems as &$mi) {
            if ($mi['parent_id'] && isset($byId[$mi['parent_id']])) {
                $byId[$mi['parent_id']]['children'][] = &$mi;
            } else {
                $sitemapMenus[] = &$mi;
            }
        }
        unset($mi);
    } catch (Exception $e) {}
}

function renderSitemapTree($items, $h) {
    if (empty($items)) return;
    echo '<ul class="sitemap-tree-level">';
    foreach ($items as $item) {
        $url   = $h($item['url'] ?? '#');
        $label = $h($item['label'] ?? $item['page_title'] ?? '');
        echo '<li>';
        echo '<a href="' . $url . '">' . $label . '</a>';
        if (!empty($item['children'])) {
            renderSitemapTree($item['children'], $h);
        }
        echo '</li>';
    }
    echo '</ul>';
}
?>
<section class="section section-sitemap" id="section-<?= $section['id'] ?>">
    <div class="container">
        <?php if ($heading): ?><h2 class="section-heading"><?= $h($heading) ?></h2><?php endif; ?>

        <?php if (!empty($sitemapMenus)): ?>
            <div class="sitemap-tree">
                <?php renderSitemapTree($sitemapMenus, $h); ?>
            </div>
        <?php endif; ?>

        <?php
        // Show any orphan pages not in the menu
        $menuUrls = [];
        $collectUrls = function($items) use (&$collectUrls, &$menuUrls) {
            foreach ($items as $item) {
                $menuUrls[] = $item['url'] ?? '';
                if (!empty($item['children'])) $collectUrls($item['children']);
            }
        };
        $collectUrls($sitemapMenus);
        $orphans = array_filter($sitemapPages, function($p) use ($menuUrls) {
            $pageUrl = $p['slug'] === 'home' ? '/' : '/' . $p['slug'];
            return !in_array($pageUrl, $menuUrls, true);
        });
        ?>
        <?php if (!empty($orphans)): ?>
            <div class="sitemap-orphans">
                <h3><?= $h(t('site.more_pages')) ?></h3>
                <ul class="sitemap-tree-level">
                    <?php foreach ($orphans as $op): ?>
                        <li>
                            <a href="/<?= $op['slug'] === 'home' ? '' : $h($op['slug']) ?>"><?= $h($op['title']) ?></a>
                            <?php if (!empty($op['meta_description'])): ?>
                                <span class="sitemap-desc">— <?= $h(mb_strimwidth($op['meta_description'], 0, 80, '...')) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
