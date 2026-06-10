<?php
// ─── Admin — Media Manager ───
// Upload, browse, edit, and delete media files.

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$uploadDir = __DIR__ . '/../assets/uploads/';
$isBrowseMode = isset($_GET['browse']);
$targetField  = $_GET['target'] ?? '';

// Ensure uploads dir exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ─── Handle upload ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['media_file'])) {
    csrfVerify();
    $file = $_FILES['media_file'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowedExts  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize      = 10 * 1024 * 1024; // 10 MB

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Server-side MIME check using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $realMime = $finfo->file($file['tmp_name']);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadError = t('admin.media.err_upload');
    } elseif ($file['size'] > $maxSize) {
        $uploadError = t('admin.media.err_too_large');
    } elseif (!in_array($realMime, $allowedTypes) || !in_array($ext, $allowedExts)) {
        $uploadError = t('admin.media.err_type');
    } else {
        $safeExt  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = time() . '_' . preg_replace('/[^a-z0-9._-]/i', '', pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = $safeName . '.' . $safeExt;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $stmt = $pdo->prepare(
                "INSERT INTO media (filename, original_name, alt_text, mime_type, file_size, is_featured)
                 VALUES (:fn, :orig, :alt, :mime, :size, :feat)"
            );
            $stmt->execute([
                'fn'   => $filename,
                'orig' => $file['name'],
                'alt'  => trim($_POST['alt_text'] ?? ''),
                'mime' => $realMime,
                'size' => $file['size'],
                'feat' => !empty($_POST['is_featured']) ? 1 : 0,
            ]);
            $redirectUrl = $isBrowseMode
                ? '/admin/media.php?browse=1&target=' . urlencode($targetField) . '&msg=uploaded'
                : '/admin/media.php?msg=uploaded';
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $uploadError = t('admin.media.err_upload');
        }
    }
}

// ─── Handle delete ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_media'])) {
    csrfVerify();
    $id = (int) $_POST['delete_media'];
    $stmt = $pdo->prepare("SELECT filename FROM media WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $media = $stmt->fetch();
    if ($media) {
        $filePath = $uploadDir . $media['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $pdo->prepare("DELETE FROM media WHERE id = :id")->execute(['id' => $id]);
    }
    header('Location: /admin/media.php?msg=deleted');
    exit;
}

// ─── Handle alt text update ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['update_alt'])) {
    csrfVerify();
    $id = (int) $_POST['media_id'];
    $alt = trim($_POST['alt_text'] ?? '');
    $pdo->prepare("UPDATE media SET alt_text = :alt WHERE id = :id")
        ->execute(['alt' => $alt, 'id' => $id]);
    header('Location: /admin/media.php?msg=saved');
    exit;
}

// ─── Handle featured toggle ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_featured'])) {
    csrfVerify();
    $id = (int) $_POST['media_id'];
    $feat = (!empty($_POST['is_featured']) && $_POST['is_featured'] == '1') ? 1 : 0;
    $pdo->prepare("UPDATE media SET is_featured = :feat WHERE id = :id")
        ->execute(['feat' => $feat, 'id' => $id]);
    header('Location: /admin/media.php?msg=saved');
    exit;
}

// Fetch all media
$media = $pdo->query("SELECT * FROM media ORDER BY uploaded_at DESC")->fetchAll();

// Browse mode — minimal layout
if ($isBrowseMode): ?>
<!DOCTYPE html>
<html lang="<?= I18n::htmlLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= te('admin.media.browser_title') ?></title>
    <link rel="stylesheet" href="/admin/assets/admin.css">
    <style>
        body { background: var(--admin-bg); padding: 1rem; }
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 0.75rem; margin-top: 1rem; }
        .media-thumb { cursor: pointer; border: 2px solid transparent; border-radius: var(--admin-radius); overflow: hidden; transition: border-color 0.15s; }
        .media-thumb:hover { border-color: var(--admin-primary); }
        .media-thumb img { width: 100%; aspect-ratio: 1; object-fit: cover; display: block; }
        .media-thumb-name { font-size: 0.75rem; padding: 0.25rem; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>
    <h2><?= te('admin.media.pick_heading') ?></h2>

    <form method="POST" enctype="multipart/form-data" style="margin:1rem 0;">
        <?= csrfField() ?>
        <div style="display:flex;gap:0.5rem;align-items:end;flex-wrap:wrap;">
            <div class="form-group" style="flex:1;margin:0;">
                <label><?= te('admin.media.upload_new') ?></label>
                <input type="file" name="media_file" accept="image/*" required>
            </div>
            <div class="form-group" style="flex:1;margin:0;">
                <label><?= te('admin.media.alt_text') ?></label>
                <input type="text" name="alt_text" placeholder="<?= $h(t('admin.media.alt_placeholder')) ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><?= te('common.upload') ?></button>
        </div>
    </form>

    <?php if (!empty($uploadError)): ?>
        <div class="alert alert-error"><?= $h($uploadError) ?></div>
    <?php endif; ?>

    <div class="media-grid">
        <?php foreach ($media as $m): ?>
            <div class="media-thumb" onclick="selectMedia('<?= $h($m['filename']) ?>', '<?= $h($targetField) ?>')">
                <img src="/assets/uploads/<?= $h($m['filename']) ?>" alt="<?= $h($m['alt_text']) ?>">
                <div class="media-thumb-name"><?= $h($m['original_name'] ?: $m['filename']) ?></div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($media)): ?>
            <p><?= te('admin.media.empty_browse') ?></p>
        <?php endif; ?>
    </div>

    <script>
    function selectMedia(filename, targetField) {
        const url = '/assets/uploads/' + filename;
        if (window.opener && targetField) {
            const field = window.opener.document.getElementById(targetField);
            if (field) {
                field.value = url;
                field.dispatchEvent(new Event('change'));
            }
            window.close();
        }
    }
    </script>
</body>
</html>
<?php exit; endif; ?>

<?php require __DIR__ . '/includes/header.php'; ?>

<div class="page-header">
    <h1><?= te('admin.media.heading') ?></h1>
</div>

<div class="help-box">
    <?= t('admin.media.help') ?>
    <button type="button" class="help-toggle" data-target="mediaHelp" aria-expanded="false"><span class="help-toggle-icon">▸</span> <?= te('admin.media.help_toggle') ?></button>
    <div class="help-collapsible" id="mediaHelp">
        <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
            <li><?= t('admin.media.help_formats') ?></li>
            <li><?= t('admin.media.help_alt') ?>
                <br>🔍 <?= t('admin.media.help_alt_seo') ?>
                <br>♿ <?= t('admin.media.help_alt_a11y') ?></li>
            <li><?= t('admin.media.help_size') ?></li>
            <li><?= t('admin.media.help_filename') ?></li>
        </ul>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $msgs = ['uploaded' => t('admin.media.msg_uploaded'), 'deleted' => t('admin.media.msg_deleted'), 'saved' => t('admin.media.msg_saved')];
        echo $h($msgs[$_GET['msg']] ?? t('admin.media.msg_done'));
        ?>
    </div>
<?php endif; ?>

<?php if (!empty($uploadError)): ?>
    <div class="alert alert-error"><?= $h($uploadError) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form" style="margin-bottom:2rem;">
    <?= csrfField() ?>
    <fieldset>
        <legend><?= te('admin.media.legend_upload') ?></legend>
        <div class="form-row">
            <div class="form-group">
                <label><?= te('admin.media.field_file') ?></label>
                <input type="file" name="media_file" accept="image/*" required>
            </div>
            <div class="form-group">
                <label><?= te('admin.media.field_alt_seo') ?> <span style="color:#D97706"><?= te('admin.media.recommended') ?></span></label>
                <input type="text" name="alt_text" placeholder="<?= $h(t('admin.media.alt_placeholder_seo')) ?>">
                <div class="seo-hint seo-warn"><?= te('admin.media.alt_warning') ?></div>
            </div>
        </div>
        <div class="form-group" style="margin:0.5rem 0;">
            <label><input type="checkbox" name="is_featured" value="1"> <?= te('admin.media.featured_label') ?></label>
        </div>
        <button type="submit" class="btn btn-primary"><?= te('common.upload') ?></button>
    </fieldset>
</form>

<?php if (!empty($media)): ?>
    <div class="media-admin-grid">
        <?php foreach ($media as $m): ?>
            <div class="media-admin-card">
                <div class="media-preview">
                    <img src="/assets/uploads/<?= $h($m['filename']) ?>" alt="<?= $h($m['alt_text']) ?>">
                </div>
                <div class="media-info">
                    <code>/assets/uploads/<?= $h($m['filename']) ?></code>
                    <form method="POST" style="margin-top:0.5rem;">
                        <?= csrfField() ?>
                        <input type="hidden" name="media_id" value="<?= $m['id'] ?>">
                        <div class="form-group" style="margin-bottom:0.5rem;">
                            <input type="text" name="alt_text" value="<?= $h($m['alt_text']) ?>" placeholder="<?= $h(t('admin.media.alt_placeholder_short')) ?>">
                        </div>
                        <div style="display:flex;gap:0.5rem;">
                            <button type="submit" name="update_alt" value="1" class="btn btn-sm"><?= te('common.save') ?></button>
                        </div>
                    </form>
                    <form method="POST" style="margin-top:0.25rem;" onsubmit="return confirm('<?= $h(t('admin.media.confirm_delete')) ?>')">
                        <?= csrfField() ?>
                        <button type="submit" name="delete_media" value="<?= $m['id'] ?>" class="btn btn-sm btn-danger"><?= te('common.delete') ?></button>
                    </form>
                    <form method="POST" style="margin-top:0.25rem;">
                        <?= csrfField() ?>
                        <input type="hidden" name="media_id" value="<?= $m['id'] ?>">
                        <input type="hidden" name="update_featured" value="1">
                        <input type="hidden" name="is_featured" value="0">
                        <label style="display:flex;align-items:center;gap:0.35rem;font-size:0.85rem;cursor:pointer;">
                            <input type="checkbox" name="is_featured" value="1"
                                   onchange="this.form.submit()"
                                   <?= !empty($m['is_featured']) ? 'checked' : '' ?>>
                            <?= te('admin.media.featured_short') ?>
                        </label>
                    </form>
                    <small class="text-muted">
                        <?= $h($m['original_name']) ?> · <?= round(($m['file_size'] ?? 0) / 1024) ?> KB
                        · <?= $h(I18n::formatDate($m['uploaded_at'])) ?>
                    </small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p><?= te('admin.media.empty') ?></p>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
