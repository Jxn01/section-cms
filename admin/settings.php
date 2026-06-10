<?php
// ─── Admin — Site Settings ───

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

$fields = [
    'site_name'        => ['label' => t('admin.settings.field.site_name.label'),        'type' => 'text',     'hint' => t('admin.settings.field.site_name.hint')],
    'site_tagline'     => ['label' => t('admin.settings.field.site_tagline.label'),      'type' => 'text',     'hint' => t('admin.settings.field.site_tagline.hint')],
    'meta_description' => ['label' => t('admin.settings.field.meta_description.label'),  'type' => 'textarea', 'hint' => t('admin.settings.field.meta_description.hint')],
    'meta_keywords'    => ['label' => t('admin.settings.field.meta_keywords.label'),     'type' => 'text',     'hint' => t('admin.settings.field.meta_keywords.hint')],
    'contact_email'    => ['label' => t('admin.settings.field.contact_email.label'),     'type' => 'email',    'hint' => t('admin.settings.field.contact_email.hint')],
    'contact_phone'    => ['label' => t('admin.settings.field.contact_phone.label'),     'type' => 'text',     'hint' => t('admin.settings.field.contact_phone.hint')],
    'contact_address'  => ['label' => t('admin.settings.field.contact_address.label'),   'type' => 'text',     'hint' => t('admin.settings.field.contact_address.hint')],
    'primary_color'    => ['label' => t('admin.settings.field.primary_color.label'),     'type' => 'color',    'hint' => t('admin.settings.field.primary_color.hint')],
    'secondary_color'  => ['label' => t('admin.settings.field.secondary_color.label'),   'type' => 'color',    'hint' => t('admin.settings.field.secondary_color.hint')],
    'facebook_url'     => ['label' => t('admin.settings.field.facebook_url.label'),      'type' => 'url',      'hint' => t('admin.settings.field.facebook_url.hint')],
    'og_default_image' => ['label' => t('admin.settings.field.og_default_image.label'),  'type' => 'image',    'hint' => t('admin.settings.field.og_default_image.hint')],
    'logo_url'         => ['label' => t('admin.settings.field.logo_url.label'),          'type' => 'image',    'hint' => t('admin.settings.field.logo_url.hint')],
    'logo_display_mode' => ['label' => t('admin.settings.field.logo_display_mode.label'), 'type' => 'select', 'hint' => t('admin.settings.field.logo_display_mode.hint'), 'options' => ['none' => t('admin.settings.field.logo_display_mode.option.none'), 'replace' => t('admin.settings.field.logo_display_mode.option.replace'), 'beside' => t('admin.settings.field.logo_display_mode.option.beside')]],
];

// ─── SMTP / Email settings ───
$smtpFields = [
    'smtp_host' => ['label' => t('admin.settings.field.smtp_host.label'), 'type' => 'text',     'hint' => t('admin.settings.field.smtp_host.hint')],
    'smtp_port' => ['label' => t('admin.settings.field.smtp_port.label'), 'type' => 'number',   'hint' => t('admin.settings.field.smtp_port.hint')],
    'smtp_user' => ['label' => t('admin.settings.field.smtp_user.label'), 'type' => 'email',    'hint' => t('admin.settings.field.smtp_user.hint')],
    'smtp_pass' => ['label' => t('admin.settings.field.smtp_pass.label'), 'type' => 'password', 'hint' => t('admin.settings.field.smtp_pass.hint')],
    'smtp_from' => ['label' => t('admin.settings.field.smtp_from.label'), 'type' => 'email',    'hint' => t('admin.settings.field.smtp_from.hint')],
    'smtp_to'   => ['label' => t('admin.settings.field.smtp_to.label'),   'type' => 'email',    'hint' => t('admin.settings.field.smtp_to.hint')],
];

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['save_settings'])) {
    csrfVerify();
    // Whitelist allowed setting keys
    $allowedKeys = array_merge(array_keys($fields), array_keys($smtpFields));
    foreach ($_POST['settings'] as $key => $value) {
        if (!in_array($key, $allowedKeys, true)) continue;
        $stmt = $pdo->prepare(
            "INSERT INTO site_settings (setting_key, setting_value)
             VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = :v2"
        );
        $stmt->execute(['k' => $key, 'v' => trim($value), 'v2' => trim($value)]);
    }
    header('Location: /admin/settings.php?msg=saved');
    exit;
}

// Load current settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
$settings = [];
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

require __DIR__ . '/includes/header.php';
?>

<h1><?= te('admin.settings.heading') ?></h1>

<div class="help-box">
    <?= t('admin.settings.help') ?>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
    <div class="alert alert-success"><?= te('admin.settings.msg_saved') ?></div>
<?php endif; ?>

<form method="POST" class="admin-form">
    <?= csrfField() ?>
    <fieldset>
        <legend><?= te('admin.settings.legend_general') ?></legend>
        <?php foreach ($fields as $key => $field): ?>
            <div class="form-group">
                <label for="<?= $key ?>"><?= $h($field['label']) ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.settings.tooltip_help')) ?>">?</button><span class="tooltip-bubble"><?= $h($field['hint']) ?></span></span>
                </label>
                <?php if ($field['type'] === 'textarea'): ?>
                    <textarea id="<?= $key ?>" name="settings[<?= $key ?>]" rows="3"><?= $h($settings[$key] ?? '') ?></textarea>
                <?php elseif ($field['type'] === 'color'): ?>
                    <input type="color" id="<?= $key ?>" name="settings[<?= $key ?>]"
                           value="<?= $h($settings[$key] ?? '#000000') ?>">
                <?php elseif ($field['type'] === 'image'): ?>
                    <div class="input-with-browse">
                        <input type="text" id="<?= $key ?>" name="settings[<?= $key ?>]"
                               value="<?= $h($settings[$key] ?? '') ?>" placeholder="/assets/uploads/kep.jpg">
                        <button type="button" class="btn btn-sm browse-media-btn" data-target="<?= $key ?>"><?= te('common.browse') ?></button>
                    </div>
                <?php elseif ($field['type'] === 'select' && !empty($field['options'])): ?>
                    <select id="<?= $key ?>" name="settings[<?= $key ?>]">
                        <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                            <option value="<?= $h($optVal) ?>" <?= ($settings[$key] ?? '') === $optVal ? 'selected' : '' ?>><?= $h($optLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="<?= $field['type'] ?>" id="<?= $key ?>"
                           name="settings[<?= $key ?>]"
                           value="<?= $h($settings[$key] ?? '') ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <fieldset>
        <legend><?= te('admin.settings.legend_smtp') ?></legend>
        <div class="help-box" style="margin-bottom:1rem;">
            <?= te('admin.settings.smtp_help') ?>
        </div>
        <?php foreach ($smtpFields as $key => $field): ?>
            <div class="form-group">
                <label for="<?= $key ?>"><?= $h($field['label']) ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="<?= $h(t('admin.settings.tooltip_help')) ?>">?</button><span class="tooltip-bubble"><?= $h($field['hint']) ?></span></span>
                </label>
                <?php if ($field['type'] === 'password'): ?>
                    <input type="password" id="<?= $key ?>"
                           name="settings[<?= $key ?>]"
                           value="<?= $h($settings[$key] ?? '') ?>"
                           autocomplete="off"
                           placeholder="••••••••">
                <?php else: ?>
                    <input type="<?= $field['type'] ?>" id="<?= $key ?>"
                           name="settings[<?= $key ?>]"
                           value="<?= $h($settings[$key] ?? '') ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <button type="button" id="smtp-test-btn" class="btn btn-sm" onclick="testSmtp()" style="margin-top:0.5rem;"><?= te('admin.settings.smtp_test_btn') ?></button>
            <span id="smtp-test-result" style="margin-left:0.5rem;"></span>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" name="save_settings" value="1" class="btn btn-primary"><?= te('common.save') ?></button>
    </div>
</form>

<script>
function testSmtp() {
    var btn = document.getElementById('smtp-test-btn');
    var res = document.getElementById('smtp-test-result');
    btn.disabled = true;
    res.textContent = <?= json_encode(t('admin.settings.smtp_sending')) ?>;
    res.style.color = '#666';
    fetch('/admin/api.php?action=test_smtp', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            csrf_token: document.querySelector('input[name=csrf_token]').value
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.disabled = false;
        if (data.ok) {
            res.textContent = '✅ ' + data.message;
            res.style.color = 'green';
        } else {
            res.textContent = '❌ ' + data.message;
            res.style.color = 'red';
        }
    })
    .catch(function() {
        btn.disabled = false;
        res.textContent = '❌ ' + <?= json_encode(t('admin.settings.smtp_network_error')) ?>;
        res.style.color = 'red';
    });
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
