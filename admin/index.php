<?php
// ─── Admin Panel — Entry / Dashboard / Login ───

require_once __DIR__ . '/auth.php';

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    csrfVerify();
    logout();
}

// Handle login POST
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (attemptLogin($pdo, $username, $password)) {
        header('Location: /admin/');
        exit;
    }
    $loginError = 'Hibás felhasználónév vagy jelszó.';
}

// If not logged in, show login page
if (!isLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bejelentkezés – Admin</title>
        <meta name="robots" content="noindex, nofollow">
        <link rel="stylesheet" href="/admin/assets/admin.css">
    </head>
    <body class="login-page">
        <div class="login-box">
            <h1>Parkoló ABC</h1>
            <p class="login-subtitle">Adminisztráció</p>
            <?php if ($loginError): ?>
                <div class="alert alert-error"><?= htmlspecialchars($loginError) ?></div>
            <?php endif; ?>
            <form method="POST" action="/admin/">
                <div class="form-group">
                    <label for="username">Felhasználónév</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Jelszó</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login" value="1" class="btn btn-primary btn-full">Bejelentkezés</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ─── Dashboard ───
$pageCount    = $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$draftCount   = $pdo->query("SELECT COUNT(*) FROM pages WHERE status = 'draft'")->fetchColumn();
$articleCount = 0;
try { $articleCount = $pdo->query("SELECT COUNT(*) FROM pages WHERE page_type = 'article'")->fetchColumn(); } catch (Exception $e) {}
$sectionCount = $pdo->query("SELECT COUNT(*) FROM sections")->fetchColumn();
$mediaCount   = $pdo->query("SELECT COUNT(*) FROM media")->fetchColumn();
$msgCount     = 0;
try { $msgCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn(); } catch (Exception $e) {}

require __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <div class="welcome-banner" id="welcomeBanner">
        <button class="welcome-dismiss" onclick="this.parentElement.style.display='none'; localStorage.setItem('hideWelcome','1');" aria-label="Bezárás">✕</button>
        <h2>👋 Üdvözöljük a Vezérlőpulton!</h2>
        <p>Innen kezelheti weboldalának teljes tartalmát: oldalakat, menüt, képeket és beállításokat. Az alábbi összefoglaló mutatja a weboldal jelenlegi állapotát.</p>
    </div>
    <script>if(localStorage.getItem('hideWelcome')==='1'){document.getElementById('welcomeBanner').style.display='none';}</script>

    <h1>Vezérlőpult</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $pageCount ?></div>
            <div class="stat-label">Oldalak
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">Az összes létrehozott oldal száma (publikált és piszkozat egyaránt). Az oldalak a weboldal fő tartalmát képezik.</span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $articleCount ?></div>
            <div class="stat-label">Cikkek
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A „cikk" típusú oldalak száma. A cikkek speciális SEO jelölést kapnak (author, dátum) — ideálisak blogbejegyzésekhez vagy hírekhez.</span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $draftCount ?></div>
            <div class="stat-label">Piszkozatok
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A még nem publikált oldalak. Piszkozat állapotú oldalak nem jelennek meg a weboldalon és a Google sem látja őket.</span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $sectionCount ?></div>
            <div class="stat-label">Szekciók
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A szekciók az oldalak építőkockái (pl. hero kép, szöveg, kártyák, galéria, stb.). Minden oldal tetszőleges számú szekcióból áll.</span></span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $mediaCount ?></div>
            <div class="stat-label">Média
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A feltöltött képek száma. A képek a szekciókban használhatók (hero háttér, galéria, kép+szöveg, stb.).</span></span>
            </div>
        </div>
        <?php if ($msgCount > 0): ?>
        <div class="stat-card">
            <div class="stat-number" style="color:var(--admin-danger)"><?= $msgCount ?></div>
            <div class="stat-label">Új üzenetek
                <span class="tooltip-wrap"><button type="button" class="tooltip-trigger" aria-label="Segítség">?</button><span class="tooltip-bubble">A weboldalon kitöltött kapcsolati űrlapon érkezett, még nem olvasott üzenetek. Ne felejtse el rendszeresen megnézni!</span></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="tip-cards">
        <div class="tip-card">
            <span class="tip-icon">📄</span>
            <strong>Oldalak és szekciók</strong>
            <p>Minden oldal szekciókat tartalmaz (hero, szöveg, galéria, stb.). A szekciók sorrendje határozza meg az oldal felépítését — húzza felfelé/lefelé a kívánt sorrendbe.</p>
        </div>
        <div class="tip-card">
            <span class="tip-icon">🔍</span>
            <strong>SEO (keresőoptimalizálás)</strong>
            <p>Minden oldalnak van meta címe és leírása. Ezek jelennek meg a Google találatok között. Ügyeljen rá, hogy kitöltse őket egyedi, releváns szöveggel!</p>
        </div>
        <div class="tip-card">
            <span class="tip-icon">🖼️</span>
            <strong>Képek és alt szöveg</strong>
            <p>Töltsön fel képeket a Média menüben. Minden képnél adjon meg alt szöveget — ez fontos a Google képkereséshez és a látássérült felhasználóknak.</p>
        </div>
        <div class="tip-card">
            <span class="tip-icon">🧭</span>
            <strong>Navigáció</strong>
            <p>A Menü kezelésben állíthatja be, hogy milyen linkek jelenjenek meg a weboldal fejlécében. Legördülő almenüt is létrehozhat szülő menüpont beállításával.</p>
        </div>
    </div>

    <div class="quick-links">
        <h2>Gyors hivatkozások</h2>
        <ul>
            <li><a href="/admin/pages.php">📄 Oldalak kezelése</a> <span style="color:#94A3B8;font-size:0.8rem;">— oldalak létrehozása, szerkesztése, törlése</span></li>
            <li><a href="/admin/page-edit.php?new=1">➕ Új oldal létrehozása</a> <span style="color:#94A3B8;font-size:0.8rem;">— válasszon sablont és kezdjen el szerkeszteni</span></li>
            <li><a href="/admin/menus.php">🧭 Menü kezelése</a> <span style="color:#94A3B8;font-size:0.8rem;">— navigáció szerkesztése, sorrend, almenük</span></li>
            <li><a href="/admin/media.php">🖼️ Média kezelése</a> <span style="color:#94A3B8;font-size:0.8rem;">— képfeltöltés, alt szöveg, galéria képek</span></li>
            <li><a href="/admin/messages.php">✉️ Üzenetek <?php if ($msgCount > 0): ?><span class="badge badge-published"><?= $msgCount ?> új</span><?php endif; ?></a> <span style="color:#94A3B8;font-size:0.8rem;">— kapcsolati űrlap üzenetek</span></li>
            <li><a href="/admin/settings.php">⚙️ Beállítások</a> <span style="color:#94A3B8;font-size:0.8rem;">— weboldal neve, elérhetőségek, színek</span></li>
            <li><a href="/admin/password.php">🔑 Jelszó módosítása</a> <span style="color:#94A3B8;font-size:0.8rem;">— admin fiók biztonsága</span></li>
            <li><a href="/" target="_blank">🌐 Weboldal megtekintése ↗</a> <span style="color:#94A3B8;font-size:0.8rem;">— nyilvános oldal előnézete</span></li>
        </ul>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
