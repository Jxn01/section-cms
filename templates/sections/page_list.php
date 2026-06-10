<?php
// Section: page_list (lists pages/articles as cards)
// $content keys: heading, page_type (page|article|all), count
// Requires $pdo to be available (passed through renderer)
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$pageType = $content['page_type'] ?? 'article';
$count    = (int) ($content['count'] ?? 10);

if (!isset($pdo)) return;

$sql = "SELECT slug, title, meta_description, featured_image, updated_at FROM pages WHERE status = 'published'";
$params = [];
if ($pageType !== 'all') {
    $sql .= " AND page_type = :ptype";
    $params['ptype'] = $pageType;
}
$sql .= " ORDER BY updated_at DESC LIMIT " . $count;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pages = $stmt->fetchAll();
?>
<section class="section section-page-list">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($pages)): ?>
            <div class="page-list-grid">
                <?php foreach ($pages as $p): ?>
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
                                <p><?= $h(mb_strimwidth($p['meta_description'], 0, 160, '…')) ?></p>
                            <?php endif; ?>
                            <time datetime="<?= date('Y-m-d', strtotime($p['updated_at'])) ?>">
                                <?= $h(I18n::formatDate($p['updated_at'])) ?>
                            </time>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align:center;color:var(--color-gray);"><?= $h(t('site.no_content')) ?></p>
        <?php endif; ?>
    </div>
</section>
