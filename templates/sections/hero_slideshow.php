<?php
// ─── Hero Slideshow Section ───
// Full-width hero with rotating featured images + overlay navigation boxes.
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$heading  = $content['heading'] ?? '';
$subtitle = $content['subtitle'] ?? '';
$ctaText  = $content['cta_text'] ?? '';
$ctaUrl   = $content['cta_url'] ?? '';
$interval = (int)($content['interval'] ?? 4) * 1000;
$boxes    = $content['overlay_boxes'] ?? [];

// Fetch featured images from media library
$featuredImages = [];
if ($pdo) {
    try {
        $featuredImages = $pdo->query(
            "SELECT filename, alt_text FROM media WHERE is_featured = 1 ORDER BY uploaded_at DESC"
        )->fetchAll();
    } catch (Exception $e) {}
}

// Fetch logo from settings
$logoUrl = '';
if ($pdo) {
    try {
        $row = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'logo_url'")->fetch();
        if ($row && $row['setting_value']) $logoUrl = $row['setting_value'];
    } catch (Exception $e) {}
}
?>
<section class="section section-hero-slideshow" id="section-<?= $section['id'] ?>">
    <div class="hero-ss-slides">
        <?php if (!empty($featuredImages)): ?>
            <?php foreach ($featuredImages as $i => $img): ?>
                <div class="hero-ss-slide<?= $i === 0 ? ' active' : '' ?>"
                     style="background-image: url('/assets/uploads/<?= $h($img['filename']) ?>')"
                     role="img" aria-label="<?= $h($img['alt_text'] ?: t('site.image')) ?>"></div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="hero-ss-slide active" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 100%)"></div>
        <?php endif; ?>
    </div>

    <div class="hero-ss-overlay">
        <?php if ($logoUrl): ?>
            <div class="hero-ss-logo">
                <img src="<?= $h($logoUrl) ?>" alt="Logo">
            </div>
        <?php endif; ?>

        <div class="hero-ss-center">
            <?php if ($heading): ?><h1><?= $h($heading) ?></h1><?php endif; ?>
            <?php if ($subtitle): ?><p class="hero-ss-subtitle"><?= $h($subtitle) ?></p><?php endif; ?>
            <?php if ($ctaText): ?>
                <a href="<?= $h($ctaUrl ?: '#') ?>" class="btn btn-hero"><?= $h($ctaText) ?></a>
            <?php endif; ?>
        </div>

        <?php if (!empty($boxes)): ?>
            <div class="hero-ss-boxes">
                <?php foreach ($boxes as $box): ?>
                    <a href="<?= $h($box['url'] ?? '#') ?>"
                       class="hero-ss-box hero-ss-box-<?= $h($box['size'] ?? 'small') ?>">
                        <span><?= $h($box['title'] ?? '') ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (count($featuredImages) > 1): ?>
    <script>
    (function(){
        var slides = document.querySelectorAll('#section-<?= $section['id'] ?> .hero-ss-slide');
        if (slides.length < 2) return;
        var current = 0;
        setInterval(function(){
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, <?= $interval ?>);
    })();
    </script>
    <?php endif; ?>
</section>
