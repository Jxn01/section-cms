<?php
// ─── Tudásmorzsák Section ───
// Floating knowledge boxes with header, description, and link.
// Items float in from the right in randomized order on each page load.
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$heading = $content['heading'] ?? 'Tudásmorzsák';
$items   = $content['items'] ?? [];
if (empty($items)) return;

// Shuffle items for random order on each page load
shuffle($items);
$sectionId = $section['id'];
?>
<section class="section section-tudasmorzsak" id="section-<?= $sectionId ?>">
    <div class="container">
        <?php if ($heading): ?><h2 class="section-heading"><?= $h($heading) ?></h2><?php endif; ?>
        <div class="tudasmorzsak-grid">
            <?php foreach ($items as $i => $item):
                $title = $h($item['title'] ?? '');
                $desc  = $h($item['description'] ?? '');
                $url   = $item['url'] ?? '';
                $tag   = $url ? 'a' : 'div';
                $href  = $url ? ' href="' . $h($url) . '"' : '';
            ?>
            <<?= $tag ?><?= $href ?> class="tudasmorzsa-card" style="animation-delay: <?= $i * 0.12 ?>s">
                <?php if ($title): ?><h3 class="tudasmorzsa-title"><?= $title ?></h3><?php endif; ?>
                <?php if ($desc): ?><p class="tudasmorzsa-desc"><?= $desc ?></p><?php endif; ?>
                <?php if ($url): ?><span class="tudasmorzsa-link">Tovább →</span><?php endif; ?>
            </<?= $tag ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>
