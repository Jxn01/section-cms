<?php
// ═══════════════════════════════════════════════════
// V2 Migration Script
// Upload to the server and visit in browser ONCE,
// then DELETE this file for security.
// ═══════════════════════════════════════════════════

require_once __DIR__ . '/../config/db.php';

$messages = [];

$migration = file_get_contents(__DIR__ . '/003_migration_v2.sql');

// Strip comment-only lines first, then split by semicolons
$lines = explode("\n", $migration);
$cleanLines = array_filter($lines, fn($l) => !str_starts_with(trim($l), '--'));
$cleanSql = implode("\n", $cleanLines);

$statements = array_filter(
    array_map('trim', explode(';', $cleanSql)),
    fn($s) => $s !== ''
);

foreach ($statements as $sql) {
    try {
        $pdo->exec($sql);
        $short = substr(preg_replace('/\s+/', ' ', $sql), 0, 80);
        $messages[] = "✅ {$short}...";
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        // Ignore "duplicate column" errors (already migrated)
        if (str_contains($msg, 'Duplicate column')) {
            $messages[] = "⚠️ Already exists (skipped): " . substr($sql, 0, 60);
        } else {
            $messages[] = "❌ Error: {$msg}";
        }
    }
}

// Ensure assets/uploads directory exists
$uploadsDir = dirname(__DIR__) . '/assets/uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
    $messages[] = '✅ Created assets/uploads directory.';
} else {
    $messages[] = '⚠️ assets/uploads already exists.';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Parkoló ABC – V2 Migration</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 3rem auto; padding: 1rem; }
        h1 { color: #0F172A; }
        .msg { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
        .warn { color: #92400E; }
        .err  { color: #DC2626; }
        .ok   { color: #059669; }
    </style>
</head>
<body>
    <h1>🔄 V2 Migration</h1>
    <?php foreach ($messages as $m): ?>
        <div class="msg <?= str_starts_with($m, '❌') ? 'err' : (str_starts_with($m, '⚠️') ? 'warn' : 'ok') ?>">
            <?= htmlspecialchars($m) ?>
        </div>
    <?php endforeach; ?>
    <p style="margin-top:2rem;color:#EF4444;font-weight:bold;">
        ⚠️ DELETE this file from the server now!
    </p>
</body>
</html>
