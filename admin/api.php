<?php
// ─── Admin — API Endpoints ───
// Handles AJAX requests for section reordering, etc.

require_once __DIR__ . '/auth.php';
requireLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // Reorder sections via drag-and-drop
    case 'reorder_sections':
        // Verify CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals(csrfToken(), $token)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }
        $order = $_POST['order'] ?? [];
        if (!is_array($order)) {
            echo json_encode(['error' => 'Invalid order data']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE sections SET sort_order = :ord WHERE id = :id");
        foreach ($order as $pos => $id) {
            $stmt->execute(['ord' => (int) $pos + 1, 'id' => (int) $id]);
        }
        echo json_encode(['success' => true]);
        break;

    // Send a test email using the saved SMTP settings
    case 'test_smtp':
        set_time_limit(30); // Prevent indefinite hang
        $input = json_decode(file_get_contents('php://input'), true);
        $token = $input['csrf_token'] ?? '';
        if (!hash_equals(csrfToken(), $token)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'message' => 'Érvénytelen CSRF token']);
            exit;
        }

        // Load SMTP settings from DB
        $stmtSmtp = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'smtp_%'");
        $smtp = [];
        foreach ($stmtSmtp->fetchAll() as $row) {
            $smtp[$row['setting_key']] = $row['setting_value'];
        }

        $host = $smtp['smtp_host'] ?? '';
        $user = $smtp['smtp_user'] ?? '';
        $pass = $smtp['smtp_pass'] ?? '';
        $port = (int)($smtp['smtp_port'] ?? 587);
        $from = $smtp['smtp_from'] ?: $user;
        $to   = $smtp['smtp_to']   ?: $user;

        if (!$host || !$user || !$pass || !$to) {
            echo json_encode(['ok' => false, 'message' => 'Hiányzó SMTP beállítások. Először mentse el az összes mezőt.']);
            exit;
        }

        try {
            require_once dirname(__DIR__) . '/lib/PHPMailer/PHPMailer.php';
            require_once dirname(__DIR__) . '/lib/PHPMailer/SMTP.php';
            require_once dirname(__DIR__) . '/lib/PHPMailer/Exception.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $user;
            $mail->Password   = $pass;
            $mail->Port       = $port;
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 15;

            // Port 465 = implicit SSL (SMTPS), everything else = STARTTLS
            if ($port === 465) {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->setFrom($from, 'ParkolóABC.hu');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = 'Teszt e-mail — ParkolóABC.hu';
            $mail->Body    = '<h2>✅ Ez egy teszt e-mail</h2><p>Ha ezt a levelet megkapta, az SMTP beállítások helyesek.</p><p><small>Küldve: ' . date('Y-m-d H:i:s') . '</small></p>';
            $mail->AltBody = "Teszt e-mail\nHa ezt a levelet megkapta, az SMTP beállítások helyesek.\nKüldve: " . date('Y-m-d H:i:s');

            $mail->send();
            echo json_encode(['ok' => true, 'message' => 'Teszt e-mail elküldve (' . htmlspecialchars($to) . ')']);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'message' => 'Küldési hiba: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
}
