<?php
// Section: cta (call to action)
// $content keys: heading, subtitle, button_text, button_url, background_color
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$bgColor = $content['background_color'] ?? '#0067FF';
?>
<section class="section section-cta" style="background-color: <?= $h($bgColor) ?>">
    <div class="container cta-content">
        <?php if (!empty($content['heading'])): ?>
            <h2><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($content['subtitle'])): ?>
            <p><?= $h($content['subtitle']) ?></p>
        <?php endif; ?>
        <?php if (!empty($content['button_text']) && !empty($content['button_url'])): ?>
            <a href="<?= $h($content['button_url']) ?>" class="btn btn-white"><?= $h($content['button_text']) ?></a>
        <?php endif; ?>
    </div>
</section>
