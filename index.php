<?php
// ═══════════════════════════════════════════════════════════════
// Front Controller (Fő vezérlő)
// ═══════════════════════════════════════════════════════════════
// Minden nyilvános kérés ide érkezik a .htaccess átirányításon
// keresztül. Az URL-ből kinyeri a slug-ot, lekéri az oldal
// adatait az adatbázisból, és a teljes HTML-t rendereli.
// ═══════════════════════════════════════════════════════════════

require_once __DIR__ . '/config/db.php';   // Adatbázis kapcsolat ($pdo)
require_once __DIR__ . '/core/router.php';  // URL → slug feloldás
require_once __DIR__ . '/core/renderer.php';// Szekciók renderelése
require_once __DIR__ . '/core/seo.php';     // Meta tag-ek, JSON-LD

// Slug kinyerése az URL-ből (pl. "/szolgaltatasaink" → "szolgaltatasaink")
$slug = Router::resolve($_SERVER['REQUEST_URI']);

// Speciális útvonalak kezelése (sitemap.xml, robots.txt)
if (Router::handleSpecialRoutes($pdo, $slug)) {
    exit;
}

// Globális adatok betöltése (beállítások + navigációs menü)
$settings = Renderer::getSettings($pdo);
$menus    = Renderer::getMenus($pdo);

// ─── Kulcsszó útvonal kezelése (/kulcsszo/{keyword}) ───
// A kulcsszó felhő szekció linkjei ide mutatnak
if (strpos($slug, 'kulcsszo/') === 0) {
    $keyword = urldecode(substr($slug, 9));
    if ($keyword !== '') {
        $matchingPages = Renderer::getPagesByKeyword($pdo, $keyword);
        require __DIR__ . '/templates/keyword_results.php';
        exit;
    }
}

// ─── Kapcsolatfelvételi űrlap feldolgozása ───
// Az üzenet mentése adatbázisba + opcionális e-mail értesítés küldése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict']);
    $contactName  = trim($_POST['contact_name'] ?? '');
    $contactEmail = trim($_POST['contact_email'] ?? '');
    $contactPhone = trim($_POST['contact_phone'] ?? '');
    $contactMsg   = trim($_POST['contact_message'] ?? '');
    $pageSlug     = $slug;
    $csrfToken    = $_POST['csrf_token'] ?? '';

    // Verify CSRF token
    $validCsrf = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $csrfToken);

    if ($validCsrf && $contactName && $contactEmail && filter_var($contactEmail, FILTER_VALIDATE_EMAIL) && $contactMsg) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO contact_messages (sender_name, sender_email, sender_phone, message, page_slug)
                 VALUES (:name, :email, :phone, :msg, :slug)"
            );
            $stmt->execute([
                'name'  => $contactName,
                'email' => $contactEmail,
                'phone' => $contactPhone,
                'msg'   => $contactMsg,
                'slug'  => $pageSlug,
            ]);

            // ─── E-mail értesítés küldése (PHPMailer SMTP) ───
            // SMTP konfiguráció: először site_settings (admin felületen szerkeszthető),
            // majd .env fallback (ha az admin mezők üresek)
            $smtpHost = !empty($settings['smtp_host']) ? $settings['smtp_host'] : ($env['SMTP_HOST'] ?? '');
            $smtpUser = !empty($settings['smtp_user']) ? $settings['smtp_user'] : ($env['SMTP_USER'] ?? '');
            $smtpPass = !empty($settings['smtp_pass']) ? $settings['smtp_pass'] : ($env['SMTP_PASS'] ?? '');
            $smtpPort = (int)(!empty($settings['smtp_port']) ? $settings['smtp_port'] : ($env['SMTP_PORT'] ?? 587));
            $smtpFrom = !empty($settings['smtp_from']) ? $settings['smtp_from'] : (!empty($env['SMTP_FROM']) ? $env['SMTP_FROM'] : $smtpUser);
            $smtpTo   = !empty($settings['smtp_to'])   ? $settings['smtp_to']   : (!empty($env['SMTP_TO'])   ? $env['SMTP_TO']   : $smtpUser);

            if ($smtpHost && $smtpUser && $smtpPass && $smtpTo) {
                try {
                    require_once __DIR__ . '/lib/PHPMailer/PHPMailer.php';
                    require_once __DIR__ . '/lib/PHPMailer/SMTP.php';
                    require_once __DIR__ . '/lib/PHPMailer/Exception.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = $smtpHost;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtpUser;
                    $mail->Password   = $smtpPass;
                    $mail->Port       = $smtpPort;
                    $mail->CharSet    = 'UTF-8';
                    $mail->Timeout    = 15;

                    // Port 465 = implicit SSL (SMTPS), everything else = STARTTLS
                    if ($smtpPort === 465) {
                        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                    } else {
                        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    }

                    $mail->setFrom($smtpFrom, 'ParkolóABC.hu');
                    $mail->addAddress($smtpTo);
                    $mail->addReplyTo($contactEmail, $contactName);

                    $mail->isHTML(true);
                    $mail->Subject = 'Új üzenet: ' . mb_substr($contactMsg, 0, 60, 'UTF-8') . '…';
                    $mail->Body    = '<h2>Új kapcsolatfelvételi üzenet</h2>'
                        . '<p><strong>Név:</strong> '    . htmlspecialchars($contactName)  . '</p>'
                        . '<p><strong>E-mail:</strong> '  . htmlspecialchars($contactEmail) . '</p>'
                        . '<p><strong>Telefon:</strong> ' . htmlspecialchars($contactPhone) . '</p>'
                        . '<p><strong>Oldal:</strong> /'  . htmlspecialchars($pageSlug)     . '</p>'
                        . '<hr><p>' . nl2br(htmlspecialchars($contactMsg)) . '</p>';
                    $mail->AltBody = "Új üzenet\nNév: {$contactName}\nE-mail: {$contactEmail}\nTelefon: {$contactPhone}\nOldal: /{$pageSlug}\n\n{$contactMsg}";

                    $mail->send();
                } catch (Exception $mailEx) {
                    // Email failed — silently ignore; the message is saved in DB
                }
            }

            header('Location: /' . $slug . '?contact=ok#contact-form');
            exit;
        } catch (Exception $e) {
            header('Location: /' . $slug . '?contact=error#contact-form');
            exit;
        }
    }
    header('Location: /' . $slug . '?contact=error#contact-form');
    exit;
}

// Oldal keresése a slug alapján
$page = Router::getPage($pdo, $slug);

// Ha nincs ilyen publikált oldal → 404 hibaoldal megjelenítése
if (!$page) {
    http_response_code(404);
    $page = [
        'title'            => '404 – Az oldal nem található',
        'slug'             => '404',
        'meta_title'       => '404 – Az oldal nem található',
        'meta_description' => '',
        'meta_keywords'    => '',
        'og_title'         => '',
        'og_description'   => '',
        'og_image'         => '',
        'updated_at'       => date('Y-m-d H:i:s'),
    ];
    $seo      = SEO::buildMeta($page, $settings);
    $sections = [];
    $bodyHtml = '';
    require __DIR__ . '/templates/404.php';
    exit;
}

// SEO meta adatok összeállítása (title, description, OG, JSON-LD)
$seo = SEO::buildMeta($page, $settings);

// Szekciók lekérése és HTML-lé renderelése
// A $pdo szükséges a dinamikus szekciókhoz (kulcsszó felhő, oldal lista stb.)
$sections = Renderer::getSections($pdo, (int) $page['id']);
$bodyHtml = Renderer::renderAllSections($sections, $pdo);

// A teljes HTML oldal összeállítása és kiküldése a böngészőnek
require __DIR__ . '/templates/base.php';
