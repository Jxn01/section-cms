<?php
// Section: cards
// $content keys: heading, cards (array of {title, description, icon, link})
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$cards = $content['cards'] ?? [];
?>
<section class="section section-cards">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($cards)): ?>
            <div class="cards-grid">
                <?php foreach ($cards as $card): ?>
                    <article class="card">
                        <?php if (!empty($card['icon'])): ?>
                            <div class="card-icon"><?= $h($card['icon']) ?></div>
                        <?php endif; ?>
                        <h3><?= $h($card['title'] ?? '') ?></h3>
                        <p><?= $h($card['description'] ?? '') ?></p>
                        <?php if (!empty($card['link'])): ?>
                            <a href="<?= $h($card['link']) ?>" class="card-link">Tovább &rarr;</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
