<?php
// ═══════════════════════════════════════════════════════════════
// Adatbázis kapcsolat (DB Connection)
// ═══════════════════════════════════════════════════════════════
// Beolvassa a hozzáférési adatokat a projekt gyökerében lévő
// .env fájlból, és létrehozza a PDO kapcsolatot.
// ═══════════════════════════════════════════════════════════════

// A .env fájl elérési útja (a webroot fölött egy szinttel)
$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die('Missing .env file.');
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

// ─── A weboldal alap URL-je (egyetlen forrásból származó igazság) ───
define('SITE_BASE_URL', 'https://www.parkoloabc.hu');
