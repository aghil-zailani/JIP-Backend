<?php
/**
 * SCRIPT SEMENTARA - Hapus setelah selesai debug!
 * Upload ke: public_html/public/debug_db.php
 * Akses via: https://jim.bigs.id/debug_db.php
 */

echo "<h2>🔍 Debug Database & Server Info</h2>";
echo "<pre>";

// 1. Clear config cache
$configCache = __DIR__ . '/../bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "✅ Config cache DIHAPUS\n\n";
} else {
    echo "ℹ️ Config cache tidak ada (sudah bersih)\n\n";
}

// 2. Clear route cache
$routeCache = __DIR__ . '/../bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "✅ Route cache DIHAPUS\n\n";
} else {
    echo "ℹ️ Route cache tidak ada\n\n";
}

// 3. Tampilkan info PHP & MySQL
echo "=== PHP Info ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "MySQL Extensions: " . (extension_loaded('pdo_mysql') ? '✅ PDO MySQL' : '❌ PDO MySQL MISSING') . "\n";
echo "GD Extension: " . (extension_loaded('gd') ? '✅ GD Loaded' : '❌ GD MISSING') . "\n";
echo "Max Upload: " . ini_get('upload_max_filesize') . "\n";
echo "Max POST: " . ini_get('post_max_size') . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n\n";

// 4. Baca .env untuk ambil DB config
$envFile = __DIR__ . '/../.env';
$dbHost = 'localhost';
$dbPort = '3306';
$dbName = '';
$dbUser = '';
$dbPass = '';
$dbSocket = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, 'DB_HOST=') === 0) $dbHost = trim(substr($line, 8));
        if (strpos($line, 'DB_PORT=') === 0) $dbPort = trim(substr($line, 8));
        if (strpos($line, 'DB_DATABASE=') === 0) $dbName = trim(substr($line, 12));
        if (strpos($line, 'DB_USERNAME=') === 0) $dbUser = trim(substr($line, 12));
        if (strpos($line, 'DB_PASSWORD=') === 0) $dbPass = trim(substr($line, 12));
        if (strpos($line, 'DB_SOCKET=') === 0) $dbSocket = trim(substr($line, 10));
    }
}

echo "=== DB Config dari .env ===\n";
echo "Host: $dbHost\n";
echo "Port: $dbPort\n";
echo "Database: $dbName\n";
echo "Username: $dbUser\n";
echo "Password: " . str_repeat('*', strlen($dbPass)) . "\n";
echo "Socket: " . ($dbSocket ?: '(tidak di-set)') . "\n\n";

// 5. Test koneksi database
echo "=== Test Koneksi Database ===\n";

// Test 1: localhost
try {
    $dsn = "mysql:host=localhost;port=$dbPort;dbname=$dbName";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_TIMEOUT => 5]);
    echo "✅ BERHASIL konek via localhost\n";
    $pdo = null;
} catch (Exception $e) {
    echo "❌ GAGAL via localhost: " . $e->getMessage() . "\n";
}

// Test 2: 127.0.0.1
try {
    $dsn = "mysql:host=127.0.0.1;port=$dbPort;dbname=$dbName";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_TIMEOUT => 5]);
    echo "✅ BERHASIL konek via 127.0.0.1\n";
    $pdo = null;
} catch (Exception $e) {
    echo "❌ GAGAL via 127.0.0.1: " . $e->getMessage() . "\n";
}

// Test 3: Unix socket (common Hostinger paths)
$socketPaths = [
    '/var/run/mysqld/mysqld.sock',
    '/var/lib/mysql/mysql.sock',
    '/tmp/mysql.sock',
];
foreach ($socketPaths as $sock) {
    if (file_exists($sock)) {
        try {
            $dsn = "mysql:unix_socket=$sock;dbname=$dbName";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_TIMEOUT => 5]);
            echo "✅ BERHASIL konek via socket: $sock\n";
            $pdo = null;
        } catch (Exception $e) {
            echo "❌ GAGAL via socket $sock: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Default Socket Path ===\n";
$defaultSocket = ini_get('pdo_mysql.default_socket') ?: ini_get('mysqli.default_socket') ?: '(tidak ter-set)';
echo "Default MySQL Socket: $defaultSocket\n";

echo "</pre>";
echo "<hr><p style='color:red;font-weight:bold'>⚠️ HAPUS FILE INI SETELAH SELESAI DEBUG!</p>";
