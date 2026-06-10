<?php
// Section: image_text
// $content keys: heading, body, image, image_alt, image_position (left|right)
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$position = ($content['image_position'] ?? 'right') === 'left' ? 'img-left' : 'img-right';
?>
<section class="section section-image-text <?= $position ?>">
    <div class="container image-text-grid">
        <div class="image-text-img">
            <img src="<?= $h($content['image'] ?? '') ?>"
                 alt="<?= $h($content['image_alt'] ?? '') ?>"
                 loading="lazy">
        </div>
        <div class="image-text-content">
            <?php if (!empty($content['heading'])): ?>
                <h2><?= $h($content['heading']) ?></h2>
            <?php endif; ?>
            <?php if (!empty($content['body'])): ?>
                <div class="text-body">
                    <?= $content['body'] ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
