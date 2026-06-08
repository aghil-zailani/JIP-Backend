<?php
/**
 * SCRIPT SEMENTARA - Hapus setelah selesai!
 * Upload ke: public_html/public/fix_cache.php
 */

echo "<h2>🔧 Fix Laravel Cache & Test DB</h2><pre>";

// 1. Boot Laravel dengan benar
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot application FULLY
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "✅ Laravel berhasil di-boot\n\n";

// 2. Clear semua cache manual
echo "=== Clearing Caches ===\n";

// Config cache
$configCache = $app->getCachedConfigPath();
if (file_exists($configCache)) {
    @unlink($configCache);
    echo "✅ Config cache dihapus: $configCache\n";
} else {
    echo "ℹ️ Config cache tidak ada\n";
}

// Route cache
$routeCache = $app->getCachedRoutesPath();
if (file_exists($routeCache)) {
    @unlink($routeCache);
    echo "✅ Route cache dihapus\n";
} else {
    echo "ℹ️ Route cache tidak ada\n";
}

// Bootstrap cache files
foreach (glob($app->bootstrapPath('cache/*.php')) as $file) {
    @unlink($file);
    echo "🗑️ Deleted: " . basename($file) . "\n";
}

// Clear file cache if exists
$cachePath = storage_path('framework/cache/data');
if (is_dir($cachePath)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $count = 0;
    foreach ($files as $f) {
        if ($f->isFile() && $f->getFilename() !== '.gitignore') {
            @unlink($f->getRealPath());
            $count++;
        }
    }
    echo "🗑️ File cache: $count file(s) dihapus\n";
}

// OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

// 3. Tampilkan config yang Laravel pakai
echo "\n=== Config Laravel AKTIF ===\n";
echo "DB_HOST: " . config('database.connections.mysql.host') . "\n";
echo "DB_PORT: " . config('database.connections.mysql.port') . "\n";
echo "DB_DATABASE: " . config('database.connections.mysql.database') . "\n";
echo "DB_USERNAME: " . config('database.connections.mysql.username') . "\n";
echo "CACHE: " . config('cache.default') . "\n";
echo "SESSION: " . config('session.driver') . "\n";
echo "QUEUE: " . config('queue.default') . "\n";

// 4. Test koneksi DB via Laravel
echo "\n=== Test DB via Laravel ===\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "✅ KONEK! MySQL " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    
    $count = DB::table('users')->count();
    echo "✅ Query OK: $count user(s)\n";
} catch (Exception $e) {
    echo "❌ GAGAL: " . $e->getMessage() . "\n";
}

// 5. Test Storage
echo "\n=== Test Storage ===\n";
try {
    $testFile = 'test_' . time() . '.txt';
    Storage::disk('public')->put($testFile, 'ok');
    echo "✅ Write OK\n";
    Storage::disk('public')->delete($testFile);
    echo "✅ Delete OK\n";
    echo "📁 Storage path: " . storage_path('app/public') . "\n";
} catch (Exception $e) {
    echo "❌ Storage error: " . $e->getMessage() . "\n";
}

echo "</pre><hr><p style='color:red;font-weight:bold'>⚠️ HAPUS FILE INI SETELAH SELESAI!</p>";

$kernel->terminate($request, $response);
