<?php
// Section: divider (visual separator)
// $content keys: style (line|dots|space|wave), spacing (compact|normal|wide)
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$style   = $content['style'] ?? 'line';
$spacing = $content['spacing'] ?? 'normal';
?>
<?php if ($style === 'line'): ?>
<hr class="section-divider divider-line divider-<?= $h($spacing) ?>" role="separator">
<?php else: ?>
<div class="section-divider divider-<?= $h($style) ?> divider-<?= $h($spacing) ?>" role="presentation">
    <?php if ($style === 'dots'): ?>
        <span aria-hidden="true">•</span><span aria-hidden="true">•</span><span aria-hidden="true">•</span>
    <?php elseif ($style === 'wave'): ?>
        <svg viewBox="0 0 1200 40" preserveAspectRatio="none" class="divider-wave" aria-hidden="true">
            <path d="M0,20 Q150,0 300,20 T600,20 T900,20 T1200,20 V40 H0 Z" fill="var(--color-border)"/>
        </svg>
    <?php endif; ?>
</div>
<?php endif; ?>
