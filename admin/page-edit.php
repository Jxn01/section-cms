<?php
// ─── Admin — Page Editor ───
// Handles creating new pages (with template) and editing existing ones.

require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../core/sanitize.php';

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$templates = require __DIR__ . '/../config/templates.php';

$isNew = isset($_GET['new']);
$pageId = isset($_GET['id']) ? (int) $_GET['id'] : null;

// Hungarian character transliteration for slug generation
function transliterateHu(string $str): string {
    $map = ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','ő'=>'o','ú'=>'u','ü'=>'u','ű'=>'u',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ö'=>'o','Ő'=>'o','Ú'=>'u','Ü'=>'u','Ű'=>'u'];
    return strtr($str, $map);
}

// All available section types with defaults
$sectionDefaults = [
    'hero'           => ['heading' => 'Új fejléc', 'subtitle' => '', 'image' => '', 'cta_text' => '', 'cta_url' => ''],
    'text'           => ['heading' => 'Új szöveg', 'body' => '<p>Szöveg...</p>'],
    'image_text'     => ['heading' => 'Kép és szöveg', 'body' => '<p>Szöveg...</p>', 'image' => '', 'image_alt' => '', 'image_position' => 'right'],
    'gallery'        => ['heading' => 'Galéria', 'images' => []],
    'cta'            => ['heading' => 'CTA', 'subtitle' => '', 'button_text' => 'Tovább', 'button_url' => '/', 'background_color' => '#0067FF'],
    'cards'          => ['heading' => 'Kártyák', 'cards' => [['title' => 'Kártya', 'description' => 'Leírás', 'icon' => '⭐', 'link' => '']]],
    'ticker'         => ['items' => [['text' => 'Hír szövege...', 'link' => '']], 'speed' => 30, 'background_color' => '#0067FF', 'text_color' => '#FFFFFF'],
    'accordion'      => ['heading' => 'Gyakran Ismételt Kérdések', 'items' => [['question' => 'Kérdés?', 'answer' => '<p>Válasz.</p>']]],
    'video'          => ['heading' => 'Videó', 'url' => '', 'type' => 'youtube'],
    'divider'        => ['style' => 'line', 'spacing' => 'normal'],
    'two_columns'    => ['heading' => '', 'left_body' => '<p>Bal oszlop szövege...</p>', 'right_body' => '<p>Jobb oszlop szövege...</p>'],
    'testimonials'   => ['heading' => 'Vélemények', 'items' => [['name' => 'Név', 'text' => 'Vélemény szövege...', 'role' => '', 'image' => '']]],
    'stats'          => ['heading' => '', 'background_color' => '#0067FF', 'items' => [['number' => '100+', 'label' => 'Ügyfél']]],
    'page_list'      => ['heading' => 'Cikkek', 'page_type' => 'article', 'count' => 10],
    'map'            => ['heading' => 'Térkép', 'embed_url' => '', 'height' => '400'],
    'contact_form'   => ['heading' => 'Kapcsolat', 'success_message' => 'Köszönjük az üzenetet! Hamarosan felvesszük Önnel a kapcsolatot.'],
    'keywords_cloud' => ['heading' => 'Kulcsszavak', 'count' => 50],
    'hero_slideshow'    => ['heading' => 'Parkoló ABC', 'subtitle' => '', 'cta_text' => '', 'cta_url' => '', 'interval' => 4, 'overlay_boxes' => []],
    'product_grid'      => ['heading' => 'Termékek', 'columns' => 5, 'items' => [['title' => 'Termék', 'short_desc' => 'Leírás', 'image' => '', 'image_alt' => '', 'url' => '']]],
    'seo_hidden'        => ['button_text' => 'Tovább olvasom...', 'body' => '<p>SEO szöveg...</p>'],
    'link_banner'       => ['text' => 'Alapfogalmak', 'url' => '/alapfogalmak', 'icon' => '📖', 'background_color' => '#0067FF'],
    'reference_gallery' => ['heading' => 'Referenciáink', 'projects' => [['title' => 'Projekt', 'cover_image' => '', 'cover_alt' => '', 'images' => []]]],
    'sitemap'            => ['heading' => 'Oldaltérkép'],
    'tudasmorzsak'       => ['heading' => 'Tudásmorzsák', 'items' => [['title' => 'Fogalom', 'description' => 'Rövid leírás...', 'url' => '']]],
];

// Section type labels in Hungarian
$sectionLabels = [
    'hero'           => 'Hero (fejléckép)',
    'text'           => 'Szöveg',
    'image_text'     => 'Kép + szöveg',
    'cards'          => 'Kártyák',
    'cta'            => 'CTA (cselekvésre ösztönzés)',
    'gallery'        => 'Galéria',
    'ticker'         => 'Futó szöveg (hírszalag)',
    'accordion'      => 'Harmonika (GYIK)',
    'video'          => 'Videó',
    'divider'        => 'Elválasztó',
    'two_columns'    => 'Két oszlop',
    'testimonials'   => 'Vélemények',
    'stats'          => 'Számok / Statisztika',
    'page_list'      => 'Oldal/cikk lista',
    'map'            => 'Térkép',
    'contact_form'   => 'Kapcsolat űrlap',
    'keywords_cloud' => 'Kulcsszó felhő',
    'hero_slideshow'    => 'Hero diavetítés',
    'product_grid'      => 'Termékrács (hover leírás)',
    'seo_hidden'        => 'Rejtett SEO szöveg',
    'link_banner'       => 'Linksáv (hivatkozás)',
    'reference_gallery' => 'Referencia galéria',
    'sitemap'              => 'Oldaltérkép',
    'tudasmorzsak'         => 'Tudásmorzsák',
];

// Section type descriptions in Hungarian
$sectionDescriptions = [
    'hero'           => 'Teljes szélességű fejléckép nagy címsorral, alcímmel és opcionális CTA gombbal. Ideális az oldal tetejére, hogy azonnal megragadja a figyelmet.',
    'text'           => 'Egyszerű szöveges blokk címsorral és formázható tartalommal (félkövér, lista, link stb.). A leggyakrabban használt szekciótípus.',
    'image_text'     => 'Kép és szöveg egymás mellett. A kép lehet bal vagy jobb oldalon. Tökéletes szolgáltatás vagy termék bemutatásához.',
    'cards'          => 'Kártyák rácsban, mindegyik ikonnal, címmel és leírással. Ideális szolgáltatások vagy előnyök felsorolásához.',
    'cta'            => 'Cselekvésre ösztönző sáv háttérszínnel, szöveggel és gombbal. Használja az oldal közepén vagy végén a látogató aktivizálásához.',
    'gallery'        => 'Képgaléria rács elrendezésben. A képek kattintásra nagyíthatók. Ideális referenciák vagy projektek bemutatásához.',
    'ticker'         => 'Vízszintesen futó szövegszalag. Figyelemfelkeltő hírek, akciók vagy fontos információk megjelenítésére.',
    'accordion'      => 'Lenyíló kérdés-válasz elemek. Tökéletes GYIK (Gyakran Ismételt Kérdések) oldalakhoz. A Google is szereti, mert FAQ strukturált adatot generál.',
    'video'          => 'YouTube vagy Vimeo videó beágyazás. A videó reszponzívan jelenik meg minden eszközön.',
    'divider'        => 'Vizuális elválasztó vonal, pontok, hullám vagy üres tér. Szekciók közötti vizuális szünet létrehozásához.',
    'two_columns'    => 'Két oszlopos szövegelrendezés. Mindkét oszlop formázható (félkövér, lista, link stb.).',
    'testimonials'   => 'Ügyfélvélemények kártyákon, névvel, pozícióval és opcionális fotóval. Növeli a bizalmat az új látogatóknál.',
    'stats'          => 'Számok/statisztikák kiemelése nagy betűmérettel (pl. „100+ Ügyfél", „15 Év tapasztalat"). Háttérszín beállítható.',
    'page_list'      => 'Automatikus oldal- vagy cikklista. A rendszer a megadott típusú oldalakat (cikk/oldal) listázza ki címmel, leírással és képpel.',
    'map'            => 'Google Maps beágyazott térkép. Illessze be a Google Maps beágyazási URL-t az iroda vagy telephely megjelenítéséhez.',
    'contact_form'   => 'Kapcsolatfelvételi űrlap (név, e-mail, telefon, üzenet). A beérkezett üzenetek az Üzenetek menüben olvashatók.',
    'keywords_cloud' => 'Automatikus kulcsszó felhő — összegyűjti az összes oldal kulcsszavait és a leggyakoribbakat jeleníti meg linkekkel. Belső linkelésre kiváló.',
    'hero_slideshow'    => 'Főoldali diavetítés a Médiában „kiemelt" jelölésű képekből, automatikus képváltással. Navigációs dobozokat is elhelyezhet a képen.',
    'product_grid'      => 'Termék/eszköz rács képekkel. Kurzor ráhúzásakor a kép helyén megjelenik a rövid leírás, kattintásra a részletes oldalra navigál.',
    'seo_hidden'        => 'Rejtett lenyitható szöveg — a Google látja és indexeli, de a weboldalon csak gombra kattintva jelenik meg. Hirdetési szövegek, kulcsszavak elhelyezésére ideális.',
    'link_banner'       => 'Széles színes sáv, amely egy másik oldalra mutat (pl. Alapfogalmak). Ikonnal, szöveggel és nyíllal jelenik meg.',
    'reference_gallery' => 'Projektek alapján csoportosított galéria. Soronként 4 borítókép; kurzor ráhúzásakor további képek jelennek meg az adott projektből.',
    'sitemap'            => 'Automatikus, hierarchikus oldaltérkép a menüszerkezet és az összes publikált oldal alapján. Segíti a Google indexálást és a látogatói navigációt.',
    'tudasmorzsak'       => 'Kis tudásdobozok (lexikon jellegű): cím, rövid leírás és opcionális link. Random sorrendben, jobbról beússzanak. Ideális szakkifejezések, fogalmak bemutatására.',
];

// ─── Handle POST (save) ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Creating a new page from template
    if (!empty($_POST['create_page'])) {
        csrfVerify();
        $title    = trim($_POST['title'] ?? 'Új oldal');
        $slug     = trim($_POST['slug'] ?? '');
        $template = $_POST['template'] ?? 'hero_two_text';

        if ($slug === '') {
            $slug = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', mb_strtolower(transliterateHu($title))));
        }
        // Sanitize slug
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'oldal-' . time();
        }

        // Check for duplicate slug
        $dupCheck = $pdo->prepare("SELECT id FROM pages WHERE slug = :slug");
        $dupCheck->execute(['slug' => $slug]);
        if ($dupCheck->fetch()) {
            $slug = $slug . '-' . time();
        }

        // Fetch site name for SEO defaults
        $siteNameRow = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name'")->fetch();
        $siteNameVal = $siteNameRow ? $siteNameRow['setting_value'] : 'Parkoló ABC';

        // Auto-detect page_type from template
        $pageType = ($template === 'article') ? 'article' : 'page';

        $stmt = $pdo->prepare(
            "INSERT INTO pages (slug, title, template, page_type, meta_title, meta_description, status, sort_order)
             VALUES (:slug, :title, :template, :page_type, :meta_title, :meta_desc, 'draft',
                     (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM pages p2))"
        );
        $stmt->execute([
            'slug'       => $slug,
            'title'      => $title,
            'template'   => $template,
            'page_type'  => $pageType,
            'meta_title' => $title . ' – ' . $siteNameVal,
            'meta_desc'  => $title . ' – ' . $siteNameVal . '. Tudjon meg többet szolgáltatásainkról.',
        ]);
        $newPageId = (int) $pdo->lastInsertId();

        // Auto-create sections from template
        if (isset($templates[$template])) {
            $order = 1;
            foreach ($templates[$template]['sections'] as $sec) {
                $stmt2 = $pdo->prepare(
                    "INSERT INTO sections (page_id, type, content, sort_order)
                     VALUES (:pid, :type, :content, :ord)"
                );
                $stmt2->execute([
                    'pid'     => $newPageId,
                    'type'    => $sec['type'],
                    'content' => json_encode($sec['content'], JSON_UNESCAPED_UNICODE),
                    'ord'     => $order++,
                ]);
            }
        }

        header('Location: /admin/page-edit.php?id=' . $newPageId . '&msg=created');
        exit;
    }

    // Saving existing page
    if (!empty($_POST['save_page']) && $pageId) {
        csrfVerify();
        $saveTitle     = trim($_POST['title'] ?? '');
        $saveSlug      = trim($_POST['slug'] ?? '');
        $saveMetaTitle = trim($_POST['meta_title'] ?? '');
        $saveMetaDesc  = trim($_POST['meta_description'] ?? '');
        $saveOgTitle   = trim($_POST['og_title'] ?? '');
        $saveOgDesc    = trim($_POST['og_description'] ?? '');

        // Sanitize slug (allow only lowercase letters, numbers, hyphens)
        $saveSlug = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', mb_strtolower(transliterateHu($saveSlug))));
        $saveSlug = trim($saveSlug, '-');
        if ($saveSlug === '') {
            $saveSlug = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', mb_strtolower(transliterateHu($saveTitle))));
            $saveSlug = trim($saveSlug, '-');
        }

        // Check for duplicate slug (exclude current page)
        $dupCheck = $pdo->prepare("SELECT id FROM pages WHERE slug = :slug AND id != :id");
        $dupCheck->execute(['slug' => $saveSlug, 'id' => $pageId]);
        if ($dupCheck->fetch()) {
            $saveSlug = $saveSlug . '-' . time();
        }

        // Validate page_type and status
        $savePageType = $_POST['page_type'] ?? 'page';
        if (!in_array($savePageType, ['page', 'article'], true)) { $savePageType = 'page'; }
        $saveStatus = $_POST['status'] ?? 'draft';
        if (!in_array($saveStatus, ['draft', 'published'], true)) { $saveStatus = 'draft'; }

        // Auto-fill SEO fields from title if empty
        if ($saveMetaTitle === '') {
            $sn = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name'")->fetchColumn();
            $saveMetaTitle = $saveTitle . ' – ' . ($sn ?: 'Parkoló ABC');
        }
        if ($saveOgTitle === '') {
            $saveOgTitle = $saveMetaTitle;
        }
        if ($saveOgDesc === '' && $saveMetaDesc !== '') {
            $saveOgDesc = $saveMetaDesc;
        }

        // Update page meta
        $stmt = $pdo->prepare(
            "UPDATE pages SET
                title = :title,
                slug = :slug,
                page_type = :page_type,
                meta_title = :meta_title,
                meta_description = :meta_description,
                meta_keywords = :meta_keywords,
                og_title = :og_title,
                og_description = :og_description,
                featured_image = :featured_image,
                status = :status
             WHERE id = :id"
        );
        $stmt->execute([
            'title'            => $saveTitle,
            'slug'             => $saveSlug,
            'page_type'        => $savePageType,
            'meta_title'       => $saveMetaTitle,
            'meta_description' => $saveMetaDesc,
            'meta_keywords'    => trim($_POST['meta_keywords'] ?? ''),
            'og_title'         => $saveOgTitle,
            'og_description'   => $saveOgDesc,
            'featured_image'   => trim($_POST['featured_image'] ?? ''),
            'status'           => $saveStatus,
            'id'               => $pageId,
        ]);

        // Update each section's content
        if (!empty($_POST['sections']) && is_array($_POST['sections'])) {
            foreach ($_POST['sections'] as $secId => $secData) {
                // Handle special JSON fields
                if (isset($secData['_raw_json'])) {
                    $decoded = json_decode($secData['_raw_json'], true);
                    if ($decoded !== null) {
                        $secData = $decoded;
                    }
                }
                if (isset($secData['images_json'])) {
                    $images = json_decode($secData['images_json'], true);
                    if ($images !== null) {
                        $secData['images'] = $images;
                    }
                    unset($secData['images_json']);
                }
                if (isset($secData['projects_json'])) {
                    $projects = json_decode($secData['projects_json'], true);
                    if ($projects !== null) {
                        $secData['projects'] = $projects;
                    }
                    unset($secData['projects_json']);
                }

                // Sanitize WYSIWYG HTML fields
                $htmlFields = ['body', 'left_body', 'right_body'];
                foreach ($htmlFields as $hf) {
                    if (isset($secData[$hf])) {
                        $secData[$hf] = sanitizeHtml($secData[$hf]);
                    }
                }
                // Sanitize accordion answers
                if (isset($secData['items']) && is_array($secData['items'])) {
                    foreach ($secData['items'] as &$item) {
                        if (isset($item['answer'])) {
                            $item['answer'] = sanitizeHtml($item['answer']);
                        }
                    }
                    unset($item);
                }

                $contentJson = json_encode($secData, JSON_UNESCAPED_UNICODE);
                $stmt2 = $pdo->prepare(
                    "UPDATE sections SET content = :content WHERE id = :id AND page_id = :pid"
                );
                $stmt2->execute([
                    'content' => $contentJson,
                    'id'      => (int) $secId,
                    'pid'     => $pageId,
                ]);
            }
        }

        header('Location: /admin/page-edit.php?id=' . $pageId . '&msg=saved');
        exit;
    }

    // Add a new section
    if (!empty($_POST['add_section']) && $pageId) {
        csrfVerify();
        $type = $_POST['new_section_type'] ?? 'text';
        if (!isset($sectionDefaults[$type])) { $type = 'text'; }
        $content = $sectionDefaults[$type];

        $maxOrder = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM sections WHERE page_id = :pid");
        $maxOrder->execute(['pid' => $pageId]);
        $nextOrder = (int) $maxOrder->fetchColumn() + 1;

        $stmt = $pdo->prepare(
            "INSERT INTO sections (page_id, type, content, sort_order)
             VALUES (:pid, :type, :content, :ord)"
        );
        $stmt->execute([
            'pid'     => $pageId,
            'type'    => $type,
            'content' => json_encode($content, JSON_UNESCAPED_UNICODE),
            'ord'     => $nextOrder,
        ]);

        header('Location: /admin/page-edit.php?id=' . $pageId . '&msg=saved');
        exit;
    }

    // Delete a section
    if (!empty($_POST['delete_section'])) {
        csrfVerify();
        $secId = (int) $_POST['delete_section'];
        $pdo->prepare("DELETE FROM sections WHERE id = :id AND page_id = :pid")->execute(['id' => $secId, 'pid' => $pageId]);
        header('Location: /admin/page-edit.php?id=' . $pageId . '&msg=saved');
        exit;
    }

    // Move section up/down
    if (!empty($_POST['move_section']) && !empty($_POST['direction'])) {
        csrfVerify();
        $secId = (int) $_POST['move_section'];
        $dir   = $_POST['direction'];

        $sections = $pdo->prepare(
            "SELECT id, sort_order FROM sections WHERE page_id = :pid ORDER BY sort_order ASC"
        );
        $sections->execute(['pid' => $pageId]);
        $allSecs = $sections->fetchAll();

        $idx = null;
        foreach ($allSecs as $i => $s) {
            if ((int) $s['id'] === $secId) { $idx = $i; break; }
        }

        if ($idx !== null) {
            $swapIdx = $dir === 'up' ? $idx - 1 : $idx + 1;
            if (isset($allSecs[$swapIdx])) {
                $pdo->prepare("UPDATE sections SET sort_order = :o WHERE id = :id")
                    ->execute(['o' => $allSecs[$swapIdx]['sort_order'], 'id' => $secId]);
                $pdo->prepare("UPDATE sections SET sort_order = :o WHERE id = :id")
                    ->execute(['o' => $allSecs[$idx]['sort_order'], 'id' => (int) $allSecs[$swapIdx]['id']]);
            }
        }

        header('Location: /admin/page-edit.php?id=' . $pageId . '&msg=saved');
        exit;
    }
}

// ─── Show new page form ───
if ($isNew) {
    require __DIR__ . '/includes/header.php';
    ?>
    <h1>Új oldal létrehozása</h1>
    <div class="help-box">
        <strong>📄 Új oldal létrehozása sablon alapján.</strong> Válasszon egy sablont — ez meghatározza, milyen szekciókkal indul az oldal. Később tetszőlegesen szerkesztheti, hozzáadhat vagy törölhet szekciókat.
    </div>
    <form method="POST" class="admin-form">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="title">Oldal címe
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Az oldal főcíme. Megjelenik a böngésző fülön és fejlécben. Ebből generálódik a slug (URL) is.</span></span>
            </label>
            <input type="text" id="title" name="title" required placeholder="pl. Szolgáltatásaink">
        </div>
        <div class="form-group">
            <label for="slug">Slug (URL)
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Az URL végén megjelenő azonosító. Ha üresen hagyja, a rendszer automatikusan generálja a címből. Csak kisbetűk, számok és kötőjel használható.</span></span>
            </label>
            <input type="text" id="slug" name="slug" placeholder="pl. szolgaltatasaink (üresen hagyva automatikus)">
            <div class="field-hint">Az oldal elérhetősége: parkoloabc.hu/<strong>slug</strong></div>
        </div>
        <div class="form-group">
            <label for="template">Sablon
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A sablon meghatározza az oldal kezdeti felépítését (milyen szekciókkal indul). A létrehozás után szabadon módosíthatja.</span></span>
            </label>
            <select id="template" name="template">
                <?php foreach ($templates as $key => $tpl): ?>
                    <option value="<?= $h($key) ?>"><?= $h($tpl['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="create_page" value="1" class="btn btn-primary">Létrehozás</button>
        <a href="/admin/pages.php" class="btn btn-secondary">Mégse</a>
    </form>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// ─── Load existing page ───
if (!$pageId) {
    header('Location: /admin/pages.php');
    exit;
}

$page = $pdo->prepare("SELECT * FROM pages WHERE id = :id");
$page->execute(['id' => $pageId]);
$page = $page->fetch();

if (!$page) {
    header('Location: /admin/pages.php');
    exit;
}

$sections = $pdo->prepare(
    "SELECT * FROM sections WHERE page_id = :pid ORDER BY sort_order ASC"
);
$sections->execute(['pid' => $pageId]);
$sections = $sections->fetchAll();

// Fetch all published pages for page selector dropdowns
$allPagesForLinks = $pdo->query("SELECT id, title, slug FROM pages WHERE status = 'published' ORDER BY title ASC")->fetchAll();

// Helper: render a page selector dropdown for a URL field
function pageSelector($allPages, $targetInputId, $h) {
    ?>
    <select class="page-selector" data-link-target="<?= $h($targetInputId) ?>" style="margin-top:0.25rem;">
        <option value="">— Oldal kiválasztása —</option>
        <?php foreach ($allPages as $pg): ?>
            <option value="<?= $pg['slug'] === 'home' ? '/' : '/' . $h($pg['slug']) ?>">
                <?= $h($pg['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

// Helper: render a page selector using __IDX__ placeholder for templates
function pageSelectorTpl($allPages, $targetIdPattern, $h) {
    ?>
    <select class="page-selector" data-link-target="<?= $h($targetIdPattern) ?>" style="margin-top:0.25rem;">
        <option value="">— Oldal kiválasztása —</option>
        <?php foreach ($allPages as $pg): ?>
            <option value="<?= $pg['slug'] === 'home' ? '/' : '/' . $h($pg['slug']) ?>">
                <?= $h($pg['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

// Helper: render remove button for a repeater item
function repeaterRemoveBtn() {
    echo '<button type="button" class="repeater-remove" title="Elem törlése">✕</button>';
}

require __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $msgs = ['saved' => 'Mentve.', 'created' => 'Oldal létrehozva.'];
        echo $h($msgs[$_GET['msg']] ?? 'Kész.');
        ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <h1>Szerkesztés: <?= $h($page['title']) ?></h1>
    <a href="/<?= $page['slug'] === 'home' ? '' : $h($page['slug']) ?>"
       target="_blank" class="btn btn-secondary">Megtekintés ↗</a>
</div>

<form method="POST" class="admin-form">
    <?= csrfField() ?>
    <fieldset>
        <legend>Oldal adatok</legend>
        <div class="form-row">
            <div class="form-group">
                <label for="title">Cím
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Az oldal főcíme. Megjelenik a fejlécben és a böngésző fülön. Nem összekeverendő a „Meta cím"-mel (SEO).</span></span>
                </label>
                <input type="text" id="title" name="title" value="<?= $h($page['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="slug">Slug
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Az URL végén megjelenő azonosító. Pl. „szolgaltatasaink" → parkoloabc.hu/szolgaltatasaink. Módosítás esetén a régi URL nem fog működni!</span></span>
                </label>
                <input type="text" id="slug" name="slug" value="<?= $h($page['slug']) ?>"
                    <?= $page['slug'] === 'home' ? 'readonly' : '' ?>>
                <small class="form-help">Csak kisbetűk, számok és kötőjelek. Ez lesz az URL: parkoloabc.hu/<strong>slug</strong></small>
            </div>
            <div class="form-group">
                <label for="status">Státusz
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">„Publikált" = az oldal nyilvánosan elérhető és a Google indexeli. „Piszkozat" = rejtett, csak az admin felületen látható.</span></span>
                </label>
                <select id="status" name="status">
                    <option value="draft" <?= $page['status'] === 'draft' ? 'selected' : '' ?>>Piszkozat</option>
                    <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?>>Publikált</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="page_type">Típus
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">„Oldal" = normál tartalom. „Cikk" = blogbejegyzés speciális SEO jelöléssel (szerző, dátum). A cikkek a „Cikklista" szekcióban is megjelennek.</span></span>
                </label>
                <select id="page_type" name="page_type">
                    <option value="page" <?= ($page['page_type'] ?? 'page') === 'page' ? 'selected' : '' ?>>Oldal</option>
                    <option value="article" <?= ($page['page_type'] ?? '') === 'article' ? 'selected' : '' ?>>Cikk</option>
                </select>
            </div>
            <div class="form-group">
                <label for="featured_image">Kiemelt kép URL
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Az oldal kiemelt képe. Megjelenik a közösségi médiában (Facebook, LinkedIn) megosztásnál és a cikklistában. A „Tallózás" gombbal választhat a feltöltött képek közül.</span></span>
                </label>
                <div class="input-with-browse">
                    <input type="text" id="featured_image" name="featured_image"
                           value="<?= $h($page['featured_image'] ?? '') ?>" placeholder="/assets/uploads/kep.jpg">
                    <button type="button" class="btn btn-sm browse-media-btn" data-target="featured_image">Tallózás</button>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>SEO (keresőoptimalizálás)
            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A SEO beállítások határozzák meg, hogyan jelenik meg az oldal a Google keresési eredményekben és a közösségi médiában. Nagyon fontos a jó helyezéshez!</span></span>
        </legend>
        <div class="help-box" style="margin-bottom:1rem;">
            <strong>🔍 Miért fontos a SEO?</strong> A Google és más keresőmotorok ezeket az adatokat használják az oldal megjelenítéséhez a találati listában.
            <button type="button" class="help-toggle" data-target="seoHelp" aria-expanded="false"><span class="help-toggle-icon">▸</span> SEO segítség</button>
            <div class="help-collapsible" id="seoHelp">
                <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
                    <li><strong>Meta cím:</strong> A Google találatokban félkövér kékkel jelenik meg. Max. 60 karakter ajánlott — ennél hosszabb szöveget a Google levágja.</li>
                    <li><strong>Meta leírás:</strong> A Google találatokban a cím alatt szürkén jelenik meg. Max. 160 karakter. Írjon vonzó, cselekvésre ösztönző szöveget!</li>
                    <li><strong>Kulcsszavak:</strong> Vesszővel elválasztva adja meg (pl. „parkoló, sorompó, beléptető rendszer"). Segít a Google-nek megérteni az oldal tartalmát.</li>
                    <li><strong>OG adatok:</strong> Ezek jelennek meg, ha valaki megosztja az oldalt Facebookon, LinkedIn-en stb. Ha üresen hagyja, a meta cím/leírás lesz használva.</li>
                    <li>💡 <em>Tipp: Minden oldalnak legyen egyedi meta címe és leírása!</em></li>
                </ul>
            </div>
        </div>
        <div class="form-group">
            <label for="meta_title">Meta cím
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A böngésző fülön és a Google találatokban megjelenő cím. Ajánlott: 50–60 karakter. Ha üres, a rendszer az oldal címéből generálja.</span></span>
                <small style="font-weight:normal;color:#94A3B8">(ajánlott: max. 60 karakter)</small>
            </label>
            <input type="text" id="meta_title" name="meta_title" value="<?= $h($page['meta_title']) ?>">
        </div>
        <div class="form-group">
            <label for="meta_description">Meta leírás
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Ez a szöveg jelenik meg a Google találatokban a cím alatt. Írjon vonzó leírást, ami kattintásra ösztönöz! Ajánlott: 120–160 karakter.</span></span>
                <small style="font-weight:normal;color:#94A3B8">(ajánlott: max. 160 karakter)</small>
            </label>
            <textarea id="meta_description" name="meta_description" rows="2"><?= $h($page['meta_description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="meta_keywords">Kulcsszavak
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Vesszővel elválasztott kulcsszavak, amelyek az oldal tartalmát jellemzik. Pl. „parkoló rendszer, sorompó, beléptető".</span></span>
                <small style="font-weight:normal;color:#94A3B8">(vesszővel elválasztva)</small>
            </label>
            <input type="text" id="meta_keywords" name="meta_keywords" value="<?= $h($page['meta_keywords']) ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="og_title">OG cím
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Open Graph cím — ez jelenik meg, ha az oldalt Facebookon vagy LinkedIn-en megosztják. Ha üresen hagyja, a meta cím lesz használva.</span></span>
                    <small style="font-weight:normal;color:#94A3B8">(közösségi média)</small>
                </label>
                <input type="text" id="og_title" name="og_title" value="<?= $h($page['og_title']) ?>"
                       placeholder="Ha üres, a meta cím lesz használva">
            </div>
            <div class="form-group">
                <label for="og_description">OG leírás
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Open Graph leírás — a közösségi média megosztásnál a cím alatt jelenik meg. Ha üresen hagyja, a meta leírás lesz használva.</span></span>
                    <small style="font-weight:normal;color:#94A3B8">(közösségi média)</small>
                </label>
                <input type="text" id="og_description" name="og_description" value="<?= $h($page['og_description']) ?>"
                       placeholder="Ha üres, a meta leírás lesz használva">
            </div>
        </div>
    </fieldset>

    <h2>Szekciók</h2>
    <div class="help-box" style="margin-bottom:1rem;">
        <strong>🧱 A szekciók az oldal építőelemei.</strong> Minden oldal szekciókat tartalmaz, amelyek felülről lefelé jelennek meg a weboldalon. A ▲/▼ gombokkal rendezheti a sorrendet, a ✕ gombbal törölheti. Alul új szekciót adhat hozzá.
    </div>

    <?php foreach ($sections as $i => $sec): ?>
        <?php $c = json_decode($sec['content'], true); ?>
        <fieldset class="section-fieldset" data-section-id="<?= $sec['id'] ?>">
            <legend>
                <?= $h($sectionLabels[$sec['type']] ?? ucfirst($sec['type'])) ?>
                <span class="section-actions">
                    <?php if ($i > 0): ?>
                        <button type="submit" name="move_section" value="<?= $sec['id'] ?>" class="btn btn-xs"
                                onclick="this.form.direction.value='up'">▲</button>
                    <?php endif; ?>
                    <?php if ($i < count($sections) - 1): ?>
                        <button type="submit" name="move_section" value="<?= $sec['id'] ?>" class="btn btn-xs"
                                onclick="this.form.direction.value='down'">▼</button>
                    <?php endif; ?>
                    <button type="submit" name="delete_section" value="<?= $sec['id'] ?>"
                            class="btn btn-xs btn-danger"
                            onclick="return confirm('Szekció törlése?')">✕</button>
                </span>
            </legend>
            <?php if (!empty($sectionDescriptions[$sec['type']])): ?>
                <p class="section-type-desc"><?= $h($sectionDescriptions[$sec['type']]) ?></p>
            <?php endif; ?>

            <?php
            $secId = $sec['id'];
            switch ($sec['type']):
                case 'hero': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Alcím</label>
                        <input type="text" name="sections[<?= $secId ?>][subtitle]" value="<?= $h($c['subtitle'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Háttérkép URL</label>
                        <div class="input-with-browse">
                            <input type="text" id="hero_image_<?= $secId ?>" name="sections[<?= $secId ?>][image]" value="<?= $h($c['image'] ?? '') ?>">
                            <button type="button" class="btn btn-sm browse-media-btn" data-target="hero_image_<?= $secId ?>">Tallózás</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>CTA gomb szöveg</label>
                            <input type="text" name="sections[<?= $secId ?>][cta_text]" value="<?= $h($c['cta_text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>CTA gomb URL</label>
                            <input type="text" id="hero_cta_<?= $secId ?>" name="sections[<?= $secId ?>][cta_url]" value="<?= $h($c['cta_url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'hero_cta_' . $secId, $h); ?>
                        </div>
                    </div>
                    <?php break;

                case 'text': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Tartalom</label>
                        <div class="quill-editor" id="quill_<?= $secId ?>"><?= $c['body'] ?? '' ?></div>
                        <textarea name="sections[<?= $secId ?>][body]" class="quill-hidden" id="quill_hidden_<?= $secId ?>" style="display:none;"><?= $h($c['body'] ?? '') ?></textarea>
                    </div>
                    <?php break;

                case 'image_text': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Tartalom</label>
                        <div class="quill-editor" id="quill_<?= $secId ?>"><?= $c['body'] ?? '' ?></div>
                        <textarea name="sections[<?= $secId ?>][body]" class="quill-hidden" id="quill_hidden_<?= $secId ?>" style="display:none;"><?= $h($c['body'] ?? '') ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Kép URL</label>
                            <div class="input-with-browse">
                                <input type="text" id="imgtext_image_<?= $secId ?>" name="sections[<?= $secId ?>][image]" value="<?= $h($c['image'] ?? '') ?>">
                                <button type="button" class="btn btn-sm browse-media-btn" data-target="imgtext_image_<?= $secId ?>">Tallózás</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kép alt szöveg
                                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Írja le röviden, mit ábrázol a kép. Fontos a Google képkereséshez és a látássérült felhasználók számára (akadálymentesség).</span></span>
                            </label>
                            <input type="text" name="sections[<?= $secId ?>][image_alt]" value="<?= $h($c['image_alt'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Kép pozíció</label>
                            <select name="sections[<?= $secId ?>][image_position]">
                                <option value="right" <?= ($c['image_position'] ?? '') === 'right' ? 'selected' : '' ?>>Jobbra</option>
                                <option value="left" <?= ($c['image_position'] ?? '') === 'left' ? 'selected' : '' ?>>Balra</option>
                            </select>
                        </div>
                    </div>
                    <?php break;

                case 'cta': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Alcím</label>
                        <input type="text" name="sections[<?= $secId ?>][subtitle]" value="<?= $h($c['subtitle'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Gomb szöveg</label>
                            <input type="text" name="sections[<?= $secId ?>][button_text]" value="<?= $h($c['button_text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Gomb URL</label>
                            <input type="text" id="cta_btn_<?= $secId ?>" name="sections[<?= $secId ?>][button_url]" value="<?= $h($c['button_url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'cta_btn_' . $secId, $h); ?>
                        </div>
                        <div class="form-group">
                            <label>Háttérszín</label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                    </div>
                    <?php break;

                case 'cards': ?>
                    <div class="form-group">
                        <label>Szekció címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="cards-editor" data-section-id="<?= $secId ?>">
                        <?php foreach (($c['cards'] ?? []) as $ci => $card): ?>
                            <div class="card-editor-item" data-card-index="<?= $ci ?>">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Ikon</label>
                                        <input type="text" name="sections[<?= $secId ?>][cards][<?= $ci ?>][icon]"
                                               value="<?= $h($card['icon'] ?? '') ?>" style="width:60px">
                                    </div>
                                    <div class="form-group">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][cards][<?= $ci ?>][title]"
                                               value="<?= $h($card['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" id="card_link_<?= $secId ?>_<?= $ci ?>" name="sections[<?= $secId ?>][cards][<?= $ci ?>][link]"
                                               value="<?= $h($card['link'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'card_link_' . $secId . '_' . $ci, $h); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Leírás</label>
                                    <textarea name="sections[<?= $secId ?>][cards][<?= $ci ?>][description]"
                                              rows="2"><?= $h($card['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="card-editor-item" data-card-index="__IDX__">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Ikon</label>
                                        <input type="text" name="sections[<?= $secId ?>][cards][__IDX__][icon]" value="" style="width:60px">
                                    </div>
                                    <div class="form-group">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][cards][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" id="card_link_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][cards][__IDX__][link]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'card_link_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Leírás</label>
                                    <textarea name="sections[<?= $secId ?>][cards][__IDX__][description]" rows="2"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Kártya hozzáadása</button>
                    </div>
                    <?php break;

                case 'gallery': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Galéria képek:
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Minden képnél adjon meg alt szöveget — ez fontos a Google képkereséshez és az akadálymentességhez!</span></span>
                        </p>
                        <?php foreach (($c['images'] ?? []) as $gi => $gImg): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Kép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="gal_img_<?= $secId ?>_<?= $gi ?>" name="sections[<?= $secId ?>][images][<?= $gi ?>][url]" value="<?= $h($gImg['url'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="gal_img_<?= $secId ?>_<?= $gi ?>">Tallózás</button>
                                        </div>
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Alt szöveg (SEO)</label>
                                        <input type="text" name="sections[<?= $secId ?>][images][<?= $gi ?>][alt]" value="<?= $h($gImg['alt'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Kép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="gal_img_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][images][__IDX__][url]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="gal_img_<?= $secId ?>___IDX__">Tallózás</button>
                                        </div>
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Alt szöveg (SEO)</label>
                                        <input type="text" name="sections[<?= $secId ?>][images][__IDX__][alt]" value="">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Kép hozzáadása</button>
                    </div>
                    <?php break;

                case 'ticker': ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Háttérszín</label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                        <div class="form-group">
                            <label>Szöveg szín</label>
                            <input type="color" name="sections[<?= $secId ?>][text_color]" value="<?= $h($c['text_color'] ?? '#FFFFFF') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sebesség (mp)</label>
                            <input type="number" name="sections[<?= $secId ?>][speed]" value="<?= (int)($c['speed'] ?? 30) ?>" min="5" max="120">
                        </div>
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Futó szalag elemei:</p>
                        <?php foreach (($c['items'] ?? []) as $ti => $tItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Szöveg</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $ti ?>][text]"
                                               value="<?= $h($tItem['text'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:1">
                                        <label>Link (opcionális)</label>
                                        <input type="text" id="ticker_link_<?= $secId ?>_<?= $ti ?>" name="sections[<?= $secId ?>][items][<?= $ti ?>][link]"
                                               value="<?= $h($tItem['link'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'ticker_link_' . $secId . '_' . $ti, $h); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Szöveg</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][text]" value="">
                                    </div>
                                    <div class="form-group" style="flex:1">
                                        <label>Link (opcionális)</label>
                                        <input type="text" id="ticker_link_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][link]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'ticker_link_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Elem hozzáadása</button>
                    </div>
                    <?php break;

                case 'accordion': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Kérdés-válasz elemek:</p>
                        <?php foreach (($c['items'] ?? []) as $ai => $aItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-group">
                                    <label>Kérdés</label>
                                    <input type="text" name="sections[<?= $secId ?>][items][<?= $ai ?>][question]"
                                           value="<?= $h($aItem['question'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Válasz (HTML)</label>
                                    <textarea name="sections[<?= $secId ?>][items][<?= $ai ?>][answer]"
                                              rows="3"><?= $h($aItem['answer'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-group">
                                    <label>Kérdés</label>
                                    <input type="text" name="sections[<?= $secId ?>][items][__IDX__][question]" value="">
                                </div>
                                <div class="form-group">
                                    <label>Válasz (HTML)</label>
                                    <textarea name="sections[<?= $secId ?>][items][__IDX__][answer]" rows="3"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Kérdés hozzáadása</button>
                    </div>
                    <?php break;

                case 'video': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:2">
                            <label>Videó URL
                                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Másolja be a YouTube vagy Vimeo videó teljes URL-jét. A rendszer automatikusan beágyazza. Pl: https://youtube.com/watch?v=abc123</span></span>
                            </label>
                            <input type="text" name="sections[<?= $secId ?>][url]"
                                   value="<?= $h($c['url'] ?? '') ?>"
                                   placeholder="https://youtube.com/watch?v=... vagy https://vimeo.com/...">
                        </div>
                        <div class="form-group">
                            <label>Típus</label>
                            <select name="sections[<?= $secId ?>][type]">
                                <option value="youtube" <?= ($c['type'] ?? '') === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                                <option value="vimeo" <?= ($c['type'] ?? '') === 'vimeo' ? 'selected' : '' ?>>Vimeo</option>
                            </select>
                        </div>
                    </div>
                    <?php break;

                case 'divider': ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Stílus</label>
                            <select name="sections[<?= $secId ?>][style]">
                                <option value="line" <?= ($c['style'] ?? '') === 'line' ? 'selected' : '' ?>>Vonal</option>
                                <option value="dots" <?= ($c['style'] ?? '') === 'dots' ? 'selected' : '' ?>>Pontok</option>
                                <option value="space" <?= ($c['style'] ?? '') === 'space' ? 'selected' : '' ?>>Üres tér</option>
                                <option value="wave" <?= ($c['style'] ?? '') === 'wave' ? 'selected' : '' ?>>Hullám</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Méret</label>
                            <select name="sections[<?= $secId ?>][spacing]">
                                <option value="compact" <?= ($c['spacing'] ?? '') === 'compact' ? 'selected' : '' ?>>Kompakt</option>
                                <option value="normal" <?= ($c['spacing'] ?? '') === 'normal' ? 'selected' : '' ?>>Normál</option>
                                <option value="wide" <?= ($c['spacing'] ?? '') === 'wide' ? 'selected' : '' ?>>Széles</option>
                            </select>
                        </div>
                    </div>
                    <?php break;

                case 'two_columns': ?>
                    <div class="form-group">
                        <label>Címsor (opcionális)</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Bal oszlop</label>
                            <div class="quill-editor" id="quill_left_<?= $secId ?>"><?= $c['left_body'] ?? '' ?></div>
                            <textarea name="sections[<?= $secId ?>][left_body]" class="quill-hidden" id="quill_hidden_left_<?= $secId ?>" style="display:none;"><?= $h($c['left_body'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Jobb oszlop</label>
                            <div class="quill-editor" id="quill_right_<?= $secId ?>"><?= $c['right_body'] ?? '' ?></div>
                            <textarea name="sections[<?= $secId ?>][right_body]" class="quill-hidden" id="quill_hidden_right_<?= $secId ?>" style="display:none;"><?= $h($c['right_body'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <?php break;

                case 'testimonials': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Vélemények:</p>
                        <?php foreach (($c['items'] ?? []) as $ti => $tItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Név</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $ti ?>][name]"
                                               value="<?= $h($tItem['name'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Pozíció/Titulus</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $ti ?>][role]"
                                               value="<?= $h($tItem['role'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Kép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="testi_img_<?= $secId ?>_<?= $ti ?>" name="sections[<?= $secId ?>][items][<?= $ti ?>][image]"
                                                   value="<?= $h($tItem['image'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="testi_img_<?= $secId ?>_<?= $ti ?>">Tallózás</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Vélemény szövege</label>
                                    <textarea name="sections[<?= $secId ?>][items][<?= $ti ?>][text]"
                                              rows="2"><?= $h($tItem['text'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Név</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][name]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Pozíció/Titulus</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][role]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Kép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="testi_img_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][image]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="testi_img_<?= $secId ?>___IDX__">Tallózás</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Vélemény szövege</label>
                                    <textarea name="sections[<?= $secId ?>][items][__IDX__][text]" rows="2"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Vélemény hozzáadása</button>
                    </div>
                    <?php break;

                case 'stats': ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Címsor (opcionális)</label>
                            <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Háttérszín</label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Számok/statisztikák:</p>
                        <?php foreach (($c['items'] ?? []) as $si => $sItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Szám</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $si ?>][number]"
                                               value="<?= $h($sItem['number'] ?? '') ?>" placeholder="100+">
                                    </div>
                                    <div class="form-group">
                                        <label>Címke</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $si ?>][label]"
                                               value="<?= $h($sItem['label'] ?? '') ?>" placeholder="Elégedett ügyfél">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Szám</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][number]" value="" placeholder="100+">
                                    </div>
                                    <div class="form-group">
                                        <label>Címke</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][label]" value="" placeholder="Elégedett ügyfél">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Szám hozzáadása</button>
                    </div>
                    <?php break;

                case 'page_list': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Oldal típus szűrő</label>
                            <select name="sections[<?= $secId ?>][page_type]">
                                <option value="article" <?= ($c['page_type'] ?? '') === 'article' ? 'selected' : '' ?>>Cikkek</option>
                                <option value="page" <?= ($c['page_type'] ?? '') === 'page' ? 'selected' : '' ?>>Oldalak</option>
                                <option value="all" <?= ($c['page_type'] ?? '') === 'all' ? 'selected' : '' ?>>Minden</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Darabszám</label>
                            <input type="number" name="sections[<?= $secId ?>][count]" value="<?= (int)($c['count'] ?? 10) ?>" min="1" max="100">
                        </div>
                    </div>
                    <?php break;

                case 'map': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Google Maps beágyazási URL
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Nyissa meg a Google Maps-et → keresse meg a helyszínt → kattintson a „Megosztás" gombra → válassza a „Térkép beágyazása" fület → másolja ki az src="..." közötti URL-t.</span></span>
                        </label>
                        <input type="text" name="sections[<?= $secId ?>][embed_url]"
                               value="<?= $h($c['embed_url'] ?? '') ?>"
                               placeholder="https://www.google.com/maps/embed?pb=...">
                        <div class="field-hint">Google Maps → Megosztás → Térkép beágyazása → másolja ki az iframe src URL-jét (https://www.google.com/maps/embed?pb=...)</div>
                    </div>
                    <div class="form-group">
                        <label>Magasság (px)</label>
                        <input type="number" name="sections[<?= $secId ?>][height]" value="<?= (int)($c['height'] ?? 400) ?>" min="200" max="800">
                    </div>
                    <?php break;

                case 'contact_form': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Sikeres küldés üzenet
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Ez az üzenet jelenik meg, miután a látogató sikeresen elküldte az űrlapot. Legyen barátságos és tájékoztassa, hogy mire számíthat.</span></span>
                        </label>
                        <input type="text" name="sections[<?= $secId ?>][success_message]"
                               value="<?= $h($c['success_message'] ?? '') ?>">
                        <div class="field-hint">A beérkezett üzenetek az admin „Üzenetek" menüben olvashatók.</div>
                    </div>
                    <?php break;

                case 'keywords_cloud': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Maximum kulcsszavak száma</label>
                        <input type="number" name="sections[<?= $secId ?>][count]" value="<?= (int)($c['count'] ?? 50) ?>" min="10" max="200">
                    </div>
                    <p class="form-help">Ez a szekció automatikusan összegyűjti az összes oldal kulcsszavait és megjeleníti a leggyakoribbakat.</p>
                    <?php break;

                case 'hero_slideshow': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Alcím</label>
                        <input type="text" name="sections[<?= $secId ?>][subtitle]" value="<?= $h($c['subtitle'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>CTA gomb szöveg</label>
                            <input type="text" name="sections[<?= $secId ?>][cta_text]" value="<?= $h($c['cta_text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>CTA gomb URL</label>
                            <input type="text" id="hs_cta_<?= $secId ?>" name="sections[<?= $secId ?>][cta_url]" value="<?= $h($c['cta_url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'hs_cta_' . $secId, $h); ?>
                        </div>
                        <div class="form-group">
                            <label>Képváltás (mp)</label>
                            <input type="number" name="sections[<?= $secId ?>][interval]" value="<?= (int)($c['interval'] ?? 4) ?>" min="1" max="15">
                        </div>
                    </div>
                    <div class="help-box" style="margin:1rem 0 0.5rem">
                        <strong>📸 Képek:</strong> A diavetítés a Média oldalon „kiemelt" jelölésű képeket használja automatikusan. <a href="/admin/media.php">Média kezelése →</a>
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Navigációs dobozok a hero képen (opcionális):</p>
                        <?php foreach (($c['overlay_boxes'] ?? []) as $bi => $box): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][overlay_boxes][<?= $bi ?>][title]" value="<?= $h($box['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Link URL</label>
                                        <input type="text" id="hs_box_<?= $secId ?>_<?= $bi ?>" name="sections[<?= $secId ?>][overlay_boxes][<?= $bi ?>][url]" value="<?= $h($box['url'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'hs_box_' . $secId . '_' . $bi, $h); ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Méret</label>
                                        <select name="sections[<?= $secId ?>][overlay_boxes][<?= $bi ?>][size]">
                                            <option value="large" <?= ($box['size'] ?? '') === 'large' ? 'selected' : '' ?>>Nagy</option>
                                            <option value="small" <?= ($box['size'] ?? '') === 'small' ? 'selected' : '' ?>>Kicsi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][overlay_boxes][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Link URL</label>
                                        <input type="text" id="hs_box_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][overlay_boxes][__IDX__][url]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'hs_box_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Méret</label>
                                        <select name="sections[<?= $secId ?>][overlay_boxes][__IDX__][size]">
                                            <option value="large">Nagy</option>
                                            <option value="small">Kicsi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Doboz hozzáadása</button>
                    </div>
                    <?php break;

                case 'product_grid': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Oszlopok száma</label>
                        <input type="number" name="sections[<?= $secId ?>][columns]" value="<?= (int)($c['columns'] ?? 5) ?>" min="2" max="6">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Termékek/kiegészítők:</p>
                        <?php foreach (($c['items'] ?? []) as $pi => $pItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $pi ?>][title]" value="<?= $h($pItem['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Kép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="pg_img_<?= $secId ?>_<?= $pi ?>" name="sections[<?= $secId ?>][items][<?= $pi ?>][image]" value="<?= $h($pItem['image'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="pg_img_<?= $secId ?>_<?= $pi ?>">Tallózás</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Link URL</label>
                                        <input type="text" id="pg_url_<?= $secId ?>_<?= $pi ?>" name="sections[<?= $secId ?>][items][<?= $pi ?>][url]" value="<?= $h($pItem['url'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'pg_url_' . $secId . '_' . $pi, $h); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Kép alt szöveg</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $pi ?>][image_alt]" value="<?= $h($pItem['image_alt'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Rövid leírás (hover)</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $pi ?>][short_desc]" value="<?= $h($pItem['short_desc'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Kép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="pg_img_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][image]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="pg_img_<?= $secId ?>___IDX__">Tallózás</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Link URL</label>
                                        <input type="text" id="pg_url_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][url]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'pg_url_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Kép alt szöveg</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][image_alt]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Rövid leírás (hover)</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][short_desc]" value="">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Termék hozzáadása</button>
                    </div>
                    <?php break;

                case 'seo_hidden': ?>
                    <div class="form-group">
                        <label>Gomb szöveg (lenyitó gomb felirata)</label>
                        <input type="text" name="sections[<?= $secId ?>][button_text]" value="<?= $h($c['button_text'] ?? 'Tovább olvasom...') ?>">
                    </div>
                    <div class="form-group">
                        <label>Tartalom (SEO szöveg)
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Ez a szöveg a Google számára látható, de a weboldalon rejtve van. Ideális hirdetési szövegek, kulcsszavak elhelyezésére.</span></span>
                        </label>
                        <div class="quill-editor" id="quill_<?= $secId ?>"><?= $c['body'] ?? '' ?></div>
                        <textarea name="sections[<?= $secId ?>][body]" class="quill-hidden" id="quill_hidden_<?= $secId ?>" style="display:none;"><?= $h($c['body'] ?? '') ?></textarea>
                    </div>
                    <?php break;

                case 'link_banner': ?>
                    <div class="form-row">
                        <div class="form-group" style="flex:3">
                            <label>Szöveg</label>
                            <input type="text" name="sections[<?= $secId ?>][text]" value="<?= $h($c['text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Ikon (emoji)</label>
                            <input type="text" name="sections[<?= $secId ?>][icon]" value="<?= $h($c['icon'] ?? '📖') ?>" style="width:60px">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:2">
                            <label>Link URL</label>
                            <input type="text" id="lb_url_<?= $secId ?>" name="sections[<?= $secId ?>][url]" value="<?= $h($c['url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'lb_url_' . $secId, $h); ?>
                        </div>
                        <div class="form-group">
                            <label>Háttérszín</label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                    </div>
                    <?php break;

                case 'reference_gallery': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Projektek:
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Minden projekt: cím, borítókép, alt szöveg, és opcionális további képek.</span></span>
                        </p>
                        <?php foreach (($c['projects'] ?? []) as $pri => $proj): ?>
                            <div class="repeater-item" data-card-index="<?= $pri ?>" style="border-left:3px solid var(--admin-primary, #0067FF);padding-left:0.75rem;margin-bottom:1rem;">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Projekt címe</label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][<?= $pri ?>][title]" value="<?= $h($proj['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Borítókép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="ref_cover_<?= $secId ?>_<?= $pri ?>" name="sections[<?= $secId ?>][projects][<?= $pri ?>][cover_image]" value="<?= $h($proj['cover_image'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_cover_<?= $secId ?>_<?= $pri ?>">Tallózás</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Borító alt szöveg</label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][<?= $pri ?>][cover_alt]" value="<?= $h($proj['cover_alt'] ?? '') ?>">
                                    </div>
                                </div>
                                <p class="form-help" style="margin-top:0.5rem;">További képek (projekt részletei):</p>
                                <div class="ref-images-container">
                                    <?php foreach (($proj['images'] ?? []) as $rii => $rImg): ?>
                                        <div class="form-row ref-image-row" style="margin-bottom:0.35rem;">
                                            <div class="form-group" style="flex:2">
                                                <div class="input-with-browse">
                                                    <input type="text" id="ref_img_<?= $secId ?>_<?= $pri ?>_<?= $rii ?>" name="sections[<?= $secId ?>][projects][<?= $pri ?>][images][<?= $rii ?>][url]" value="<?= $h($rImg['url'] ?? '') ?>">
                                                    <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_img_<?= $secId ?>_<?= $pri ?>_<?= $rii ?>">Tallózás</button>
                                                </div>
                                            </div>
                                            <div class="form-group" style="flex:2">
                                                <input type="text" name="sections[<?= $secId ?>][projects][<?= $pri ?>][images][<?= $rii ?>][alt]" value="<?= $h($rImg['alt'] ?? '') ?>" placeholder="Alt szöveg">
                                            </div>
                                            <button type="button" class="ref-image-remove repeater-remove" title="Kép törlése">✕</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <template class="ref-image-template">
                                    <div class="form-row ref-image-row" style="margin-bottom:0.35rem;">
                                        <div class="form-group" style="flex:2">
                                            <div class="input-with-browse">
                                                <input type="text" id="ref_img_<?= $secId ?>___PIDX_____IIDX__" name="sections[<?= $secId ?>][projects][__PIDX__][images][__IIDX__][url]" value="">
                                                <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_img_<?= $secId ?>___PIDX_____IIDX__">Tallózás</button>
                                            </div>
                                        </div>
                                        <div class="form-group" style="flex:2">
                                            <input type="text" name="sections[<?= $secId ?>][projects][__PIDX__][images][__IIDX__][alt]" value="" placeholder="Alt szöveg">
                                        </div>
                                        <button type="button" class="ref-image-remove repeater-remove" title="Kép törlése">✕</button>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-xs ref-image-add-btn" style="margin-top:0.35rem;">+ Kép</button>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item" data-card-index="__IDX__" style="border-left:3px solid var(--admin-primary, #0067FF);padding-left:0.75rem;margin-bottom:1rem;">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Projekt címe</label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Borítókép URL</label>
                                        <div class="input-with-browse">
                                            <input type="text" id="ref_cover_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][projects][__IDX__][cover_image]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_cover_<?= $secId ?>___IDX__">Tallózás</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Borító alt szöveg</label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][__IDX__][cover_alt]" value="">
                                    </div>
                                </div>
                                <p class="form-help" style="margin-top:0.5rem;">További képek (projekt részletei):</p>
                                <div class="ref-images-container"></div>
                                <template class="ref-image-template">
                                    <div class="form-row ref-image-row" style="margin-bottom:0.35rem;">
                                        <div class="form-group" style="flex:2">
                                            <div class="input-with-browse">
                                                <input type="text" id="ref_img_<?= $secId ?>___IDX_____IIDX__" name="sections[<?= $secId ?>][projects][__IDX__][images][__IIDX__][url]" value="">
                                                <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_img_<?= $secId ?>___IDX_____IIDX__">Tallózás</button>
                                            </div>
                                        </div>
                                        <div class="form-group" style="flex:2">
                                            <input type="text" name="sections[<?= $secId ?>][projects][__IDX__][images][__IIDX__][alt]" value="" placeholder="Alt szöveg">
                                        </div>
                                        <button type="button" class="ref-image-remove repeater-remove" title="Kép törlése">✕</button>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-xs ref-image-add-btn" style="margin-top:0.35rem;">+ Kép</button>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Projekt hozzáadása</button>
                    </div>
                    <?php break;

                case 'sitemap': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? 'Oldaltérkép') ?>">
                    </div>
                    <p class="form-help">Ez a szekció automatikusan generálja az oldaltérképet a menüszerkezet és a publikált oldalak alapján. Nincs szükség manuális konfigurációra.</p>
                    <?php break;

                case 'tudasmorzsak': ?>
                    <div class="form-group">
                        <label>Címsor</label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? 'Tudásmorzsák') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help">Tudásmorzsák (kis tudásdobozok, lexikon jellegű):</p>
                        <?php foreach (($c['items'] ?? []) as $tmi => $tmItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $tmi ?>][title]" value="<?= $h($tmItem['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Link URL (opcionális)</label>
                                        <input type="text" id="tm_url_<?= $secId ?>_<?= $tmi ?>" name="sections[<?= $secId ?>][items][<?= $tmi ?>][url]" value="<?= $h($tmItem['url'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'tm_url_' . $secId . '_' . $tmi, $h); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Rövid leírás</label>
                                    <textarea name="sections[<?= $secId ?>][items][<?= $tmi ?>][description]" rows="2"><?= $h($tmItem['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label>Cím</label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label>Link URL (opcionális)</label>
                                        <input type="text" id="tm_url_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][url]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'tm_url_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Rövid leírás</label>
                                    <textarea name="sections[<?= $secId ?>][items][__IDX__][description]" rows="2"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ Morzsa hozzáadása</button>
                    </div>
                    <?php break;

                default: ?>
                    <div class="form-group">
                        <label>Tartalom (JSON)</label>
                        <textarea name="sections[<?= $secId ?>][_raw_json]"
                                  rows="6"><?= $h(json_encode($c, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) ?></textarea>
                    </div>
            <?php endswitch; ?>
        </fieldset>
    <?php endforeach; ?>

    <input type="hidden" name="direction" value="">
    <div class="form-actions">
        <button type="submit" name="save_page" value="1" class="btn btn-primary">Mentés</button>
        <a href="/admin/pages.php" class="btn btn-secondary">Vissza</a>
    </div>
</form>

<hr>

<form method="POST" class="admin-form inline-form">
    <?= csrfField() ?>
    <h3>Új szekció hozzáadása
        <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Válasszon szekciótípust és kattintson a „Hozzáadás" gombra. Az új szekció az oldal végére kerül — utána a ▲/▼ gombokkal rendezheti.</span></span>
    </h3>
    <div class="form-row">
        <div class="form-group">
            <select name="new_section_type">
                <?php foreach ($sectionLabels as $typeKey => $typeLabel): ?>
                    <option value="<?= $h($typeKey) ?>" title="<?= $h($sectionDescriptions[$typeKey] ?? '') ?>"><?= $h($typeLabel) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="add_section" value="1" class="btn btn-primary">Hozzáadás</button>
    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
