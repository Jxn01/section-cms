<?php
// ─── Link Banner Section ───
// Full-width colored banner linking to another page.
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$text    = $content['text'] ?? '';
$url     = $content['url'] ?? '#';
$icon    = $content['icon'] ?? '📖';
$bgColor = $content['background_color'] ?? '#0067FF';
?>
<section class="section section-link-banner" id="section-<?= $section['id'] ?>"
         style="background-color: <?= $h($bgColor) ?>">
    <div class="container">
        <a href="<?= $h($url) ?>" class="link-banner-inner">
            <span class="link-banner-icon"><?= $icon ?></span>
            <span class="link-banner-text"><?= $h($text) ?></span>
            <span class="link-banner-arrow">→</span>
        </a>
    </div>
</section>
