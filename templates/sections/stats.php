<?php
// Section: stats (number counters / statistics)
// $content keys: heading, background_color, items (array of {number, label})
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$items   = $content['items'] ?? [];
$bgColor = $content['background_color'] ?? '';
$style   = $bgColor ? 'background-color:' . $h($bgColor) . '; color:#fff;' : '';
?>
<section class="section section-stats" <?= $style ? 'style="' . $style . '"' : '' ?>>
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($items)): ?>
            <div class="stats-row">
                <?php foreach ($items as $item): ?>
                    <div class="stat-item">
                        <span class="stat-number"><?= $h($item['number'] ?? '') ?></span>
                        <span class="stat-label"><?= $h($item['label'] ?? '') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
