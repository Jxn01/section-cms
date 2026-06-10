<?php
// Section: accordion (FAQ / expandable items)
// $content keys: heading, items (array of {question, answer})
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$items = $content['items'] ?? [];
?>
<section class="section section-accordion">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($items)): ?>
            <div class="accordion" itemscope itemtype="https://schema.org/FAQPage">
                <?php foreach ($items as $i => $item): ?>
                    <div class="accordion-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <button class="accordion-header" aria-expanded="false" aria-controls="acc-<?= $section['id'] ?>-<?= $i ?>">
                            <span itemprop="name"><?= $h($item['question'] ?? '') ?></span>
                            <span class="accordion-icon" aria-hidden="true">+</span>
                        </button>
                        <div class="accordion-body" id="acc-<?= $section['id'] ?>-<?= $i ?>"
                             itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"
                             aria-hidden="true">
                            <div itemprop="text">
                                <?= $item['answer'] ?? '' ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<script>
(function() {
    var section = document.currentScript.previousElementSibling;
    while (section && !section.classList.contains('section-accordion')) {
        section = section.previousElementSibling;
    }
    if (!section) return;
    section.querySelectorAll('.accordion-header').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', !expanded);
            var body = btn.parentElement.querySelector('.accordion-body');
            body.setAttribute('aria-hidden', expanded ? 'true' : 'false');
            btn.parentElement.classList.toggle('open');
        });
    });
})();
</script>
