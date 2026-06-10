<?php
// ─── SEO Hidden Section ───
// Collapsible text block — crawlable by Google but hidden until clicked.
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$buttonText = !empty($content['button_text']) ? $content['button_text'] : t('site.read_more_long');
$body       = $content['body'] ?? '';
?>
<section class="section section-seo-hidden" id="section-<?= $section['id'] ?>">
    <div class="container">
        <details class="seo-hidden-details">
            <summary class="seo-hidden-toggle">
                <span class="seo-hidden-icon">▸</span>
                <?= $h($buttonText) ?>
            </summary>
            <div class="seo-hidden-content">
                <?= $body ?>
            </div>
        </details>
    </div>
</section>
