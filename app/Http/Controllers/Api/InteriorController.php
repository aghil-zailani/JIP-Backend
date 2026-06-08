<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use App\Models\HasilInspeksiDetail;
use App\Models\Order;
use App\Models\ItemInspeksi;
use App\Models\FotoKerusakan;

class InteriorController extends Controller
{
    public function simpanHasilItem(Request $request, $order_id, $item_id)
    {
        $request->validate([
            'status_kondisi'   => 'required|string',
            'catatan'          => 'nullable|string',
            'is_draft'         => 'nullable|in:0,1',
            'foto_utama'       => 'nullable|array',
            'foto_utama.*'     => 'nullable|image|max:5120',
            'foto_tambahan'    => 'nullable|array',
            'foto_tambahan.*'  => 'nullable|image|max:5120',
        ]);

        $order = Order::findOrFail($order_id);
        
        $hasilLama = HasilInspeksiDetail::where('order_id', $order_id)
                        ->where('item_inspeksi_id', $item_id)
                        ->first();

        $dataUpdate = [
            'status_kondisi' => str_replace(' ', '_', strtolower(trim($request->status_kondisi ?? 'normal'))),
            'catatan'        => $request->catatan,
            'is_draft'       => $request->input('is_draft', true),
        ];
                
        $imageManager = new ImageManager(new Driver());

        if ($request->hasFile('foto_utama')) {

            $arrayFoto = $request->file('foto_utama');
            // Pastikan selalu array
            if (!is_array($arrayFoto)) {
                $arrayFoto = [$arrayFoto];
            }

            // Hapus semua foto utama lama
            if ($hasilLama && !empty($hasilLama->foto_utama)) {
                foreach ($hasilLama->foto_utama as $oldFoto) {
                    $oldPath = str_replace('/storage/', '', $oldFoto);
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $savedPaths = [];
            foreach ($arrayFoto as $index => $file) {
                if (!$file || !$file->isValid()) continue;

                $filename = time() . '_utama_' . $index . '_order' . $order_id . '_item' . $item_id . '.jpg';
                $path = 'inspeksi/item/' . $filename;

                $image = $imageManager->decodePath($file->getRealPath());
                $image->scaleDown(width: 800);
                Storage::disk('public')->put($path, $image->encode(new JpegEncoder(quality: 75)));

                $savedPaths[] = '/storage/' . $path;
            }

            $dataUpdate['foto_utama'] = $savedPaths;
        }
                
        $hasil = HasilInspeksiDetail::updateOrCreate(
            ['order_id' => $order_id, 'item_inspeksi_id' => $item_id],
            $dataUpdate
        );
                
        if ($request->hasFile('foto_tambahan')) {
            $arrayFotoTambahan = $request->file('foto_tambahan');
            // Pastikan selalu array
            if (!is_array($arrayFotoTambahan)) {
                $arrayFotoTambahan = [$arrayFotoTambahan];
            }

            // Hapus foto kerusakan lama
            if ($hasilLama) {
                $fotoLamaList = FotoKerusakan::where('hasil_inspeksi_detail_id', $hasilLama->id)->get();
                foreach ($fotoLamaList as $fotoLama) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $fotoLama->path_foto));
                    $fotoLama->delete();
                }
            }

            foreach ($arrayFotoTambahan as $index => $fileTambahan) {
                if (!$fileTambahan || !$fileTambahan->isValid()) continue;

                $filenameTambahan = time() . '_tambahan_' . $index . '_order' . $order_id . '_item' . $item_id . '.jpg';
                $pathTambahan = 'inspeksi/kerusakan/' . $filenameTambahan;
                $imgTambahan = $imageManager->decodePath($fileTambahan->getRealPath());
                $imgTambahan->scaleDown(width: 800);
                Storage::disk('public')->put($pathTambahan, $imgTambahan->encode(new JpegEncoder(quality: 75)));

                FotoKerusakan::create([
                    'hasil_inspeksi_detail_id' => $hasil->id,
                    'path_foto'                => '/storage/' . $pathTambahan,
                ]);
            }
        }
        
        $hasil->load('fotoKerusakans');

        $pesan = $request->input('is_draft', true) 
                    ? 'Draft item berhasil disimpan.' 
                    : 'Data item berhasil diselesaikan dan difinalisasi.';

        return response()->json([
            'success' => true,
            'message' => $pesan,
            'data' => $hasil
        ], 200);
    }

    /**
     * Simpan hasil inspeksi item dengan foto langsung ke folder public/Photo (tanpa storage:link).
     * Struktur folder:
     *   public/Photo/inspeksi/{order_id}/{item_id}/utama/
     *   public/Photo/inspeksi/{order_id}/{item_id}/kerusakan/
     * 
     * Endpoint: POST /api/tugas/{order_id}/inspeksi-photo/{item_id}
     */
    public function simpanHasilItemPhoto(Request $request, $order_id, $item_id)
    {
        $request->validate([
            'status_kondisi'   => 'required|string',
            'catatan'          => 'nullable|string',
            'is_draft'         => 'nullable|in:0,1',
            'foto_utama'       => 'nullable|array',
            'foto_utama.*'     => 'nullable|image|max:5120',
            'foto_tambahan'    => 'nullable|array',
            'foto_tambahan.*'  => 'nullable|image|max:5120',
        ]);

        $order = Order::findOrFail($order_id);

        $hasilLama = HasilInspeksiDetail::where('order_id', $order_id)
                        ->where('item_inspeksi_id', $item_id)
                        ->first();

        $dataUpdate = [
            'status_kondisi' => str_replace(' ', '_', strtolower(trim($request->status_kondisi ?? 'normal'))),
            'catatan'        => $request->catatan,
            'is_draft'       => $request->input('is_draft', true),
        ];

        $imageManager = new ImageManager(new Driver());

        // ─── Foto Utama ───────────────────────────────────────
        if ($request->hasFile('foto_utama')) {
            $arrayFoto = $request->file('foto_utama');
            if (!is_array($arrayFoto)) {
                $arrayFoto = [$arrayFoto];
            }

            $dirUtama = public_path('Photo/inspeksi/' . $order_id . '/' . $item_id . '/utama');
            if (!File::isDirectory($dirUtama)) {
                File::makeDirectory($dirUtama, 0755, true);
            }

            // Hapus foto utama lama
            if ($hasilLama && !empty($hasilLama->foto_utama)) {
                foreach ($hasilLama->foto_utama as $oldFoto) {
                    $oldFile = public_path(ltrim($oldFoto, '/'));
                    if (File::exists($oldFile)) {
                        File::delete($oldFile);
                    }
                }
            }

            $savedPaths = [];
            foreach ($arrayFoto as $index => $file) {
                if (!$file || !$file->isValid()) continue;

                $filename = time() . '_utama_' . $index . '_order' . $order_id . '_item' . $item_id . '.jpg';

                $image = $imageManager->decodePath($file->getRealPath());
                $image->scaleDown(width: 800);
                $encoded = $image->encode(new JpegEncoder(quality: 75));

                file_put_contents($dirUtama . '/' . $filename, (string) $encoded);

                $savedPaths[] = '/Photo/inspeksi/' . $order_id . '/' . $item_id . '/utama/' . $filename;
            }

            $dataUpdate['foto_utama'] = $savedPaths;
        }

        // ─── Simpan / Update record ───────────────────────────
        $hasil = HasilInspeksiDetail::updateOrCreate(
            ['order_id' => $order_id, 'item_inspeksi_id' => $item_id],
            $dataUpdate
        );

        // ─── Foto Tambahan (Kerusakan) ────────────────────────
        if ($request->hasFile('foto_tambahan')) {
            $arrayFotoTambahan = $request->file('foto_tambahan');
            if (!is_array($arrayFotoTambahan)) {
                $arrayFotoTambahan = [$arrayFotoTambahan];
            }

            $dirKerusakan = public_path('Photo/inspeksi/' . $order_id . '/' . $item_id . '/kerusakan');
            if (!File::isDirectory($dirKerusakan)) {
                File::makeDirectory($dirKerusakan, 0755, true);
            }

            // Hapus foto kerusakan lama
            if ($hasilLama) {
                $fotoLamaList = FotoKerusakan::where('hasil_inspeksi_detail_id', $hasilLama->id)->get();
                foreach ($fotoLamaList as $fotoLama) {
                    $oldFile = public_path(ltrim($fotoLama->path_foto, '/'));
                    if (File::exists($oldFile)) {
                        File::delete($oldFile);
                    }
                    $fotoLama->delete();
                }
            }

            foreach ($arrayFotoTambahan as $index => $fileTambahan) {
                if (!$fileTambahan || !$fileTambahan->isValid()) continue;

                $filenameTambahan = time() . '_tambahan_' . $index . '_order' . $order_id . '_item' . $item_id . '.jpg';

                $imgTambahan = $imageManager->decodePath($fileTambahan->getRealPath());
                $imgTambahan->scaleDown(width: 800);
                $encodedTambahan = $imgTambahan->encode(new JpegEncoder(quality: 75));

                file_put_contents($dirKerusakan . '/' . $filenameTambahan, (string) $encodedTambahan);

                FotoKerusakan::create([
                    'hasil_inspeksi_detail_id' => $hasil->id,
                    'path_foto'                => '/Photo/inspeksi/' . $order_id . '/' . $item_id . '/kerusakan/' . $filenameTambahan,
                ]);
            }
        }

        $hasil->load('fotoKerusakans');

        $pesan = $request->input('is_draft', true)
                    ? 'Draft item berhasil disimpan ke Photo.'
                    : 'Data item berhasil diselesaikan dan difinalisasi ke Photo.';

        return response()->json([
            'success' => true,
            'message' => $pesan,
            'data' => $hasil
        ], 200);
    }
}