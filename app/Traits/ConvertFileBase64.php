<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * Trait ConvertFileBase64
 * 
 * Menggantikan kebutuhan `php artisan storage:link` pada shared hosting.
 * File yang tersimpan di storage/app/public akan dikonversi ke base64 data URI
 * sehingga frontend bisa langsung render tanpa perlu akses symlink.
 */
trait ConvertFileBase64
{
    /**
     * Konversi single file path ke base64 data URI.
     * 
     * @param string|null $storagePath Path dari database (misal: /storage/inspeksi/item/xxx.jpg)
     * @return string|null Base64 data URI atau null jika file tidak ada
     */
    protected function fileToBase64(?string $storagePath): ?string
    {
        if (empty($storagePath)) {
            return null;
        }

        // Bersihkan path: hapus prefix '/storage/' untuk mendapat path relatif di disk public
        $relativePath = $this->cleanStoragePath($storagePath);

        if (!Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        $fileContent = Storage::disk('public')->get($relativePath);
        $mimeType = Storage::disk('public')->mimeType($relativePath);

        return 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
    }

    /**
     * Konversi array of file paths ke array of base64 data URIs.
     * 
     * @param array|null $paths Array path dari database
     * @return array Array of base64 data URIs
     */
    protected function filesToBase64(?array $paths): array
    {
        if (empty($paths)) {
            return [];
        }

        return array_values(array_filter(
            array_map(fn($path) => $this->fileToBase64($path), $paths)
        ));
    }

    /**
     * Bersihkan storage path dari prefix '/storage/' 
     * agar menjadi path relatif terhadap disk public.
     * 
     * @param string $path
     * @return string
     */
    protected function cleanStoragePath(string $path): string
    {
        // Hapus prefix /storage/ 
        if (str_starts_with($path, '/storage/')) {
            return substr($path, 9); // strlen('/storage/') = 9
        }

        // Hapus prefix storage/
        if (str_starts_with($path, 'storage/')) {
            return substr($path, 8);
        }

        return $path;
    }
}
