<?php
// Section: text
// $content keys: heading, body
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
?>
<section class="section section-text">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($content['body'])): ?>
            <div class="text-body">
                <?= $content['body'] /* HTML content — already stored sanitised */ ?>
            </div>
        <?php endif; ?>
    </div>
</section>
