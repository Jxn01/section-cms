<?php
// Section: testimonials (customer reviews)
// $content keys: heading, items (array of {name, text, role, image})
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$items = $content['items'] ?? [];
?>
<section class="section section-testimonials">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($items)): ?>
            <div class="testimonials-grid">
                <?php foreach ($items as $item): ?>
                    <blockquote class="testimonial-card">
                        <div class="testimonial-text">
                            <p>&ldquo;<?= $h($item['text'] ?? '') ?>&rdquo;</p>
                        </div>
                        <footer class="testimonial-author">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= $h($item['image']) ?>"
                                     alt="<?= $h($item['name'] ?? '') ?>"
                                     class="testimonial-avatar" loading="lazy">
                            <?php else: ?>
                                <div class="testimonial-avatar-placeholder">
                                    <?= mb_strtoupper(mb_substr($item['name'] ?? '?', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <cite class="testimonial-name"><?= $h($item['name'] ?? '') ?></cite>
                                <?php if (!empty($item['role'])): ?>
                                    <span class="testimonial-role"><?= $h($item['role']) ?></span>
                                <?php endif; ?>
                            </div>
                        </footer>
                    </blockquote>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
