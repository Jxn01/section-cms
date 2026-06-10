<?php
// Section: map (embedded Google Maps)
// $content keys: heading, embed_url, height
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$height = (int) ($content['height'] ?? 400);
?>
<section class="section section-map">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
    </div>
    <?php if (!empty($content['embed_url'])): ?>
        <div class="map-wrapper">
            <iframe src="<?= $h($content['embed_url']) ?>"
                    width="100%"
                    height="<?= $height ?>"
                    style="border:0; min-height:300px; max-height:<?= max($height, 300) ?>px;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="<?= $h(!empty($content['heading']) ? $content['heading'] : t('site.map')) ?>"></iframe>
        </div>
    <?php endif; ?>
</section>
