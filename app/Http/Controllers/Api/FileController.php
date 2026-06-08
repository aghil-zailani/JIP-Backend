<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ConvertFileBase64;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Endpoint khusus untuk serve file dari storage tanpa symlink.
 */
class FileController extends Controller
{
    use ConvertFileBase64;
    public function serveBase64(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $base64 = $this->fileToBase64($request->path);

        if (!$base64) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $base64,
        ]);
    }

    public function serveDirect(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relativePath = $this->cleanStoragePath($request->path);

        if (!Storage::disk('public')->exists($relativePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        $file = Storage::disk('public')->get($relativePath);
        $mimeType = Storage::disk('public')->mimeType($relativePath);

        return response($file, 200)->header('Content-Type', $mimeType);
    }
}
