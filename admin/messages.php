<?php
// ─── Admin — Contact Messages ───

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

// Mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    csrfVerify();
    $id = (int) $_POST['mark_read'];
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = :id")->execute(['id' => $id]);
    header('Location: /admin/messages.php?msg=read');
    exit;
}

// Delete message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    csrfVerify();
    $id = (int) $_POST['delete_message'];
    $pdo->prepare("DELETE FROM contact_messages WHERE id = :id")->execute(['id' => $id]);
    header('Location: /admin/messages.php?msg=deleted');
    exit;
}

// Fetch messages
$messages = $pdo->query(
    "SELECT * FROM contact_messages ORDER BY created_at DESC"
)->fetchAll();

$unreadCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><?= te('admin.messages.heading') ?> <?php if ($unreadCount > 0): ?><span class="badge badge-published"><?= $unreadCount ?> <?= te('admin.messages.new_badge') ?></span><?php endif; ?></h1>
</div>

<div class="help-box">
    <?= t('admin.messages.help') ?>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php
        $msgs = ['read' => t('admin.messages.msg_read'), 'deleted' => t('admin.messages.msg_deleted')];
        echo $h($msgs[$_GET['msg']] ?? t('admin.messages.msg_done'));
        ?>
    </div>
<?php endif; ?>

<?php if (empty($messages)): ?>
    <p><?= te('admin.messages.empty') ?></p>
<?php else: ?>
    <div class="table-responsive">
    <table class="admin-table">
        <thead>
            <tr>
                <th><?= te('common.date') ?></th>
                <th><?= te('admin.messages.col_sender') ?></th>
                <th><?= te('admin.messages.col_email') ?></th>
                <th><?= te('admin.messages.col_phone') ?></th>
                <th><?= te('admin.messages.col_message') ?></th>
                <th><?= te('admin.messages.col_page') ?></th>
                <th><?= te('common.actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $m): ?>
                <tr style="<?= !$m['is_read'] ? 'font-weight:600; background:#EBF5FF;' : '' ?>">
                    <td><?= $h(I18n::formatDate($m['created_at'], true)) ?></td>
                    <td><?= $h($m['sender_name']) ?></td>
                    <td><a href="mailto:<?= $h($m['sender_email']) ?>"><?= $h($m['sender_email']) ?></a></td>
                    <td><?= $h($m['sender_phone']) ?></td>
                    <td title="<?= $h($m['message']) ?>"><?= $h(mb_strimwidth($m['message'], 0, 80, '…')) ?></td>
                    <td><?= $h($m['page_slug']) ?></td>
                    <td class="actions">
                        <?php if (!$m['is_read']): ?>
                            <form method="POST" style="display:inline;">
                                <?= csrfField() ?>
                                <button type="submit" name="mark_read" value="<?= $m['id'] ?>" class="btn btn-sm"><?= te('admin.messages.mark_read') ?></button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm(<?= $h(json_encode(t('admin.messages.confirm_delete'))) ?>)">
                            <?= csrfField() ?>
                            <button type="submit" name="delete_message" value="<?= $m['id'] ?>" class="btn btn-sm btn-danger"><?= te('common.delete') ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
