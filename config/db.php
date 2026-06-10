<?php
// ═══════════════════════════════════════════════════════════════
// Database Connection
// ═══════════════════════════════════════════════════════════════
// Reads credentials from the .env file in the project root (one
// level above the web root) and opens a PDO connection.
// ═══════════════════════════════════════════════════════════════

// Path to the .env file (kept outside version control — see .env.example).
$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die('Missing .env file. Copy .env.example to .env and fill in your credentials.');
}

$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$key, $value] = explode('=', $line, 2);
    $env[trim($key)] = trim($value);
}

$host = $env['DB_HOST'] ?? 'localhost';
$name = $env['DB_NAME'] ?? '';
$user = $env['DB_USER'] ?? '';
$pass = $env['DB_PASS'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Please try again later.');
}

// ─── Canonical site URL (single source of truth) ───
// Prefer the explicit SITE_URL from .env. If it is not set, derive it
// from the current request so the project runs on any domain without
// code changes. (CLI scripts should set SITE_URL in .env.)
if (!empty($env['SITE_URL'])) {
    $siteBaseUrl = $env['SITE_URL'];
} else {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) == 443)
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme     = $https ? 'https' : 'http';
    $hostHeader = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $siteBaseUrl = $scheme . '://' . $hostHeader;
}
define('SITE_BASE_URL', rtrim($siteBaseUrl, '/'));
