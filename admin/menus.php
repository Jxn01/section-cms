<?php
// ─── Admin — Menu Management ───
// CRUD for navigation menu items with nesting support.

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

// ─── Handle POST ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add new menu item
    if (!empty($_POST['add_menu'])) {
        csrfVerify();
        $label    = trim($_POST['label'] ?? '');
        $url      = trim($_POST['url'] ?? '');
        $pageId   = !empty($_POST['page_id']) ? (int) $_POST['page_id'] : null;
        $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;

        // If linked to a page, set URL from page slug
        if ($pageId) {
            $p = $pdo->prepare("SELECT slug FROM pages WHERE id = :id");
            $p->execute(['id' => $pageId]);
            $row = $p->fetch();
            if ($row) {
                $url = $row['slug'] === 'home' ? '/' : '/' . $row['slug'];
            }
        }

        // Ensure a valid URL exists (fallback to '#' if empty)
        if ($url === '') {
            $url = '#';
        }

        $maxOrder = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM menus")->fetchColumn();

        $stmt = $pdo->prepare(
            "INSERT INTO menus (label, url, page_id, parent_id, sort_order)
             VALUES (:label, :url, :pid, :parid, :ord)"
        );
        $stmt->execute([
            'label'  => $label,
            'url'    => $url,
            'pid'    => $pageId,
            'parid'  => $parentId,
            'ord'    => (int) $maxOrder + 1,
        ]);
        header('Location: /admin/menus.php?msg=added');
        exit;
    }

    // Update menu item
    if (!empty($_POST['update_menu'])) {
        csrfVerify();
        $id       = (int) $_POST['menu_id'];
        $label    = trim($_POST['label'] ?? '');
        $url      = trim($_POST['url'] ?? '');
        $pageId   = !empty($_POST['page_id']) ? (int) $_POST['page_id'] : null;
        $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;

        if ($pageId) {
            $p = $pdo->prepare("SELECT slug FROM pages WHERE id = :id");
            $p->execute(['id' => $pageId]);
            $row = $p->fetch();
            if ($row) {
                $url = $row['slug'] === 'home' ? '/' : '/' . $row['slug'];
            }
        }

        // Ensure a valid URL exists (fallback to '#' if empty)
        if ($url === '') {
            $url = '#';
        }

        $stmt = $pdo->prepare(
            "UPDATE menus SET label = :label, url = :url, page_id = :pid, parent_id = :parid WHERE id = :id"
        );
        $stmt->execute([
            'label' => $label,
            'url'   => $url,
            'pid'   => $pageId,
            'parid' => $parentId,
            'id'    => $id,
        ]);
        header('Location: /admin/menus.php?msg=saved');
        exit;
    }

    // Reorder (move up/down) — only swap within same group (top-level or same parent)
    if (!empty($_POST['move_menu']) && !empty($_POST['direction'])) {
        csrfVerify();
        $menuId = (int) $_POST['move_menu'];
        $dir    = $_POST['direction'];

        // Find the current item to determine its parent_id
        $currentItem = $pdo->prepare("SELECT id, parent_id, sort_order FROM menus WHERE id = :id");
        $currentItem->execute(['id' => $menuId]);
        $currentItem = $currentItem->fetch();

        if ($currentItem) {
            // Only select siblings (same parent_id)
            if ($currentItem['parent_id']) {
                $siblings = $pdo->prepare("SELECT id, sort_order FROM menus WHERE parent_id = :pid ORDER BY sort_order ASC");
                $siblings->execute(['pid' => $currentItem['parent_id']]);
            } else {
                $siblings = $pdo->query("SELECT id, sort_order FROM menus WHERE parent_id IS NULL ORDER BY sort_order ASC");
            }
            $allSiblings = $siblings->fetchAll();

            $idx = null;
            foreach ($allSiblings as $i => $m) {
                if ((int) $m['id'] === $menuId) { $idx = $i; break; }
            }
            if ($idx !== null) {
                $swapIdx = $dir === 'up' ? $idx - 1 : $idx + 1;
                if (isset($allSiblings[$swapIdx])) {
                    $pdo->prepare("UPDATE menus SET sort_order = :o WHERE id = :id")
                        ->execute(['o' => $allSiblings[$swapIdx]['sort_order'], 'id' => $menuId]);
                    $pdo->prepare("UPDATE menus SET sort_order = :o WHERE id = :id")
                        ->execute(['o' => $allSiblings[$idx]['sort_order'], 'id' => (int) $allSiblings[$swapIdx]['id']]);
                }
            }
        }
        header('Location: /admin/menus.php?msg=saved');
        exit;
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_menu'])) {
    csrfVerify();
    $id = (int) $_POST['delete_menu'];
    // Reset children to top-level
    $pdo->prepare("UPDATE menus SET parent_id = NULL WHERE parent_id = :id")->execute(['id' => $id]);
    $pdo->prepare("DELETE FROM menus WHERE id = :id")->execute(['id' => $id]);
    header('Location: /admin/menus.php?msg=deleted');
    exit;
}

// Fetch data
$menuItems = $pdo->query("SELECT m.*, p.title as page_title FROM menus m LEFT JOIN pages p ON m.page_id = p.id ORDER BY m.sort_order ASC")->fetchAll();
$allPages  = $pdo->query("SELECT id, title, slug FROM pages WHERE status = 'published' ORDER BY title ASC")->fetchAll();

// Separate top-level and children (full tree for display)
$topLevel = [];
$children = [];
foreach ($menuItems as $item) {
    if ($item['parent_id']) {
        $children[$item['parent_id']][] = $item;
    } else {
        $topLevel[] = $item;
    }
}

// Build flat list with depth for parent selector (supports N-level nesting)
$flatMenuWithDepth = [];
function buildFlatMenu($items, $children, $depth = 0) {
    global $flatMenuWithDepth;
    foreach ($items as $item) {
        $item['_depth'] = $depth;
        $flatMenuWithDepth[] = $item;
        if (!empty($children[$item['id']])) {
            buildFlatMenu($children[$item['id']], $children, $depth + 1);
        }
    }
}
buildFlatMenu($topLevel, $children);

// Edit mode
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    foreach ($menuItems as $m) {
        if ((int) $m['id'] === $editId) { $editItem = $m; break; }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>Menü kezelése</h1>
</div>

<div class="help-box">
    <strong>🧭 A navigációs menü a weboldal fejlécében jelenik meg.</strong> A látogatók innen navigálnak az oldalak között. A menüpontok lehetnek felső szintű (mindig látható) vagy almenüpontok (legördülő menüben jelennek meg).
    <button type="button" class="help-toggle" data-target="menuHelp" aria-expanded="false"><span class="help-toggle-icon">▸</span> Hogyan működik?</button>
    <div class="help-collapsible" id="menuHelp">
        <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
            <li><strong>Címke:</strong> A menüpont megjelenő neve a fejlécben (pl. „Szolgáltatásaink").</li>
            <li><strong>Oldal kiválasztása:</strong> Ha egy meglévő oldalhoz köti, az URL automatikusan beáll. Ez az ajánlott módszer!</li>
            <li><strong>Egyedi URL:</strong> Ha nem oldalhoz köti, adjon meg egyedi URL-t (pl. külső link: <code>https://facebook.com</code>).</li>
            <li><strong>Szülő menüpont:</strong> Ha beállít szülőt, a menüpont legördülő almenüben jelenik meg az adott szülő alatt.</li>
            <li><strong>Sorrend:</strong> A ▲/▼ gombokkal változtathatja a menüpontok sorrendjét. A sorrend azonos szinten belül érvényes.</li>
            <li><strong>Törlés:</strong> Ha egy szülő menüpontot töröl, az almenüpontjai felső szintűvé válnak.</li>
        </ul>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $msgs = ['added' => 'Menüpont hozzáadva.', 'saved' => 'Mentve.', 'deleted' => 'Menüpont törölve.'];
        echo $h($msgs[$_GET['msg']] ?? 'Kész.');
        ?>
    </div>
<?php endif; ?>

<!-- Current menu structure -->
<div style="margin-bottom:2rem;">
    <h2>Jelenlegi menüszerkezet</h2>
    <div class="table-responsive">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Címke</th>
                <th>URL</th>
                <th>Szülő</th>
                <th>Oldal</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Recursive table display function
            function renderMenuTableRows($items, $children, $h, $depth = 0) {
                foreach ($items as $item):
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $depth);
                    $prefix = $depth > 0 ? $indent . '↳ ' : '';
                    $bgStyle = $depth > 0 ? ' style="background:#' . ($depth === 1 ? 'f8fafc' : ($depth === 2 ? 'f1f5f9' : 'e8edf3')) . ';"' : '';
                    // Find parent label
                    $parentLabel = '—';
                    if ($item['parent_id']) {
                        global $flatMenuWithDepth;
                        foreach ($flatMenuWithDepth as $fm) {
                            if ((int)$fm['id'] === (int)$item['parent_id']) {
                                $parentLabel = $h($fm['label']);
                                break;
                            }
                        }
                    }
            ?>
                <tr<?= $bgStyle ?>>
                    <td><?= $prefix ?><?= $depth === 0 ? '<strong>' : '' ?><?= $h($item['label']) ?><?= $depth === 0 ? '</strong>' : '' ?></td>
                    <td><code><?= $h($item['url']) ?></code></td>
                    <td><?= $parentLabel ?></td>
                    <td><?= $h($item['page_title'] ?? '–') ?></td>
                    <td class="actions">
                        <form method="POST" style="display:inline;">
                            <?= csrfField() ?>
                            <input type="hidden" name="direction" value="up">
                            <button type="submit" name="move_menu" value="<?= $item['id'] ?>" class="btn btn-xs">▲</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <?= csrfField() ?>
                            <input type="hidden" name="direction" value="down">
                            <button type="submit" name="move_menu" value="<?= $item['id'] ?>" class="btn btn-xs">▼</button>
                        </form>
                        <a href="/admin/menus.php?edit=<?= $item['id'] ?>" class="btn btn-sm">Szerkesztés</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Biztosan törli?')">
                            <?= csrfField() ?>
                            <button type="submit" name="delete_menu" value="<?= $item['id'] ?>" class="btn btn-sm btn-danger">Törlés</button>
                        </form>
                    </td>
                </tr>
                <?php
                    if (!empty($children[$item['id']])) {
                        renderMenuTableRows($children[$item['id']], $children, $h, $depth + 1);
                    }
                endforeach;
            }
            renderMenuTableRows($topLevel, $children, $h);
            ?>
            <?php if (empty($topLevel)): ?>
                <tr><td colspan="5" style="text-align:center">Nincs menüpont.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Edit form (if editing) -->
<?php if ($editItem): ?>
<form method="POST" class="admin-form" style="margin-bottom:2rem;">
    <?= csrfField() ?>
    <fieldset>
        <legend>Menüpont szerkesztése: <?= $h($editItem['label']) ?></legend>
        <input type="hidden" name="menu_id" value="<?= $editItem['id'] ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Címke</label>
                <input type="text" name="label" value="<?= $h($editItem['label']) ?>" required>
            </div>
            <div class="form-group">
                <label>Egyedi URL (ha nincs oldalhoz kötve)</label>
                <input type="text" name="url" value="<?= $h($editItem['url']) ?>" placeholder="/oldal-neve">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Oldal kiválasztása (opcionális)</label>
                <select name="page_id">
                    <option value="">– Nincs (egyedi URL) –</option>
                    <?php foreach ($allPages as $pg): ?>
                        <option value="<?= $pg['id'] ?>" <?= (int)($editItem['page_id'] ?? 0) === (int)$pg['id'] ? 'selected' : '' ?>>
                            <?= $h($pg['title']) ?> (/<?= $h($pg['slug']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Szülő menüpont (legördülőhöz)</label>
                <select name="parent_id">
                    <option value="">– Felső szint –</option>
                    <?php foreach ($flatMenuWithDepth as $fm): ?>
                        <?php if ((int) $fm['id'] !== (int) $editItem['id']): ?>
                            <option value="<?= $fm['id'] ?>" <?= (int)($editItem['parent_id'] ?? 0) === (int)$fm['id'] ? 'selected' : '' ?>>
                                <?= str_repeat('\u00a0\u00a0\u00a0', $fm['_depth']) ?><?= $h($fm['label']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" name="update_menu" value="1" class="btn btn-primary">Mentés</button>
            <a href="/admin/menus.php" class="btn btn-secondary">Mégse</a>
        </div>
    </fieldset>
</form>
<?php endif; ?>

<!-- Add new menu item -->
<form method="POST" class="admin-form">
    <?= csrfField() ?>
    <fieldset>
        <legend>Új menüpont hozzáadása</legend>
        <div class="form-row">
            <div class="form-group">
                <label>Címke
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A menüpont szövege, ami a navigációs sávban megjelenik. Legyen rövid és érthetó (pl. „Szolgáltatásaink", „Kapcsolat").</span></span>
                </label>
                <input type="text" name="label" required placeholder="pl. Szolgáltatások">
            </div>
            <div class="form-group">
                <label>Egyedi URL (ha nincs oldalhoz kötve)
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Csak akkor kell kitölteni, ha nem oldalt választ alább. Használja külső linkekhez (pl. https://facebook.com) vagy speciális útvonalakhoz.</span></span>
                </label>
                <input type="text" name="url" placeholder="/oldal-neve vagy https://...">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Oldal kiválasztása (opcionális)
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Ha egy meglévő oldalhoz köti a menüpontot, az URL automatikusan az oldal slug-jára áll be. Ez az ajánlott módszer!</span></span>
                </label>
                <select name="page_id">
                    <option value="">– Nincs (egyedi URL) –</option>
                    <?php foreach ($allPages as $pg): ?>
                        <option value="<?= $pg['id'] ?>"><?= $h($pg['title']) ?> (/<?= $h($pg['slug']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Szülő menüpont (legördülőhöz)
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Ha szülőt választ, ez a menüpont almenüként (legördülő menüben) jelenik meg a kiválasztott szülő alatt. „Felső szint" = önálló fő menüpont.</span></span>
                </label>
                <select name="parent_id">
                    <option value="">– Felső szint –</option>
                    <?php foreach ($flatMenuWithDepth as $fm): ?>
                        <option value="<?= $fm['id'] ?>">
                            <?= str_repeat('\u00a0\u00a0\u00a0', $fm['_depth']) ?><?= $h($fm['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" name="add_menu" value="1" class="btn btn-primary">Hozzáadás</button>
    </fieldset>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
