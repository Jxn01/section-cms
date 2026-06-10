<?php
// ─── Reference Gallery Section ───
// Project-based gallery — hover shows additional images from the same project.
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$heading  = $content['heading'] ?? '';
$projects = $content['projects'] ?? [];
?>
<section class="section section-reference-gallery" id="section-<?= $section['id'] ?>">
    <div class="container">
        <?php if ($heading): ?>
            <h2 class="section-heading"><?= $h($heading) ?></h2>
        <?php endif; ?>
        <div class="ref-gallery-grid">
            <?php foreach ($projects as $project): ?>
                <div class="ref-gallery-project">
                    <div class="ref-gallery-cover">
                        <?php if (!empty($project['cover_image'])): ?>
                            <img src="<?= $h($project['cover_image']) ?>"
                                 alt="<?= $h($project['cover_alt'] ?? $project['title'] ?? '') ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="ref-gallery-placeholder">🖼️</div>
                        <?php endif; ?>
                        <?php if (!empty($project['title'])): ?>
                            <div class="ref-gallery-label"><?= $h($project['title']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($project['images'])): ?>
                        <div class="ref-gallery-expand">
                            <?php foreach ($project['images'] as $img): ?>
                                <img src="<?= $h($img['url'] ?? '') ?>"
                                     alt="<?= $h($img['alt'] ?? '') ?>"
                                     loading="lazy">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
