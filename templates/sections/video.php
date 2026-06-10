<?php
// Section: video (embedded YouTube/Vimeo)
// $content keys: heading, url, type (youtube|vimeo)
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$url  = $content['url'] ?? '';
$type = $content['type'] ?? 'youtube';

// Convert watch URLs to embed URLs
if ($type === 'youtube') {
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
        $url = 'https://www.youtube.com/embed/' . $m[1];
    }
} elseif ($type === 'vimeo') {
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
        $url = 'https://player.vimeo.com/video/' . $m[1];
    }
}
?>
<section class="section section-video">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($url)): ?>
            <div class="video-wrapper">
                <iframe src="<?= $h($url) ?>"
                        style="border:0;"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                        title="<?= $h($content['heading'] ?? 'Videó') ?>"></iframe>
            </div>
        <?php endif; ?>
    </div>
</section>
