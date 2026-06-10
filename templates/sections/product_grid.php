<?php
// ─── Product Grid Section ───
// Grid of product/accessory images with hover-reveal descriptions.
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$heading = $content['heading'] ?? '';
$columns = (int)($content['columns'] ?? 5);
$items   = $content['items'] ?? [];
?>
<section class="section section-product-grid" id="section-<?= $section['id'] ?>">
    <div class="container">
        <?php if ($heading): ?>
            <h2 class="section-heading"><?= $h($heading) ?></h2>
        <?php endif; ?>
        <div class="product-grid" style="--pg-cols: <?= $columns ?>">
            <?php foreach ($items as $item): ?>
                <a href="<?= $h($item['url'] ?? '#') ?>"
                   class="product-grid-item"
                   <?= !empty($item['url']) ? 'target="_blank"' : '' ?>>
                    <div class="product-grid-front">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= $h($item['image']) ?>"
                                 alt="<?= $h($item['image_alt'] ?? $item['title'] ?? '') ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="product-grid-placeholder">📦</div>
                        <?php endif; ?>
                        <span class="product-grid-label"><?= $h($item['title'] ?? '') ?></span>
                    </div>
                    <div class="product-grid-back">
                        <strong><?= $h($item['title'] ?? '') ?></strong>
                        <p><?= $h($item['short_desc'] ?? '') ?></p>
                        <?php if (!empty($item['url'])): ?>
                            <span class="product-grid-link">Részletek →</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
