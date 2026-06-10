<?php
// ─── Admin — Pages List ───

require_once __DIR__ . '/auth.php';
requireLogin();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_page'])) {
    csrfVerify();
    $id = (int) $_POST['delete_page'];
    $stmt = $pdo->prepare("DELETE FROM pages WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: /admin/pages.php?msg=deleted');
    exit;
}

$pages = $pdo->query(
    "SELECT p.*, COUNT(s.id) as section_count
     FROM pages p
     LEFT JOIN sections s ON s.page_id = p.id
     GROUP BY p.id
     ORDER BY p.sort_order ASC"
)->fetchAll();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>Oldalak</h1>
    <a href="/admin/page-edit.php?new=1" class="btn btn-primary">+ Új oldal</a>
</div>

<div class="help-box">
    <strong>📄 Az oldalak a weboldal fő építőelemei.</strong> Minden oldal egyedi URL-en (slug-on) érhető el, és szekciókat tartalmaz (szöveg, kép, galéria, stb.). Az oldalak lehetnek „Publikált" (mindenki látja) vagy „Piszkozat" (csak itt látható).
    <button type="button" class="help-toggle" data-target="pagesHelp" aria-expanded="false"><span class="help-toggle-icon">▸</span> Bővebb segítség</button>
    <div class="help-collapsible" id="pagesHelp">
        <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
            <li><strong>Cím:</strong> Az oldal neve, ami a böngésző fülön és a fejlécben jelenik meg.</li>
            <li><strong>Slug:</strong> Az URL végén megjelenő azonosító (pl. <code>/szolgaltatasaink</code>). Csak kisbetűk, számok és kötőjel.</li>
            <li><strong>Típus:</strong> „Oldal" — normál tartalom. „Cikk" — blogbejegyzés, speciális SEO jelöléssel (szerző, dátum).</li>
            <li><strong>Szekciók:</strong> Egy oldal tetszőleges számú szekcióból (blokkból) áll. A szekciók sorrendje határozza meg az oldal felépítését.</li>
            <li><strong>Státusz:</strong> „Publikált" = nyilvánosan elérhető, a Google is indexeli. „Piszkozat" = rejtett, csak az adminban látható.</li>
            <li><strong>home slug:</strong> A főoldal (<code>/</code>). Ez az oldal nem törölhető és slug-ja nem változtatható.</li>
        </ul>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $msgs = [
            'saved'   => 'Oldal sikeresen mentve.',
            'created' => 'Új oldal létrehozva.',
            'deleted' => 'Oldal törölve.',
        ];
        echo $h($msgs[$_GET['msg']] ?? 'Kész.');
        ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
<table class="admin-table">
    <thead>
        <tr>
            <th>Cím</th>
            <th>Slug</th>
            <th>Típus</th>
            <th>Szekciók</th>
            <th>Státusz</th>
            <th>Műveletek</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pages)): ?>
            <tr><td colspan="6" style="text-align:center">Nincsenek oldalak.</td></tr>
        <?php endif; ?>
        <?php foreach ($pages as $p): ?>
            <tr>
                <td><strong><?= $h($p['title']) ?></strong></td>
                <td><code>/<?= $h($p['slug']) ?></code></td>
                <td><?= ($p['page_type'] ?? 'page') === 'article' ? '<span class="badge" style="background:#E0E7FF;color:#3730A3;">Cikk</span>' : 'Oldal' ?></td>
                <td><?= (int) $p['section_count'] ?></td>
                <td>
                    <span class="badge badge-<?= $p['status'] ?>">
                        <?= $p['status'] === 'published' ? 'Publikált' : 'Piszkozat' ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="/admin/page-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm">Szerkesztés</a>
                    <?php if ($p['slug'] !== 'home'): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Biztosan törli?')">
                            <?= csrfField() ?>
                            <button type="submit" name="delete_page" value="<?= $p['id'] ?>"
                                    class="btn btn-sm btn-danger">Törlés</button>
                        </form>
                    <?php endif; ?>
                    <a href="/<?= $p['slug'] === 'home' ? '' : $h($p['slug']) ?>"
                       target="_blank" class="btn btn-sm">Megtekintés ↗</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
