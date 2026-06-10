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
    <h1><?= te('admin.pages.list_title') ?></h1>
    <a href="/admin/page-edit.php?new=1" class="btn btn-primary">+ <?= te('admin.pages.new_page') ?></a>
</div>

<div class="help-box">
    <strong>📄 <?= te('admin.pages.help_intro') ?></strong> <?= te('admin.pages.help_intro_body') ?>
    <button type="button" class="help-toggle" data-target="pagesHelp" aria-expanded="false"><span class="help-toggle-icon">▸</span> <?= te('admin.pages.help_more') ?></button>
    <div class="help-collapsible" id="pagesHelp">
        <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
            <li><strong><?= te('common.title') ?>:</strong> <?= te('admin.pages.help_title') ?></li>
            <li><strong><?= te('common.slug') ?>:</strong> <?= te('admin.pages.help_slug') ?></li>
            <li><strong><?= te('common.type') ?>:</strong> <?= te('admin.pages.help_type') ?></li>
            <li><strong><?= te('admin.pages.sections') ?>:</strong> <?= te('admin.pages.help_sections') ?></li>
            <li><strong><?= te('common.status') ?>:</strong> <?= te('admin.pages.help_status') ?></li>
            <li><strong><?= te('admin.pages.help_home_slug_label') ?>:</strong> <?= te('admin.pages.help_home_slug') ?></li>
        </ul>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $msgs = [
            'saved'   => t('admin.pages.msg_saved'),
            'created' => t('admin.pages.msg_created'),
            'deleted' => t('admin.pages.msg_deleted'),
        ];
        echo $h($msgs[$_GET['msg']] ?? t('admin.pages.msg_done'));
        ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
<table class="admin-table">
    <thead>
        <tr>
            <th><?= te('common.title') ?></th>
            <th><?= te('common.slug') ?></th>
            <th><?= te('common.type') ?></th>
            <th><?= te('admin.pages.sections') ?></th>
            <th><?= te('common.status') ?></th>
            <th><?= te('common.actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pages)): ?>
            <tr><td colspan="6" style="text-align:center"><?= te('admin.pages.no_pages') ?></td></tr>
        <?php endif; ?>
        <?php foreach ($pages as $p): ?>
            <tr>
                <td><strong><?= $h($p['title']) ?></strong></td>
                <td><code>/<?= $h($p['slug']) ?></code></td>
                <td><?= ($p['page_type'] ?? 'page') === 'article' ? '<span class="badge" style="background:#E0E7FF;color:#3730A3;">' . $h(t('admin.pages.article')) . '</span>' : $h(t('admin.pages.page')) ?></td>
                <td><?= (int) $p['section_count'] ?></td>
                <td>
                    <span class="badge badge-<?= $p['status'] ?>">
                        <?= $p['status'] === 'published' ? $h(t('common.published')) : $h(t('common.draft')) ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="/admin/page-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm"><?= te('common.edit') ?></a>
                    <?php if ($p['slug'] !== 'home'): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('<?= $h(t('admin.pages.confirm_delete')) ?>')">
                            <?= csrfField() ?>
                            <button type="submit" name="delete_page" value="<?= $p['id'] ?>"
                                    class="btn btn-sm btn-danger"><?= te('common.delete') ?></button>
                        </form>
                    <?php endif; ?>
                    <a href="/<?= $p['slug'] === 'home' ? '' : $h($p['slug']) ?>"
                       target="_blank" class="btn btn-sm"><?= te('common.view') ?> ↗</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
