<?php
// Section: keywords_cloud (auto-generated from page meta_keywords)
// $content keys: heading, count
// Requires $pdo to be available (passed through renderer)
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$maxCount = (int) ($content['count'] ?? 50);

if (!isset($pdo)) return;

// Gather all keywords from published pages
$stmt = $pdo->query("SELECT meta_keywords FROM pages WHERE status = 'published' AND meta_keywords != ''");
$allKeywords = [];
foreach ($stmt->fetchAll() as $row) {
    $kws = array_map('trim', explode(',', $row['meta_keywords']));
    foreach ($kws as $kw) {
        $kw = mb_strtolower(trim($kw));
        if ($kw !== '') {
            $allKeywords[$kw] = ($allKeywords[$kw] ?? 0) + 1;
        }
    }
}

arsort($allKeywords);
$allKeywords = array_slice($allKeywords, 0, $maxCount, true);

if (empty($allKeywords)) return;

// Calculate font sizes (min 0.8rem, max 2.5rem)
$maxFreq = max($allKeywords);
$minFreq = min($allKeywords);
$range   = max($maxFreq - $minFreq, 1);
?>
<section class="section section-keywords-cloud">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>
        <div class="keywords-cloud" role="navigation" aria-label="<?= $h(t('site.keywords')) ?>">
            <?php foreach ($allKeywords as $keyword => $freq):
                $size = 0.8 + (($freq - $minFreq) / $range) * 1.7;
                $slug = urlencode($keyword);
            ?>
                <a href="/keyword/<?= $slug ?>"
                   class="keyword-tag"
                   style="font-size:<?= number_format($size, 2) ?>rem;"
                   title="<?= $h(t('site.kw_pages_title', ['count' => $freq])) ?>"
                   aria-label="<?= $h(t('site.kw_pages_aria', ['keyword' => $keyword, 'count' => $freq])) ?>"><?= $h($keyword) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
