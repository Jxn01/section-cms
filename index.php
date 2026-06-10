<?php
// ═══════════════════════════════════════════════════════════════
// Front Controller
// ═══════════════════════════════════════════════════════════════
// Every public request lands here via the .htaccess rewrite. It
// resolves the slug from the URL, loads the page from the database
// and renders the complete HTML server-side.
// ═══════════════════════════════════════════════════════════════

require_once __DIR__ . '/config/db.php';    // Database connection ($pdo)
require_once __DIR__ . '/core/i18n.php';     // Translations / locale
require_once __DIR__ . '/core/router.php';   // URL → slug resolution
require_once __DIR__ . '/core/renderer.php'; // Section rendering
require_once __DIR__ . '/core/seo.php';      // Meta tags, JSON-LD

// Resolve the slug from the URL (e.g. "/services" → "services").
$slug = Router::resolve($_SERVER['REQUEST_URI']);

// Handle special routes (sitemap.xml, robots.txt).
if (Router::handleSpecialRoutes($pdo, $slug)) {
    exit;
}

// Load global data (settings + navigation menu) and set the locale.
$settings = Renderer::getSettings($pdo);
$menus    = Renderer::getMenus($pdo);
I18n::init($settings['site_language'] ?? 'en');

$siteName = $settings['site_name'] ?? 'Section CMS';

// ─── Keyword route (/keyword/{keyword}) ───
// The keyword cloud section links here.
if (strpos($slug, 'keyword/') === 0) {
    $keyword = urldecode(substr($slug, 8));
    if ($keyword !== '') {
        $matchingPages = Renderer::getPagesByKeyword($pdo, $keyword);
        require __DIR__ . '/templates/keyword_results.php';
        exit;
    }
}

// ─── Contact form submission ───
// Stores the message in the database + sends an optional email notification.
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

            // ─── Send email notification (PHPMailer SMTP) ───
            // SMTP config comes from site_settings first (editable in the
            // admin panel), then falls back to .env if those fields are blank.
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

                    $mail->setFrom($smtpFrom, $siteName);
                    $mail->addAddress($smtpTo);
                    $mail->addReplyTo($contactEmail, $contactName);

                    $h = fn($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                    $mail->isHTML(true);
                    $mail->Subject = t('site.contact.email_subject', ['preview' => mb_substr($contactMsg, 0, 60, 'UTF-8')]);
                    $mail->Body    = '<h2>' . $h(t('site.contact.email_heading')) . '</h2>'
                        . '<p><strong>' . $h(t('site.contact.email_name'))  . ':</strong> ' . $h($contactName)  . '</p>'
                        . '<p><strong>' . $h(t('site.contact.email_email')) . ':</strong> ' . $h($contactEmail) . '</p>'
                        . '<p><strong>' . $h(t('site.contact.email_phone')) . ':</strong> ' . $h($contactPhone) . '</p>'
                        . '<p><strong>' . $h(t('site.contact.email_page'))  . ':</strong> /' . $h($pageSlug)    . '</p>'
                        . '<hr><p>' . nl2br($h($contactMsg)) . '</p>';
                    $mail->AltBody = t('site.contact.email_heading') . "\n"
                        . t('site.contact.email_name')  . ": {$contactName}\n"
                        . t('site.contact.email_email') . ": {$contactEmail}\n"
                        . t('site.contact.email_phone') . ": {$contactPhone}\n"
                        . t('site.contact.email_page')  . ": /{$pageSlug}\n\n{$contactMsg}";

                    $mail->send();
                } catch (Exception $mailEx) {
                    // Email failed — silently ignore; the message is saved in DB.
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

// Look up the page by slug.
$page = Router::getPage($pdo, $slug);

// No published page with this slug → render the 404 page.
if (!$page) {
    http_response_code(404);
    $page = [
        'title'            => '404',
        'slug'             => '404',
        'meta_title'       => '404 – ' . $siteName,
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

// Build the SEO meta data (title, description, OG, JSON-LD).
$seo = SEO::buildMeta($page, $settings);

// Fetch the page's sections and render them to HTML.
// $pdo is required by dynamic sections (keyword cloud, page list, etc.).
$sections = Renderer::getSections($pdo, (int) $page['id']);
$bodyHtml = Renderer::renderAllSections($sections, $pdo);

// Assemble the complete HTML page and send it to the browser.
require __DIR__ . '/templates/base.php';
