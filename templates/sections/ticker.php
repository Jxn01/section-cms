<?php
// Section: ticker (scrolling marquee)
// $content keys: items (array of {text, link}), speed, background_color, text_color
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$items   = $content['items'] ?? [];
$speed   = (int) ($content['speed'] ?? 30);
$bgColor = $content['background_color'] ?? '#0067FF';
$txColor = $content['text_color'] ?? '#FFFFFF';
if (empty($items)) return;

// Randomize ticker item order on each page load
shuffle($items);
?>
<section class="section section-ticker" style="background-color:<?= $h($bgColor) ?>; color:<?= $h($txColor) ?>;">
    <div class="ticker-wrapper" role="marquee" aria-label="Hírek">
        <div class="ticker-track" style="animation-duration:<?= $speed ?>s;">
            <?php foreach ($items as $item): ?>
                <span class="ticker-item">
                    <?php if (!empty($item['link'])): ?>
                        <a href="<?= $h($item['link']) ?>" style="color:<?= $h($txColor) ?>;"><?= $h($item['text'] ?? '') ?></a>
                    <?php else: ?>
                        <?= $h($item['text'] ?? '') ?>
                    <?php endif; ?>
                </span>
            <?php endforeach; ?>
            <?php /* Duplicate for seamless loop */ ?>
            <?php foreach ($items as $item): ?>
                <span class="ticker-item" aria-hidden="true">
                    <?php if (!empty($item['link'])): ?>
                        <a href="<?= $h($item['link']) ?>" style="color:<?= $h($txColor) ?>;"><?= $h($item['text'] ?? '') ?></a>
                    <?php else: ?>
                        <?= $h($item['text'] ?? '') ?>
                    <?php endif; ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
    <button class="ticker-pause-btn" aria-label="Futó szöveg szüneteltetése" aria-pressed="false">⏸</button>
</section>
<script>
(function() {
    var section = document.currentScript.previousElementSibling;
    while (section && !section.classList.contains('section-ticker')) {
        section = section.previousElementSibling;
    }
    if (!section) return;
    var btn = section.querySelector('.ticker-pause-btn');
    var track = section.querySelector('.ticker-track');
    if (btn && track) {
        btn.addEventListener('click', function() {
            var isPaused = btn.getAttribute('aria-pressed') === 'true';
            if (isPaused) {
                track.style.animationPlayState = 'running';
                btn.setAttribute('aria-pressed', 'false');
                btn.textContent = '\u23F8';
                btn.setAttribute('aria-label', 'Futó szöveg szüneteltetése');
            } else {
                track.style.animationPlayState = 'paused';
                btn.setAttribute('aria-pressed', 'true');
                btn.textContent = '\u25B6';
                btn.setAttribute('aria-label', 'Futó szöveg indítása');
            }
        });
    }
})();
</script>
