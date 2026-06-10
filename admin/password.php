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
        $error = 'A jelenlegi jelszó helytelen.';
    } elseif (strlen($newPw) < 6) {
        $error = 'Az új jelszónak legalább 6 karakter hosszúnak kell lennie.';
    } elseif ($newPw !== $confirmPw) {
        $error = 'Az új jelszó és a megerősítés nem egyezik.';
    } else {
        $hash = password_hash($newPw, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id")
            ->execute(['hash' => $hash, 'id' => $userId]);
        $msg = 'Jelszó sikeresen megváltoztatva.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1>Jelszó módosítása</h1>

<div class="help-box help-warning">
    <strong>🔑 Biztonsági tippek a jelszóhoz:</strong>
    <ul style="margin:0.5rem 0 0 1.2rem;font-size:0.85rem;line-height:1.7;">
        <li>Használjon legalább 8 karaktert (minimum 6 szükséges).</li>
        <li>Keverjen nagy- és kisbetűket, számokat és speciális karaktereket.</li>
        <li>Ne használja ugyanazt a jelszót más fiókokhoz.</li>
        <li>Ne ossza meg a jelszót mással e-mailben vagy üzenetben.</li>
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
        <legend>Jelszó módosítása</legend>
        <div class="form-group">
            <label for="current_password">Jelenlegi jelszó</label>
            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <label for="new_password">Új jelszó</label>
            <input type="password" id="new_password" name="new_password" required
                   minlength="6" autocomplete="new-password">
            <small class="form-help">Legalább 6 karakter.</small>
        </div>
        <div class="form-group">
            <label for="confirm_password">Új jelszó megerősítése</label>
            <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
        </div>
    </fieldset>
    <div class="form-actions">
        <button type="submit" name="change_password" value="1" class="btn btn-primary">Jelszó módosítása</button>
    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
