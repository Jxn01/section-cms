<?php
// Section: gallery
// $content keys: heading, images (array of {url, alt})
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$images = $content['images'] ?? [];
?>
<section class="section section-gallery">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($images)): ?>
            <div class="gallery-grid">
                <?php foreach ($images as $img): ?>
                    <figure class="gallery-item">
                        <img src="<?= $h($img['url'] ?? '') ?>"
                             alt="<?= $h($img['alt'] ?? '') ?>"
                             loading="lazy">
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
