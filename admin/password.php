<?php
// ─── Admin — Password Change ───

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['change_password'])) {
    csrfVerify();
    $currentPw = $_POST['current_password'] ?? '';
    $newPw     = $_POST['new_password'] ?? '';
    $confirmPw = $_POST['confirm_password'] ?? '';

    // Verify current password
    $userId = $_SESSION['admin_user_id'];
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($currentPw, $user['password_hash'])) {
        $error = t('admin.password.err_current');
    } elseif (strlen($newPw) < 6) {
        $error = t('admin.password.err_min_length');
    } elseif ($newPw !== $confirmPw) {
        $error = t('admin.password.err_mismatch');
    } else {
        $hash = password_hash($newPw, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id")
            ->execute(['hash' => $hash, 'id' => $userId]);
        $msg = t('admin.password.msg_changed');
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1><?= te('admin.password.heading') ?></h1>

<div class="help-box help-warning">
    <strong><?= te('admin.password.help_title') ?></strong>
    <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
        <li><?= te('admin.password.help_length') ?></li>
        <li><?= te('admin.password.help_mix') ?></li>
        <li><?= te('admin.password.help_unique') ?></li>
        <li><?= te('admin.password.help_share') ?></li>
    </ul>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success"><?= $h($msg) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= $h($error) ?></div>
<?php endif; ?>

<form method="POST" class="admin-form">
    <?= csrfField() ?>
    <fieldset>
        <legend><?= te('admin.password.legend') ?></legend>
        <div class="form-group">
            <label for="current_password"><?= te('admin.password.field_current') ?></label>
            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <label for="new_password"><?= te('admin.password.field_new') ?></label>
            <input type="password" id="new_password" name="new_password" required
                   minlength="6" autocomplete="new-password">
            <small class="form-help"><?= te('admin.password.hint_min') ?></small>
        </div>
        <div class="form-group">
            <label for="confirm_password"><?= te('admin.password.field_confirm') ?></label>
            <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
        </div>
    </fieldset>
    <div class="form-actions">
        <button type="submit" name="change_password" value="1" class="btn btn-primary"><?= te('admin.password.submit') ?></button>
    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
