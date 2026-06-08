<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * Trait ConvertFileBase64
 * 
 * Menggantikan kebutuhan `php artisan storage:link` pada shared hosting.
 * File yang tersimpan di storage/app/public ATAU public/Photo 
 * akan dikonversi ke base64 data URI sehingga frontend bisa langsung render.
 * 
 * Support 2 jenis path:
 *   - /storage/...  → baca dari storage/app/public/
 *   - /Photo/...    → baca dari public/Photo/
 */
trait ConvertFileBase64
{
    /**
     * Konversi single file path ke base64 data URI.
     * Otomatis deteksi apakah file dari storage atau dari public/Photo.
     * 
     * @param string|null $path Path dari database (misal: /storage/inspeksi/item/xxx.jpg ATAU /Photo/dokumen/5/stnk/xxx.jpg)
     * @return string|null Base64 data URI atau null jika file tidak ada
     */
    protected function fileToBase64(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // ─── Path dari public/Photo/ ───────────────────────
        if ($this->isPhotoPath($path)) {
            $fullPath = public_path(ltrim($path, '/'));

            if (!File::exists($fullPath)) {
                return null;
            }

            $fileContent = File::get($fullPath);
            $mimeType = File::mimeType($fullPath);

            return 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
        }

        // ─── Path dari storage (lama) ──────────────────────
        $relativePath = $this->cleanStoragePath($path);

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
     * Cek apakah path mengarah ke folder public/Photo.
     * 
     * @param string $path
     * @return bool
     */
    protected function isPhotoPath(string $path): bool
    {
        return str_starts_with($path, '/Photo/') || str_starts_with($path, 'Photo/');
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
