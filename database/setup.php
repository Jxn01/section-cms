<?php
// ═══════════════════════════════════════════════════
// Database Setup Script
// Upload to the server and visit in browser ONCE,
// then DELETE this file for security.
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

// 2. Run seed data
$seed = file_get_contents(__DIR__ . '/002_seed.sql');
try {
    $pdo->exec($seed);
    $messages[] = '✅ Seed data inserted successfully.';
} catch (PDOException $e) {
    $messages[] = '⚠️ Seed error (may already exist): ' . $e->getMessage();
}

// 3. Create admin user
$adminUser = 'admin';
$adminPass = 'admin123'; // CHANGE THIS after first login!

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
    $messages[] = "✅ Admin user created (username: {$adminUser}, password: {$adminPass}).";
} catch (PDOException $e) {
    $messages[] = '❌ Admin user error: ' . $e->getMessage();
}

// Output results
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Parkoló ABC – DB Setup</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 4rem auto; padding: 2rem; }
        h1 { margin-bottom: 1rem; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
        .warning { margin-top: 2rem; padding: 1rem; background: #FEF3C7; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>Parkoló ABC – Database Setup</h1>
    <ul>
        <?php foreach ($messages as $msg): ?>
            <li><?= $msg ?></li>
        <?php endforeach; ?>
    </ul>
    <div class="warning">
        <strong>⚠️ FONTOS:</strong> Törölje ezt a fájlt a szerverről a telepítés után!<br>
        Admin belépés: <a href="/admin/">/admin/</a><br>
        Felhasználó: <code><?= $adminUser ?></code> / Jelszó: <code><?= $adminPass ?></code>
    </div>
</body>
</html>
