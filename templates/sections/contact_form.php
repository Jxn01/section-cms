<?php
// Section: contact_form
// $content keys: heading, success_message
$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };
$success = isset($_GET['contact']) && $_GET['contact'] === 'ok';
$error   = isset($_GET['contact']) && $_GET['contact'] === 'error';
$currentUrl = $_SERVER['REQUEST_URI'];
$baseUrl    = strtok($currentUrl, '?');
?>
<section class="section section-contact-form" id="contact-form">
    <div class="container">
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-heading"><?= $h($content['heading']) ?></h2>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="form-success-message" role="alert">
                <p>✓ <?= $h($content['success_message'] ?? 'Köszönjük az üzenetet! Hamarosan felvesszük Önnel a kapcsolatot.') ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="form-error-message" role="alert">
                <p>Hiba történt. Kérjük próbálja újra.</p>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $h($baseUrl) ?>" class="contact-form" novalidate>
            <input type="hidden" name="contact_submit" value="1">
            <?php
                if (session_status() === PHP_SESSION_NONE) { session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict']); }
                if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
            ?>
            <input type="hidden" name="csrf_token" value="<?= $h($_SESSION['csrf_token']) ?>">
            <div class="form-grid">
                <div class="form-field">
                    <label for="contact_name">Név <span class="required">*</span></label>
                    <input type="text" id="contact_name" name="contact_name" required
                           placeholder="Az Ön neve" autocomplete="name">
                </div>
                <div class="form-field">
                    <label for="contact_email">E-mail <span class="required">*</span></label>
                    <input type="email" id="contact_email" name="contact_email" required
                           placeholder="pelda@email.hu" autocomplete="email">
                </div>
                <div class="form-field">
                    <label for="contact_phone">Telefon</label>
                    <input type="tel" id="contact_phone" name="contact_phone"
                           placeholder="+36 ..." autocomplete="tel">
                </div>
                <div class="form-field form-field-full">
                    <label for="contact_message">Üzenet <span class="required">*</span></label>
                    <textarea id="contact_message" name="contact_message" rows="5" required
                              placeholder="Írja le üzenetét..."></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Üzenet küldése</button>
        </form>
    </div>
</section>
