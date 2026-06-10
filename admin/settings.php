<?php
// ─── Admin — Site Settings ───

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

$fields = [
    'site_name'        => ['label' => 'Weboldal neve',       'type' => 'text',     'hint' => 'Megjelenik a fejlécben, láblécben, böngésző fülön és a Google találatokban. Ez a weboldal „márkaneve".'],
    'site_tagline'     => ['label' => 'Szlogen',             'type' => 'text',     'hint' => 'Rövid mottó, ami a weboldal neve mellett jelenhet meg. Pl. „Megbízható parkolási megoldások".'],
    'meta_description' => ['label' => 'Alapértelmezett meta leírás', 'type' => 'textarea', 'hint' => 'Ez a leírás jelenik meg a Google találatoknál, ha egy oldalnak nincs saját meta leírása. Max. 160 karakter ajánlott.'],
    'meta_keywords'    => ['label' => 'Alapértelmezett kulcsszavak',  'type' => 'text',  'hint' => 'Vesszővel elválasztott kulcsszavak, amelyek az oldalak alapértelmezett kulcsszavai lesznek, ha nem adnak meg sajátot.'],
    'contact_email'    => ['label' => 'E-mail cím',          'type' => 'email',    'hint' => 'A weboldalon megjelenő kapcsolattartási e-mail. A kapcsolati űrlap ide küld értesítést.'],
    'contact_phone'    => ['label' => 'Telefonszám',         'type' => 'text',     'hint' => 'A fejlécben és láblécben megjelenő telefonszám. Formátum: +36 1 234 5678.'],
    'contact_address'  => ['label' => 'Cím',                 'type' => 'text',     'hint' => 'A cég fizikai címe. Megjelenik a láblécben és a Google strukturált adatokban.'],
    'primary_color'    => ['label' => 'Elsődleges szín',     'type' => 'color',    'hint' => 'A weboldal fő színe: gombok, linkek, kiemelések. A weoldal arculatát határozza meg.'],
    'secondary_color'  => ['label' => 'Másodlagos szín',     'type' => 'color',    'hint' => 'Kiegészítő szín háttérelemekhez és másodlagos kiemelésekhez.'],
    'facebook_url'     => ['label' => 'Facebook URL',        'type' => 'url',      'hint' => 'A cég Facebook oldalának teljes URL-je. Megjelenik a láblécben és a JSON-LD strukturált adatokban.'],
    'og_default_image' => ['label' => 'Alapértelmezett OG kép', 'type' => 'image',  'hint' => 'Ez a kép jelenik meg, ha valaki megosztja az oldalt Facebookon vagy más közösségi médiában, és nincs saját kiemelt kép beállítva.'],
    'logo_url'         => ['label' => 'Logo URL',             'type' => 'image',  'hint' => 'A cég logójának URL-je. Megjelenik a fejlécben a weboldal neve mellett vagy helyett (a megjelenítési mód lentebb állítható). Töltsön fel egy logó képet a Média oldalon, majd válassza ki a „Tallózás" gombbal.'],
    'logo_display_mode' => ['label' => 'Logo megjelenítés módja', 'type' => 'select', 'hint' => 'Határozza meg, hogyan jelenjen meg a logo a fejlécben. „Nincs" = csak a weboldal neve jelenik meg. „Logo helyettesíti a nevet" = csak a logókép jelenik meg. „Logo a név mellett" = a logókép és a weboldal neve egymás mellett.', 'options' => ['none' => 'Nincs (csak a weboldal neve)', 'replace' => 'Logo helyettesíti a nevet', 'beside' => 'Logo a név mellett']],
];

// ─── SMTP / E-mail beállítások ───
$smtpFields = [
    'smtp_host' => ['label' => 'SMTP szerver',       'type' => 'text',     'hint' => 'Az SMTP szerver címe, pl. smtp.rackhost.hu. Ezt a tárhelyszolgáltatótól kapja.'],
    'smtp_port' => ['label' => 'SMTP port',           'type' => 'number',   'hint' => 'Az SMTP port száma. Általában 587 (STARTTLS) vagy 465 (SSL). Alapértelmezett: 587.'],
    'smtp_user' => ['label' => 'SMTP felhasználónév', 'type' => 'email',    'hint' => 'Az e-mail fiók felhasználóneve (általában maga az e-mail cím), pl. info@parkoloabc.hu.'],
    'smtp_pass' => ['label' => 'SMTP jelszó',         'type' => 'password', 'hint' => 'Az e-mail fiók jelszava. Biztonságosan tárolva az adatbázisban.'],
    'smtp_from' => ['label' => 'Feladó e-mail cím',   'type' => 'email',    'hint' => 'A „Feladó" mező értéke a küldött e-mailekben. Ha üres, az SMTP felhasználónév lesz használva.'],
    'smtp_to'   => ['label' => 'Értesítési e-mail',   'type' => 'email',    'hint' => 'Erre az e-mail címre érkeznek az űrlap-értesítések. Ha üres, az SMTP felhasználónév lesz használva.'],
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

<h1>Beállítások</h1>

<div class="help-box">
    <strong>⚙️ Weboldal globális beállítások.</strong> Ezek az értékek az egész weboldalra hatással vannak: fejléc, lábléc, Google keresési eredmények, közösségi média megosztás és az arculat. A módosítások a mentés után azonnal érvénybe lépnek.
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
    <div class="alert alert-success">Beállítások mentve.</div>
<?php endif; ?>

<form method="POST" class="admin-form">
    <?= csrfField() ?>
    <fieldset>
        <legend>Általános</legend>
        <?php foreach ($fields as $key => $field): ?>
            <div class="form-group">
                <label for="<?= $key ?>"><?= $h($field['label']) ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble"><?= $h($field['hint']) ?></span></span>
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
                        <button type="button" class="btn btn-sm browse-media-btn" data-target="<?= $key ?>">Tallózás</button>
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
        <legend>📧 E-mail értesítések (SMTP)</legend>
        <div class="help-box" style="margin-bottom:1rem;">
            Az űrlapon keresztül érkező üzenetek automatikusan mentésre kerülnek az adatbázisba. Ha szeretne e-mail értesítést is kapni, töltse ki az alábbi SMTP beállításokat. A tárhelyszolgáltatónál (rackhost.hu) először hozzon létre egy e-mail fiókot a parkoloabc.hu domainhez.
        </div>
        <?php foreach ($smtpFields as $key => $field): ?>
            <div class="form-group">
                <label for="<?= $key ?>"><?= $h($field['label']) ?>
                    <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble"><?= $h($field['hint']) ?></span></span>
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
            <button type="button" id="smtp-test-btn" class="btn btn-sm" onclick="testSmtp()" style="margin-top:0.5rem;">📧 Teszt e-mail küldése</button>
            <span id="smtp-test-result" style="margin-left:0.5rem;"></span>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" name="save_settings" value="1" class="btn btn-primary">Mentés</button>
    </div>
</form>

<script>
function testSmtp() {
    var btn = document.getElementById('smtp-test-btn');
    var res = document.getElementById('smtp-test-result');
    btn.disabled = true;
    res.textContent = 'Küldés...';
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
        res.textContent = '❌ Hálózati hiba';
        res.style.color = 'red';
    });
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
