<?php
// ═══════════════════════════════════════════════════
// Database Setup Script
// Upload to the server and open it in a browser ONCE,
// then DELETE the /database/ folder for security.
//
// Creates the schema, inserts the demo seed data and
// creates the first admin user. The admin password is
// taken from ADMIN_PASS in .env if set, otherwise a
// random one is generated and shown ONCE below.
// ═══════════════════════════════════════════════════

require_once __DIR__ . '/../config/db.php';

$messages = [];

// 1. Run schema
$schema = file_get_contents(__DIR__ . '/001_schema.sql');
try {
    $pdo->exec($schema);
    $messages[] = '✅ Schema created successfully.';
} catch (PDOException $e) {
    $messages[] = '❌ Schema error: ' . $e->getMessage();
}

// 2. Run seed data (optional demo content)
$seed = file_get_contents(__DIR__ . '/002_seed.sql');
try {
    $pdo->exec($seed);
    $messages[] = '✅ Seed data inserted successfully.';
} catch (PDOException $e) {
    $messages[] = '⚠️ Seed error (may already exist): ' . $e->getMessage();
}

// 3. Create the first admin user
$adminUser = $env['ADMIN_USER'] ?? 'admin';
$adminPass = $env['ADMIN_PASS'] ?? bin2hex(random_bytes(6)); // shown once below
$generated = empty($env['ADMIN_PASS']);

$hash = password_hash($adminPass, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password_hash, display_name)
         VALUES (:u, :h, :d)
         ON DUPLICATE KEY UPDATE password_hash = :h2"
    );
    $stmt->execute([
        'u'  => $adminUser,
        'h'  => $hash,
        'd'  => 'Admin',
        'h2' => $hash,
    ]);
    $messages[] = "✅ Admin user ready (username: {$adminUser}).";
} catch (PDOException $e) {
    $messages[] = '❌ Admin user error: ' . $e->getMessage();
}

// Output results
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Section CMS – Database Setup</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 4rem auto; padding: 2rem; }
        h1 { margin-bottom: 1rem; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
        code { background: #F1F5F9; padding: 0.1rem 0.35rem; border-radius: 4px; }
        .warning { margin-top: 2rem; padding: 1rem; background: #FEF3C7; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>Section CMS – Database Setup</h1>
    <ul>
        <?php foreach ($messages as $msg): ?>
            <li><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
    <div class="warning">
        <strong>⚠️ IMPORTANT:</strong> Delete the <code>/database/</code> folder from the server now that setup is complete.<br><br>
        Admin login: <a href="/admin/">/admin/</a><br>
        Username: <code><?= htmlspecialchars($adminUser, ENT_QUOTES, 'UTF-8') ?></code><br>
        <?php if ($generated): ?>
            Password: <code><?= htmlspecialchars($adminPass, ENT_QUOTES, 'UTF-8') ?></code>
            <em>(shown only once — copy it now, then change it after logging in)</em>
        <?php else: ?>
            Password: <em>as set in your <code>.env</code> (ADMIN_PASS)</em>
        <?php endif; ?>
    </div>
</body>
</html>
