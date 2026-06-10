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

// Transliterate accented characters (incl. Hungarian) for slug generation.
function transliterateHu(string $str): string {
    $map = ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','ő'=>'o','ú'=>'u','ü'=>'u','ű'=>'u',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ö'=>'o','Ő'=>'o','Ú'=>'u','Ü'=>'u','Ű'=>'u'];
    return strtr($str, $map);
}

// All available section types with default content (written to the DB
// when a new block is created; fully editable afterwards).
$sectionDefaults = [
    'hero'           => ['heading' => 'New hero', 'subtitle' => '', 'image' => '', 'cta_text' => '', 'cta_url' => ''],
    'text'           => ['heading' => 'New text', 'body' => '<p>Text...</p>'],
    'image_text'     => ['heading' => 'Image and text', 'body' => '<p>Text...</p>', 'image' => '', 'image_alt' => '', 'image_position' => 'right'],
    'gallery'        => ['heading' => 'Gallery', 'images' => []],
    'cta'            => ['heading' => 'CTA', 'subtitle' => '', 'button_text' => 'Learn more', 'button_url' => '/', 'background_color' => '#0067FF'],
    'cards'          => ['heading' => 'Cards', 'cards' => [['title' => 'Card', 'description' => 'Description', 'icon' => '⭐', 'link' => '']]],
    'ticker'         => ['items' => [['text' => 'News item...', 'link' => '']], 'speed' => 30, 'background_color' => '#0067FF', 'text_color' => '#FFFFFF'],
    'accordion'      => ['heading' => 'Frequently Asked Questions', 'items' => [['question' => 'Question?', 'answer' => '<p>Answer.</p>']]],
    'video'          => ['heading' => 'Video', 'url' => '', 'type' => 'youtube'],
    'divider'        => ['style' => 'line', 'spacing' => 'normal'],
    'two_columns'    => ['heading' => '', 'left_body' => '<p>Left column text...</p>', 'right_body' => '<p>Right column text...</p>'],
    'testimonials'   => ['heading' => 'Testimonials', 'items' => [['name' => 'Name', 'text' => 'Testimonial text...', 'role' => '', 'image' => '']]],
    'stats'          => ['heading' => '', 'background_color' => '#0067FF', 'items' => [['number' => '100+', 'label' => 'Clients']]],
    'page_list'      => ['heading' => 'Articles', 'page_type' => 'article', 'count' => 10],
    'map'            => ['heading' => 'Map', 'embed_url' => '', 'height' => '400'],
    'contact_form'   => ['heading' => 'Contact', 'success_message' => ''],
    'keywords_cloud' => ['heading' => 'Keywords', 'count' => 50],
    'hero_slideshow'    => ['heading' => 'Section CMS', 'subtitle' => '', 'cta_text' => '', 'cta_url' => '', 'interval' => 4, 'overlay_boxes' => []],
    'product_grid'      => ['heading' => 'Products', 'columns' => 5, 'items' => [['title' => 'Product', 'short_desc' => 'Description', 'image' => '', 'image_alt' => '', 'url' => '']]],
    'seo_hidden'        => ['button_text' => 'Read more...', 'body' => '<p>SEO text...</p>'],
    'link_banner'       => ['text' => 'Glossary', 'url' => '/glossary', 'icon' => '📖', 'background_color' => '#0067FF'],
    'reference_gallery' => ['heading' => 'Our references', 'projects' => [['title' => 'Project', 'cover_image' => '', 'cover_alt' => '', 'images' => []]]],
    'sitemap'            => ['heading' => 'Sitemap'],
    'tudasmorzsak'       => ['heading' => 'Knowledge bites', 'items' => [['title' => 'Term', 'description' => 'Short description...', 'url' => '']]],
];

// Section type labels (translation keys → resolved per locale)
$sectionLabels = [
    'hero'           => t('admin.section.hero_label'),
    'text'           => t('admin.section.text_label'),
    'image_text'     => t('admin.section.image_text_label'),
    'cards'          => t('admin.section.cards_label'),
    'cta'            => t('admin.section.cta_label'),
    'gallery'        => t('admin.section.gallery_label'),
    'ticker'         => t('admin.section.ticker_label'),
    'accordion'      => t('admin.section.accordion_label'),
    'video'          => t('admin.section.video_label'),
    'divider'        => t('admin.section.divider_label'),
    'two_columns'    => t('admin.section.two_columns_label'),
    'testimonials'   => t('admin.section.testimonials_label'),
    'stats'          => t('admin.section.stats_label'),
    'page_list'      => t('admin.section.page_list_label'),
    'map'            => t('admin.section.map_label'),
    'contact_form'   => t('admin.section.contact_form_label'),
    'keywords_cloud' => t('admin.section.keywords_cloud_label'),
    'hero_slideshow'    => t('admin.section.hero_slideshow_label'),
    'product_grid'      => t('admin.section.product_grid_label'),
    'seo_hidden'        => t('admin.section.seo_hidden_label'),
    'link_banner'       => t('admin.section.link_banner_label'),
    'reference_gallery' => t('admin.section.reference_gallery_label'),
    'sitemap'              => t('admin.section.sitemap_label'),
    'tudasmorzsak'         => t('admin.section.tudasmorzsak_label'),
];

// Section type descriptions (translation keys → resolved per locale)
$sectionDescriptions = [
    'hero'           => t('admin.section.hero_desc'),
    'text'           => t('admin.section.text_desc'),
    'image_text'     => t('admin.section.image_text_desc'),
    'cards'          => t('admin.section.cards_desc'),
    'cta'            => t('admin.section.cta_desc'),
    'gallery'        => t('admin.section.gallery_desc'),
    'ticker'         => t('admin.section.ticker_desc'),
    'accordion'      => t('admin.section.accordion_desc'),
    'video'          => t('admin.section.video_desc'),
    'divider'        => t('admin.section.divider_desc'),
    'two_columns'    => t('admin.section.two_columns_desc'),
    'testimonials'   => t('admin.section.testimonials_desc'),
    'stats'          => t('admin.section.stats_desc'),
    'page_list'      => t('admin.section.page_list_desc'),
    'map'            => t('admin.section.map_desc'),
    'contact_form'   => t('admin.section.contact_form_desc'),
    'keywords_cloud' => t('admin.section.keywords_cloud_desc'),
    'hero_slideshow'    => t('admin.section.hero_slideshow_desc'),
    'product_grid'      => t('admin.section.product_grid_desc'),
    'seo_hidden'        => t('admin.section.seo_hidden_desc'),
    'link_banner'       => t('admin.section.link_banner_desc'),
    'reference_gallery' => t('admin.section.reference_gallery_desc'),
    'sitemap'            => t('admin.section.sitemap_desc'),
    'tudasmorzsak'       => t('admin.section.tudasmorzsak_desc'),
];

// ─── Handle POST (save) ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Creating a new page from template
    if (!empty($_POST['create_page'])) {
        csrfVerify();
        $title    = trim($_POST['title'] ?? t('admin.pages.default_new_page_title'));
        $slug     = trim($_POST['slug'] ?? '');
        $template = $_POST['template'] ?? 'hero_two_text';

        if ($slug === '') {
            $slug = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', mb_strtolower(transliterateHu($title))));
        }
        // Sanitize slug
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'page-' . time();
        }

        // Check for duplicate slug
        $dupCheck = $pdo->prepare("SELECT id FROM pages WHERE slug = :slug");
        $dupCheck->execute(['slug' => $slug]);
        if ($dupCheck->fetch()) {
            $slug = $slug . '-' . time();
        }

        // Fetch site name for SEO defaults
        $siteNameRow = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name'")->fetch();
        $siteNameVal = $siteNameRow ? $siteNameRow['setting_value'] : 'Section CMS';

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
            'meta_desc'  => $title . ' – ' . $siteNameVal . '. ' . t('admin.pages.default_meta_desc'),
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
            $saveMetaTitle = $saveTitle . ' – ' . ($sn ?: 'Section CMS');
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
    <h1><?= te('admin.pages.new_page_title') ?></h1>
    <div class="help-box">
        <strong>📄 <?= te('admin.pages.new_help') ?></strong> <?= te('admin.pages.new_help_body') ?>
    </div>
    <form method="POST" class="admin-form">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="title"><?= te('admin.pages.field_page_title') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_new_title') ?></span></span>
            </label>
            <input type="text" id="title" name="title" required placeholder="<?= $h(t('admin.pages.placeholder_new_title')) ?>">
        </div>
        <div class="form-group">
            <label for="slug"><?= te('admin.pages.field_slug_url') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_new_slug') ?></span></span>
            </label>
            <input type="text" id="slug" name="slug" placeholder="<?= $h(t('admin.pages.placeholder_new_slug')) ?>">
            <div class="field-hint"><?= te('admin.pages.hint_page_url') ?> example.com/<strong><?= te('common.slug') ?></strong></div>
        </div>
        <div class="form-group">
            <label for="template"><?= te('admin.pages.field_template') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_template') ?></span></span>
            </label>
            <select id="template" name="template">
                <?php foreach ($templates as $key => $tpl): ?>
                    <option value="<?= $h($key) ?>"><?= $h(t($tpl['name'])) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="create_page" value="1" class="btn btn-primary"><?= te('common.create') ?></button>
        <a href="/admin/pages.php" class="btn btn-secondary"><?= te('common.cancel') ?></a>
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
        <option value="">— <?= $h(t('admin.pages.select_page')) ?> —</option>
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
        <option value="">— <?= $h(t('admin.pages.select_page')) ?> —</option>
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
    echo '<button type="button" class="repeater-remove" title="' . htmlspecialchars(t('admin.pages.remove_item'), ENT_QUOTES, 'UTF-8') . '">✕</button>';
}

require __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $msgs = ['saved' => t('admin.pages.msg_saved'), 'created' => t('admin.pages.msg_created')];
        echo $h($msgs[$_GET['msg']] ?? t('admin.pages.msg_done'));
        ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <h1><?= $h(t('admin.pages.edit_heading', ['title' => $page['title']])) ?></h1>
    <a href="/<?= $page['slug'] === 'home' ? '' : $h($page['slug']) ?>"
       target="_blank" class="btn btn-secondary"><?= te('common.view') ?> ↗</a>
</div>

<form method="POST" class="admin-form">
    <?= csrfField() ?>
    <fieldset>
        <legend><?= te('admin.pages.legend_page_data') ?></legend>
        <div class="form-row">
            <div class="form-group">
                <label for="title"><?= te('common.title') ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_title') ?></span></span>
                </label>
                <input type="text" id="title" name="title" value="<?= $h($page['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="slug"><?= te('common.slug') ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_slug') ?></span></span>
                </label>
                <input type="text" id="slug" name="slug" value="<?= $h($page['slug']) ?>"
                    <?= $page['slug'] === 'home' ? 'readonly' : '' ?>>
                <small class="form-help"><?= te('admin.pages.slug_help') ?> example.com/<strong><?= te('common.slug') ?></strong></small>
            </div>
            <div class="form-group">
                <label for="status"><?= te('common.status') ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_status') ?></span></span>
                </label>
                <select id="status" name="status">
                    <option value="draft" <?= $page['status'] === 'draft' ? 'selected' : '' ?>><?= te('common.draft') ?></option>
                    <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?>><?= te('common.published') ?></option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="page_type"><?= te('common.type') ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_type') ?></span></span>
                </label>
                <select id="page_type" name="page_type">
                    <option value="page" <?= ($page['page_type'] ?? 'page') === 'page' ? 'selected' : '' ?>><?= te('admin.pages.page') ?></option>
                    <option value="article" <?= ($page['page_type'] ?? '') === 'article' ? 'selected' : '' ?>><?= te('admin.pages.article') ?></option>
                </select>
            </div>
            <div class="form-group">
                <label for="featured_image"><?= te('admin.pages.field_featured_image') ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_featured_image') ?></span></span>
                </label>
                <div class="input-with-browse">
                    <input type="text" id="featured_image" name="featured_image"
                           value="<?= $h($page['featured_image'] ?? '') ?>" placeholder="/assets/uploads/image.jpg">
                    <button type="button" class="btn btn-sm browse-media-btn" data-target="featured_image"><?= te('common.browse') ?></button>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= te('admin.pages.legend_seo') ?>
            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_seo') ?></span></span>
        </legend>
        <div class="help-box" style="margin-bottom:1rem;">
            <strong>🔍 <?= te('admin.pages.seo_why_title') ?></strong> <?= te('admin.pages.seo_why_body') ?>
            <button type="button" class="help-toggle" data-target="seoHelp" aria-expanded="false"><span class="help-toggle-icon">▸</span> <?= te('admin.pages.seo_help_more') ?></button>
            <div class="help-collapsible" id="seoHelp">
                <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
                    <li><strong><?= te('admin.pages.field_meta_title') ?>:</strong> <?= te('admin.pages.seo_help_meta_title') ?></li>
                    <li><strong><?= te('admin.pages.field_meta_desc') ?>:</strong> <?= te('admin.pages.seo_help_meta_desc') ?></li>
                    <li><strong><?= te('admin.pages.field_keywords') ?>:</strong> <?= te('admin.pages.seo_help_keywords') ?></li>
                    <li><strong><?= te('admin.pages.seo_help_og_label') ?>:</strong> <?= te('admin.pages.seo_help_og') ?></li>
                    <li>💡 <em><?= te('admin.pages.seo_help_tip') ?></em></li>
                </ul>
            </div>
        </div>
        <div class="form-group">
            <label for="meta_title"><?= te('admin.pages.field_meta_title') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_meta_title') ?></span></span>
                <small style="font-weight:normal;color:#94A3B8"><?= te('admin.pages.hint_max_60') ?></small>
            </label>
            <input type="text" id="meta_title" name="meta_title" value="<?= $h($page['meta_title']) ?>">
        </div>
        <div class="form-group">
            <label for="meta_description"><?= te('admin.pages.field_meta_desc') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_meta_desc') ?></span></span>
                <small style="font-weight:normal;color:#94A3B8"><?= te('admin.pages.hint_max_160') ?></small>
            </label>
            <textarea id="meta_description" name="meta_description" rows="2"><?= $h($page['meta_description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="meta_keywords"><?= te('admin.pages.field_keywords') ?>
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_keywords') ?></span></span>
                <small style="font-weight:normal;color:#94A3B8"><?= te('admin.pages.hint_comma_separated') ?></small>
            </label>
            <input type="text" id="meta_keywords" name="meta_keywords" value="<?= $h($page['meta_keywords']) ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="og_title"><?= te('admin.pages.field_og_title') ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_og_title') ?></span></span>
                    <small style="font-weight:normal;color:#94A3B8"><?= te('admin.pages.hint_social') ?></small>
                </label>
                <input type="text" id="og_title" name="og_title" value="<?= $h($page['og_title']) ?>"
                       placeholder="<?= $h(t('admin.pages.placeholder_og_title')) ?>">
            </div>
            <div class="form-group">
                <label for="og_description"><?= te('admin.pages.field_og_desc') ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_og_desc') ?></span></span>
                    <small style="font-weight:normal;color:#94A3B8"><?= te('admin.pages.hint_social') ?></small>
                </label>
                <input type="text" id="og_description" name="og_description" value="<?= $h($page['og_description']) ?>"
                       placeholder="<?= $h(t('admin.pages.placeholder_og_desc')) ?>">
            </div>
        </div>
    </fieldset>

    <h2><?= te('admin.pages.sections') ?></h2>
    <div class="help-box" style="margin-bottom:1rem;">
        <strong>🧱 <?= te('admin.pages.sections_help_title') ?></strong> <?= te('admin.pages.sections_help_body') ?>
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
                            onclick="return confirm('<?= $h(t('admin.pages.confirm_delete_section')) ?>')">✕</button>
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
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_subtitle') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][subtitle]" value="<?= $h($c['subtitle'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_bg_image_url') ?></label>
                        <div class="input-with-browse">
                            <input type="text" id="hero_image_<?= $secId ?>" name="sections[<?= $secId ?>][image]" value="<?= $h($c['image'] ?? '') ?>">
                            <button type="button" class="btn btn-sm browse-media-btn" data-target="hero_image_<?= $secId ?>"><?= te('common.browse') ?></button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_cta_text') ?></label>
                            <input type="text" name="sections[<?= $secId ?>][cta_text]" value="<?= $h($c['cta_text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_cta_url') ?></label>
                            <input type="text" id="hero_cta_<?= $secId ?>" name="sections[<?= $secId ?>][cta_url]" value="<?= $h($c['cta_url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'hero_cta_' . $secId, $h); ?>
                        </div>
                    </div>
                    <?php break;

                case 'text': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
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
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Tartalom</label>
                        <div class="quill-editor" id="quill_<?= $secId ?>"><?= $c['body'] ?? '' ?></div>
                        <textarea name="sections[<?= $secId ?>][body]" class="quill-hidden" id="quill_hidden_<?= $secId ?>" style="display:none;"><?= $h($c['body'] ?? '') ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_image_url') ?></label>
                            <div class="input-with-browse">
                                <input type="text" id="imgtext_image_<?= $secId ?>" name="sections[<?= $secId ?>][image]" value="<?= $h($c['image'] ?? '') ?>">
                                <button type="button" class="btn btn-sm browse-media-btn" data-target="imgtext_image_<?= $secId ?>"><?= te('common.browse') ?></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_image_alt') ?>
                                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_image_alt') ?></span></span>
                            </label>
                            <input type="text" name="sections[<?= $secId ?>][image_alt]" value="<?= $h($c['image_alt'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_image_position') ?></label>
                            <select name="sections[<?= $secId ?>][image_position]">
                                <option value="right" <?= ($c['image_position'] ?? '') === 'right' ? 'selected' : '' ?>><?= te('admin.pages.opt_right') ?></option>
                                <option value="left" <?= ($c['image_position'] ?? '') === 'left' ? 'selected' : '' ?>><?= te('admin.pages.opt_left') ?></option>
                            </select>
                        </div>
                    </div>
                    <?php break;

                case 'cta': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_subtitle') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][subtitle]" value="<?= $h($c['subtitle'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_button_text') ?></label>
                            <input type="text" name="sections[<?= $secId ?>][button_text]" value="<?= $h($c['button_text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_button_url') ?></label>
                            <input type="text" id="cta_btn_<?= $secId ?>" name="sections[<?= $secId ?>][button_url]" value="<?= $h($c['button_url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'cta_btn_' . $secId, $h); ?>
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_bg_color') ?></label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                    </div>
                    <?php break;

                case 'cards': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_section_heading') ?></label>
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
                                        <label><?= te('common.title') ?></label>
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
                                    <label><?= te('admin.pages.label_description') ?></label>
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
                                        <label><?= te('common.title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][cards][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" id="card_link_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][cards][__IDX__][link]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'card_link_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_description') ?></label>
                                    <textarea name="sections[<?= $secId ?>][cards][__IDX__][description]" rows="2"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_card') ?></button>
                    </div>
                    <?php break;

                case 'gallery': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_gallery_images') ?>
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_gallery_alt') ?></span></span>
                        </p>
                        <?php foreach (($c['images'] ?? []) as $gi => $gImg): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="gal_img_<?= $secId ?>_<?= $gi ?>" name="sections[<?= $secId ?>][images][<?= $gi ?>][url]" value="<?= $h($gImg['url'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="gal_img_<?= $secId ?>_<?= $gi ?>"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_alt_seo') ?></label>
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
                                        <label><?= te('admin.pages.label_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="gal_img_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][images][__IDX__][url]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="gal_img_<?= $secId ?>___IDX__"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_alt_seo') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][images][__IDX__][alt]" value="">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_image') ?></button>
                    </div>
                    <?php break;

                case 'ticker': ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_bg_color') ?></label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_text_color') ?></label>
                            <input type="color" name="sections[<?= $secId ?>][text_color]" value="<?= $h($c['text_color'] ?? '#FFFFFF') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_speed') ?></label>
                            <input type="number" name="sections[<?= $secId ?>][speed]" value="<?= (int)($c['speed'] ?? 30) ?>" min="5" max="120">
                        </div>
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_ticker_items') ?></p>
                        <?php foreach (($c['items'] ?? []) as $ti => $tItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_text') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $ti ?>][text]"
                                               value="<?= $h($tItem['text'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:1">
                                        <label><?= te('admin.pages.label_link_optional') ?></label>
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
                                        <label><?= te('admin.pages.label_text') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][text]" value="">
                                    </div>
                                    <div class="form-group" style="flex:1">
                                        <label><?= te('admin.pages.label_link_optional') ?></label>
                                        <input type="text" id="ticker_link_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][link]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'ticker_link_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_element') ?></button>
                    </div>
                    <?php break;

                case 'accordion': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_qa_items') ?></p>
                        <?php foreach (($c['items'] ?? []) as $ai => $aItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_question') ?></label>
                                    <input type="text" name="sections[<?= $secId ?>][items][<?= $ai ?>][question]"
                                           value="<?= $h($aItem['question'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_answer_html') ?></label>
                                    <textarea name="sections[<?= $secId ?>][items][<?= $ai ?>][answer]"
                                              rows="3"><?= $h($aItem['answer'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_question') ?></label>
                                    <input type="text" name="sections[<?= $secId ?>][items][__IDX__][question]" value="">
                                </div>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_answer_html') ?></label>
                                    <textarea name="sections[<?= $secId ?>][items][__IDX__][answer]" rows="3"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_question') ?></button>
                    </div>
                    <?php break;

                case 'video': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:2">
                            <label><?= te('admin.pages.label_video_url') ?>
                                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_video_url') ?></span></span>
                            </label>
                            <input type="text" name="sections[<?= $secId ?>][url]"
                                   value="<?= $h($c['url'] ?? '') ?>"
                                   placeholder="<?= $h(t('admin.pages.placeholder_video_url')) ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('common.type') ?></label>
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
                            <label><?= te('admin.pages.label_style') ?></label>
                            <select name="sections[<?= $secId ?>][style]">
                                <option value="line" <?= ($c['style'] ?? '') === 'line' ? 'selected' : '' ?>><?= te('admin.pages.opt_line') ?></option>
                                <option value="dots" <?= ($c['style'] ?? '') === 'dots' ? 'selected' : '' ?>><?= te('admin.pages.opt_dots') ?></option>
                                <option value="space" <?= ($c['style'] ?? '') === 'space' ? 'selected' : '' ?>><?= te('admin.pages.opt_space') ?></option>
                                <option value="wave" <?= ($c['style'] ?? '') === 'wave' ? 'selected' : '' ?>><?= te('admin.pages.opt_wave') ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_size') ?></label>
                            <select name="sections[<?= $secId ?>][spacing]">
                                <option value="compact" <?= ($c['spacing'] ?? '') === 'compact' ? 'selected' : '' ?>><?= te('admin.pages.opt_compact') ?></option>
                                <option value="normal" <?= ($c['spacing'] ?? '') === 'normal' ? 'selected' : '' ?>><?= te('admin.pages.opt_normal') ?></option>
                                <option value="wide" <?= ($c['spacing'] ?? '') === 'wide' ? 'selected' : '' ?>><?= te('admin.pages.opt_wide') ?></option>
                            </select>
                        </div>
                    </div>
                    <?php break;

                case 'two_columns': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading_optional') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_left_column') ?></label>
                            <div class="quill-editor" id="quill_left_<?= $secId ?>"><?= $c['left_body'] ?? '' ?></div>
                            <textarea name="sections[<?= $secId ?>][left_body]" class="quill-hidden" id="quill_hidden_left_<?= $secId ?>" style="display:none;"><?= $h($c['left_body'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_right_column') ?></label>
                            <div class="quill-editor" id="quill_right_<?= $secId ?>"><?= $c['right_body'] ?? '' ?></div>
                            <textarea name="sections[<?= $secId ?>][right_body]" class="quill-hidden" id="quill_hidden_right_<?= $secId ?>" style="display:none;"><?= $h($c['right_body'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <?php break;

                case 'testimonials': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_testimonials') ?></p>
                        <?php foreach (($c['items'] ?? []) as $ti => $tItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_name') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $ti ?>][name]"
                                               value="<?= $h($tItem['name'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_role') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $ti ?>][role]"
                                               value="<?= $h($tItem['role'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="testi_img_<?= $secId ?>_<?= $ti ?>" name="sections[<?= $secId ?>][items][<?= $ti ?>][image]"
                                                   value="<?= $h($tItem['image'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="testi_img_<?= $secId ?>_<?= $ti ?>"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_testimonial_text') ?></label>
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
                                        <label><?= te('admin.pages.label_name') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][name]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_role') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][role]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="testi_img_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][image]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="testi_img_<?= $secId ?>___IDX__"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_testimonial_text') ?></label>
                                    <textarea name="sections[<?= $secId ?>][items][__IDX__][text]" rows="2"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_testimonial') ?></button>
                    </div>
                    <?php break;

                case 'stats': ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_heading_optional') ?></label>
                            <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_bg_color') ?></label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_stats') ?></p>
                        <?php foreach (($c['items'] ?? []) as $si => $sItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_number') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $si ?>][number]"
                                               value="<?= $h($sItem['number'] ?? '') ?>" placeholder="100+">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_stat_label') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $si ?>][label]"
                                               value="<?= $h($sItem['label'] ?? '') ?>" placeholder="<?= $h(t('admin.pages.placeholder_stat_label')) ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_number') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][number]" value="" placeholder="100+">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_stat_label') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][label]" value="" placeholder="<?= $h(t('admin.pages.placeholder_stat_label')) ?>">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_stat') ?></button>
                    </div>
                    <?php break;

                case 'page_list': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_page_type_filter') ?></label>
                            <select name="sections[<?= $secId ?>][page_type]">
                                <option value="article" <?= ($c['page_type'] ?? '') === 'article' ? 'selected' : '' ?>><?= te('admin.pages.opt_articles') ?></option>
                                <option value="page" <?= ($c['page_type'] ?? '') === 'page' ? 'selected' : '' ?>><?= te('admin.pages.opt_pages') ?></option>
                                <option value="all" <?= ($c['page_type'] ?? '') === 'all' ? 'selected' : '' ?>><?= te('admin.pages.opt_all') ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_count') ?></label>
                            <input type="number" name="sections[<?= $secId ?>][count]" value="<?= (int)($c['count'] ?? 10) ?>" min="1" max="100">
                        </div>
                    </div>
                    <?php break;

                case 'map': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_map_embed_url') ?>
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_map_embed') ?></span></span>
                        </label>
                        <input type="text" name="sections[<?= $secId ?>][embed_url]"
                               value="<?= $h($c['embed_url'] ?? '') ?>"
                               placeholder="https://www.google.com/maps/embed?pb=...">
                        <div class="field-hint"><?= te('admin.pages.hint_map_embed') ?></div>
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_height_px') ?></label>
                        <input type="number" name="sections[<?= $secId ?>][height]" value="<?= (int)($c['height'] ?? 400) ?>" min="200" max="800">
                    </div>
                    <?php break;

                case 'contact_form': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_success_message') ?>
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_success_message') ?></span></span>
                        </label>
                        <input type="text" name="sections[<?= $secId ?>][success_message]"
                               value="<?= $h($c['success_message'] ?? '') ?>">
                        <div class="field-hint"><?= te('admin.pages.hint_messages_menu') ?></div>
                    </div>
                    <?php break;

                case 'keywords_cloud': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_max_keywords') ?></label>
                        <input type="number" name="sections[<?= $secId ?>][count]" value="<?= (int)($c['count'] ?? 50) ?>" min="10" max="200">
                    </div>
                    <p class="form-help"><?= te('admin.pages.help_keywords_cloud') ?></p>
                    <?php break;

                case 'hero_slideshow': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_subtitle') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][subtitle]" value="<?= $h($c['subtitle'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= te('admin.pages.label_cta_text') ?></label>
                            <input type="text" name="sections[<?= $secId ?>][cta_text]" value="<?= $h($c['cta_text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_cta_url') ?></label>
                            <input type="text" id="hs_cta_<?= $secId ?>" name="sections[<?= $secId ?>][cta_url]" value="<?= $h($c['cta_url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'hs_cta_' . $secId, $h); ?>
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_slide_interval') ?></label>
                            <input type="number" name="sections[<?= $secId ?>][interval]" value="<?= (int)($c['interval'] ?? 4) ?>" min="1" max="15">
                        </div>
                    </div>
                    <div class="help-box" style="margin:1rem 0 0.5rem">
                        <strong>📸 <?= te('admin.pages.hs_images_label') ?></strong> <?= te('admin.pages.hs_images_body') ?> <a href="/admin/media.php"><?= te('admin.pages.hs_media_link') ?> →</a>
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_overlay_boxes') ?></p>
                        <?php foreach (($c['overlay_boxes'] ?? []) as $bi => $box): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('common.title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][overlay_boxes][<?= $bi ?>][title]" value="<?= $h($box['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_link_url') ?></label>
                                        <input type="text" id="hs_box_<?= $secId ?>_<?= $bi ?>" name="sections[<?= $secId ?>][overlay_boxes][<?= $bi ?>][url]" value="<?= $h($box['url'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'hs_box_' . $secId . '_' . $bi, $h); ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_size') ?></label>
                                        <select name="sections[<?= $secId ?>][overlay_boxes][<?= $bi ?>][size]">
                                            <option value="large" <?= ($box['size'] ?? '') === 'large' ? 'selected' : '' ?>><?= te('admin.pages.opt_large') ?></option>
                                            <option value="small" <?= ($box['size'] ?? '') === 'small' ? 'selected' : '' ?>><?= te('admin.pages.opt_small') ?></option>
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
                                        <label><?= te('common.title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][overlay_boxes][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_link_url') ?></label>
                                        <input type="text" id="hs_box_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][overlay_boxes][__IDX__][url]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'hs_box_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_size') ?></label>
                                        <select name="sections[<?= $secId ?>][overlay_boxes][__IDX__][size]">
                                            <option value="large"><?= te('admin.pages.opt_large') ?></option>
                                            <option value="small"><?= te('admin.pages.opt_small') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_box') ?></button>
                    </div>
                    <?php break;

                case 'product_grid': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_columns') ?></label>
                        <input type="number" name="sections[<?= $secId ?>][columns]" value="<?= (int)($c['columns'] ?? 5) ?>" min="2" max="6">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_products') ?></p>
                        <?php foreach (($c['items'] ?? []) as $pi => $pItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><?= te('common.title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $pi ?>][title]" value="<?= $h($pItem['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="pg_img_<?= $secId ?>_<?= $pi ?>" name="sections[<?= $secId ?>][items][<?= $pi ?>][image]" value="<?= $h($pItem['image'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="pg_img_<?= $secId ?>_<?= $pi ?>"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_link_url') ?></label>
                                        <input type="text" id="pg_url_<?= $secId ?>_<?= $pi ?>" name="sections[<?= $secId ?>][items][<?= $pi ?>][url]" value="<?= $h($pItem['url'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'pg_url_' . $secId . '_' . $pi, $h); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_image_alt') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $pi ?>][image_alt]" value="<?= $h($pItem['image_alt'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_short_desc_hover') ?></label>
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
                                        <label><?= te('common.title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="pg_img_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][image]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="pg_img_<?= $secId ?>___IDX__"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_link_url') ?></label>
                                        <input type="text" id="pg_url_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][url]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'pg_url_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_image_alt') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][image_alt]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_short_desc_hover') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][short_desc]" value="">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_product') ?></button>
                    </div>
                    <?php break;

                case 'seo_hidden': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_seo_button_text') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][button_text]" value="<?= $h($c['button_text'] ?? t('admin.pages.default_read_more')) ?>">
                    </div>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_seo_content') ?>
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_seo_content') ?></span></span>
                        </label>
                        <div class="quill-editor" id="quill_<?= $secId ?>"><?= $c['body'] ?? '' ?></div>
                        <textarea name="sections[<?= $secId ?>][body]" class="quill-hidden" id="quill_hidden_<?= $secId ?>" style="display:none;"><?= $h($c['body'] ?? '') ?></textarea>
                    </div>
                    <?php break;

                case 'link_banner': ?>
                    <div class="form-row">
                        <div class="form-group" style="flex:3">
                            <label><?= te('admin.pages.label_text') ?></label>
                            <input type="text" name="sections[<?= $secId ?>][text]" value="<?= $h($c['text'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_icon_emoji') ?></label>
                            <input type="text" name="sections[<?= $secId ?>][icon]" value="<?= $h($c['icon'] ?? '📖') ?>" style="width:60px">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:2">
                            <label><?= te('admin.pages.label_link_url') ?></label>
                            <input type="text" id="lb_url_<?= $secId ?>" name="sections[<?= $secId ?>][url]" value="<?= $h($c['url'] ?? '') ?>">
                            <?php pageSelector($allPagesForLinks, 'lb_url_' . $secId, $h); ?>
                        </div>
                        <div class="form-group">
                            <label><?= te('admin.pages.label_bg_color') ?></label>
                            <input type="color" name="sections[<?= $secId ?>][background_color]" value="<?= $h($c['background_color'] ?? '#0067FF') ?>">
                        </div>
                    </div>
                    <?php break;

                case 'reference_gallery': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? '') ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_projects') ?>
                            <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_projects') ?></span></span>
                        </p>
                        <?php foreach (($c['projects'] ?? []) as $pri => $proj): ?>
                            <div class="repeater-item" data-card-index="<?= $pri ?>" style="border-left:3px solid var(--admin-primary, #0067FF);padding-left:0.75rem;margin-bottom:1rem;">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_project_title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][<?= $pri ?>][title]" value="<?= $h($proj['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_cover_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="ref_cover_<?= $secId ?>_<?= $pri ?>" name="sections[<?= $secId ?>][projects][<?= $pri ?>][cover_image]" value="<?= $h($proj['cover_image'] ?? '') ?>">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_cover_<?= $secId ?>_<?= $pri ?>"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_cover_alt') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][<?= $pri ?>][cover_alt]" value="<?= $h($proj['cover_alt'] ?? '') ?>">
                                    </div>
                                </div>
                                <p class="form-help" style="margin-top:0.5rem;"><?= te('admin.pages.help_more_images') ?></p>
                                <div class="ref-images-container">
                                    <?php foreach (($proj['images'] ?? []) as $rii => $rImg): ?>
                                        <div class="form-row ref-image-row" style="margin-bottom:0.35rem;">
                                            <div class="form-group" style="flex:2">
                                                <div class="input-with-browse">
                                                    <input type="text" id="ref_img_<?= $secId ?>_<?= $pri ?>_<?= $rii ?>" name="sections[<?= $secId ?>][projects][<?= $pri ?>][images][<?= $rii ?>][url]" value="<?= $h($rImg['url'] ?? '') ?>">
                                                    <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_img_<?= $secId ?>_<?= $pri ?>_<?= $rii ?>"><?= te('common.browse') ?></button>
                                                </div>
                                            </div>
                                            <div class="form-group" style="flex:2">
                                                <input type="text" name="sections[<?= $secId ?>][projects][<?= $pri ?>][images][<?= $rii ?>][alt]" value="<?= $h($rImg['alt'] ?? '') ?>" placeholder="<?= $h(t('admin.pages.placeholder_alt_text')) ?>">
                                            </div>
                                            <button type="button" class="ref-image-remove repeater-remove" title="<?= $h(t('admin.pages.remove_image')) ?>">✕</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <template class="ref-image-template">
                                    <div class="form-row ref-image-row" style="margin-bottom:0.35rem;">
                                        <div class="form-group" style="flex:2">
                                            <div class="input-with-browse">
                                                <input type="text" id="ref_img_<?= $secId ?>___PIDX_____IIDX__" name="sections[<?= $secId ?>][projects][__PIDX__][images][__IIDX__][url]" value="">
                                                <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_img_<?= $secId ?>___PIDX_____IIDX__"><?= te('common.browse') ?></button>
                                            </div>
                                        </div>
                                        <div class="form-group" style="flex:2">
                                            <input type="text" name="sections[<?= $secId ?>][projects][__PIDX__][images][__IIDX__][alt]" value="" placeholder="<?= $h(t('admin.pages.placeholder_alt_text')) ?>">
                                        </div>
                                        <button type="button" class="ref-image-remove repeater-remove" title="<?= $h(t('admin.pages.remove_image')) ?>">✕</button>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-xs ref-image-add-btn" style="margin-top:0.35rem;">+ <?= te('admin.pages.add_image_short') ?></button>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item" data-card-index="__IDX__" style="border-left:3px solid var(--admin-primary, #0067FF);padding-left:0.75rem;margin-bottom:1rem;">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_project_title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_cover_image_url') ?></label>
                                        <div class="input-with-browse">
                                            <input type="text" id="ref_cover_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][projects][__IDX__][cover_image]" value="">
                                            <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_cover_<?= $secId ?>___IDX__"><?= te('common.browse') ?></button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><?= te('admin.pages.label_cover_alt') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][projects][__IDX__][cover_alt]" value="">
                                    </div>
                                </div>
                                <p class="form-help" style="margin-top:0.5rem;"><?= te('admin.pages.help_more_images') ?></p>
                                <div class="ref-images-container"></div>
                                <template class="ref-image-template">
                                    <div class="form-row ref-image-row" style="margin-bottom:0.35rem;">
                                        <div class="form-group" style="flex:2">
                                            <div class="input-with-browse">
                                                <input type="text" id="ref_img_<?= $secId ?>___IDX_____IIDX__" name="sections[<?= $secId ?>][projects][__IDX__][images][__IIDX__][url]" value="">
                                                <button type="button" class="btn btn-sm browse-media-btn" data-target="ref_img_<?= $secId ?>___IDX_____IIDX__"><?= te('common.browse') ?></button>
                                            </div>
                                        </div>
                                        <div class="form-group" style="flex:2">
                                            <input type="text" name="sections[<?= $secId ?>][projects][__IDX__][images][__IIDX__][alt]" value="" placeholder="<?= $h(t('admin.pages.placeholder_alt_text')) ?>">
                                        </div>
                                        <button type="button" class="ref-image-remove repeater-remove" title="<?= $h(t('admin.pages.remove_image')) ?>">✕</button>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-xs ref-image-add-btn" style="margin-top:0.35rem;">+ <?= te('admin.pages.add_image_short') ?></button>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_project') ?></button>
                    </div>
                    <?php break;

                case 'sitemap': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? t('admin.section.sitemap_label')) ?>">
                    </div>
                    <p class="form-help"><?= te('admin.pages.help_sitemap') ?></p>
                    <?php break;

                case 'tudasmorzsak': ?>
                    <div class="form-group">
                        <label><?= te('admin.pages.label_heading') ?></label>
                        <input type="text" name="sections[<?= $secId ?>][heading]" value="<?= $h($c['heading'] ?? t('admin.section.tudasmorzsak_label')) ?>">
                    </div>
                    <div class="repeater-editor" data-section-id="<?= $secId ?>">
                        <p class="form-help"><?= te('admin.pages.help_morsels') ?></p>
                        <?php foreach (($c['items'] ?? []) as $tmi => $tmItem): ?>
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('common.title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][<?= $tmi ?>][title]" value="<?= $h($tmItem['title'] ?? '') ?>">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_link_url_optional') ?></label>
                                        <input type="text" id="tm_url_<?= $secId ?>_<?= $tmi ?>" name="sections[<?= $secId ?>][items][<?= $tmi ?>][url]" value="<?= $h($tmItem['url'] ?? '') ?>">
                                        <?php pageSelector($allPagesForLinks, 'tm_url_' . $secId . '_' . $tmi, $h); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_short_desc') ?></label>
                                    <textarea name="sections[<?= $secId ?>][items][<?= $tmi ?>][description]" rows="2"><?= $h($tmItem['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <template class="repeater-template">
                            <div class="repeater-item">
                                <?php repeaterRemoveBtn(); ?>
                                <div class="form-row">
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('common.title') ?></label>
                                        <input type="text" name="sections[<?= $secId ?>][items][__IDX__][title]" value="">
                                    </div>
                                    <div class="form-group" style="flex:2">
                                        <label><?= te('admin.pages.label_link_url_optional') ?></label>
                                        <input type="text" id="tm_url_<?= $secId ?>___IDX__" name="sections[<?= $secId ?>][items][__IDX__][url]" value="">
                                        <?php pageSelectorTpl($allPagesForLinks, 'tm_url_' . $secId . '___IDX__', $h); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= te('admin.pages.label_short_desc') ?></label>
                                    <textarea name="sections[<?= $secId ?>][items][__IDX__][description]" rows="2"></textarea>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn btn-sm repeater-add-btn">+ <?= te('admin.pages.add_morsel') ?></button>
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
        <button type="submit" name="save_page" value="1" class="btn btn-primary"><?= te('common.save') ?></button>
        <a href="/admin/pages.php" class="btn btn-secondary"><?= te('common.back') ?></a>
    </div>
</form>

<hr>

<form method="POST" class="admin-form inline-form">
    <?= csrfField() ?>
    <h3><?= te('admin.pages.add_section_title') ?>
        <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.pages.tooltip_label')) ?>">?</button><span class="tooltip-bubble"><?= te('admin.pages.tooltip_add_section') ?></span></span>
    </h3>
    <div class="form-row">
        <div class="form-group">
            <select name="new_section_type">
                <?php foreach ($sectionLabels as $typeKey => $typeLabel): ?>
                    <option value="<?= $h($typeKey) ?>" title="<?= $h($sectionDescriptions[$typeKey] ?? '') ?>"><?= $h($typeLabel) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="add_section" value="1" class="btn btn-primary"><?= te('common.add') ?></button>
    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
