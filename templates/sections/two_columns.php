<?php
// Section: two_columns (two text columns side by side)
// $content keys: heading, left_body, right_body
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
?>
<section class="section section-two-columns">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <div class="two-columns-grid">
            <div class="column text-body">
                <?= $content['left_body'] ?? '' ?>
            </div>
            <div class="column text-body">
                <?= $content['right_body'] ?? '' ?>
            </div>
        </div>
    </div>
</section>
