<?php
// Section: hero
// $content keys: heading, subtitle, image, cta_text, cta_url
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$bgImage = $content['image'] ?? '';
$bgStyle = $bgImage ? 'background-image:url(' . $h($bgImage) . ')' : '';
?>
<section class="section section-hero" <?= $bgStyle ? 'style="' . $bgStyle . '"' : '' ?> aria-label="<?= $h(!empty($content['heading']) ? $content['heading'] : t('site.hero')) ?>">
    <div class="hero-overlay" aria-hidden="true"></div>
    <div class="container hero-content">
        <?php if (!empty($content['heading'])): ?>
            <h1><?= $h($content['heading']) ?></h1>
        <?php endif; ?>
        <?php if (!empty($content['subtitle'])): ?>
            <p class="hero-subtitle"><?= $h($content['subtitle']) ?></p>
        <?php endif; ?>
        <?php if (!empty($content['cta_text']) && !empty($content['cta_url'])): ?>
            <a href="<?= $h($content['cta_url']) ?>" class="btn btn-primary"><?= $h($content['cta_text']) ?></a>
        <?php endif; ?>
    </div>
</section>
