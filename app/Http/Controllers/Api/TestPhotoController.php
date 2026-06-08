<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * TestPhotoController
 * 
 * Controller untuk menguji upload gambar dan simpan langsung ke folder public/Photo
 * tanpa menggunakan storage:link. File disimpan sebagai base64 lalu decode kembali.
 */
class TestPhotoController extends Controller
{
    /**
     * Test 1: Upload gambar via multipart/form-data → simpan ke public/Photo
     * 
     * Postman: POST /api/test-photo/upload
     * Body: form-data → key: photo (File)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        $file = $request->file('photo');

        // Baca file → encode ke base64
        $fileContent = file_get_contents($file->getRealPath());
        $base64 = base64_encode($fileContent);
        $mimeType = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        // Decode base64 kembali → simpan ke public/Photo
        $decoded = base64_decode($base64);
        $filename = 'test_' . time() . '.' . $extension;
        
        $photoDir = public_path('Photo');
        if (!File::isDirectory($photoDir)) {
            File::makeDirectory($photoDir, 0755, true);
        }

        $fullPath = $photoDir . '/' . $filename;
        file_put_contents($fullPath, $decoded);

        // URL bisa diakses langsung tanpa storage:link
        $publicUrl = url('Photo/' . $filename);

        return response()->json([
            'success' => true,
            'message' => 'Upload + base64 encode/decode berhasil!',
            'data' => [
                'filename'   => $filename,
                'mime_type'  => $mimeType,
                'size_original' => strlen($fileContent) . ' bytes',
                'size_base64'   => strlen($base64) . ' chars',
                'size_decoded'  => strlen($decoded) . ' bytes',
                'match'      => $fileContent === $decoded ? '✅ SAMA' : '❌ BEDA',
                'url'        => $publicUrl,
                'base64_preview' => 'data:' . $mimeType . ';base64,' . substr($base64, 0, 100) . '...',
            ]
        ]);
    }

    /**
     * Test 2: Kirim base64 string langsung via JSON → simpan ke public/Photo
     * 
     * Postman: POST /api/test-photo/base64
     * Body: raw JSON → { "photo_base64": "data:image/jpeg;base64,/9j/4AAQ..." }
     */
    public function uploadBase64(Request $request)
    {
        $request->validate([
            'photo_base64' => 'required|string',
        ]);

        $base64Input = $request->photo_base64;

        // Parse data URI: "data:image/jpeg;base64,/9j/4AAQ..."
        if (str_contains($base64Input, ',')) {
            $parts = explode(',', $base64Input, 2);
            $meta = $parts[0];      // "data:image/jpeg;base64"
            $base64Data = $parts[1]; // raw base64 string
            
            // Extract mime type
            preg_match('/data:(.*?);/', $meta, $matches);
            $mimeType = $matches[1] ?? 'image/jpeg';
        } else {
            $base64Data = $base64Input;
            $mimeType = 'image/jpeg';
        }

        // Decode base64 → simpan ke file
        $decoded = base64_decode($base64Data);
        if ($decoded === false) {
            return response()->json([
                'success' => false,
                'message' => 'Base64 string tidak valid!'
            ], 422);
        }

        // Tentukan extension dari mime type
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = $extensions[$mimeType] ?? 'jpg';
        $filename = 'base64_' . time() . '.' . $ext;

        $photoDir = public_path('Photo');
        if (!File::isDirectory($photoDir)) {
            File::makeDirectory($photoDir, 0755, true);
        }

        $fullPath = $photoDir . '/' . $filename;
        file_put_contents($fullPath, $decoded);

        $publicUrl = url('Photo/' . $filename);

        return response()->json([
            'success' => true,
            'message' => 'Base64 → File berhasil disimpan!',
            'data' => [
                'filename'  => $filename,
                'mime_type' => $mimeType,
                'file_size' => strlen($decoded) . ' bytes',
                'url'       => $publicUrl,
            ]
        ]);
    }

    /**
     * Test 3: List semua foto di folder public/Photo
     * 
     * Postman: GET /api/test-photo/list
     */
    public function list()
    {
        $photoDir = public_path('Photo');
        
        if (!File::isDirectory($photoDir)) {
            return response()->json([
                'success' => true,
                'message' => 'Folder Photo belum ada',
                'data' => []
            ]);
        }

        $files = File::files($photoDir);
        $photos = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $photos[] = [
                'filename' => $filename,
                'size'     => $file->getSize() . ' bytes',
                'url'      => url('Photo/' . $filename),
                'base64'   => 'data:' . mime_content_type($file->getRealPath()) . ';base64,' 
                              . base64_encode(file_get_contents($file->getRealPath())),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($photos) . ' foto ditemukan',
            'data' => $photos,
        ]);
    }
}
