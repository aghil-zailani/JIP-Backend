<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\InspeksiStnk;
use App\Models\InspeksiBpkb;
use App\Models\InspeksiDokumenLain;
use App\Models\Order;
use App\Traits\ConvertFileBase64;

class DokumenController extends Controller
{
    use ConvertFileBase64;
    public function simpanDokumen(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        $stnkTersimpan = null;
        $bpkbTersimpan = null;
        $dokumenLainTersimpan = null;
        
        if ($request->has('nomor_rangka')) { 
            $stnkData = $request->only(['pajak_1_tahun', 'pajak_5_tahun', 'pkb', 'nomor_rangka', 'nomor_mesin']);
            
            if ($request->hasFile('foto_stnk')) {
                $file = $request->file('foto_stnk');
                $filename = time() . '_stnk_' . $order->mobil_id . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('dokumen/stnk', $filename, 'public'); 
                $stnkData['foto_stnk'] = '/storage/' . $path;
            }

            $stnkTersimpan = InspeksiStnk::updateOrCreate(
                ['mobil_id' => $order->mobil_id],
                $stnkData
            );
        }

        if ($request->has('nomor_bpkb')) {
            $bpkbData = $request->only([
                'nama_pemilik', 'nomor_bpkb', 'kepemilikan_mobil', 'sph', 
                'benang_pembatas', 'hologram_polri', 'faktur', 'nik', 'form_a'
            ]);

            for ($i = 1; $i <= 4; $i++) {
                $fotoKey = 'foto_bpkb_' . $i;
                if ($request->hasFile($fotoKey)) {
                    $file = $request->file($fotoKey);
                    $filename = time() . '_bpkb'.$i.'_' . $order->mobil_id . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('dokumen/bpkb', $filename, 'public');
                    $bpkbData[$fotoKey] = '/storage/' . $path;
                }
            }

            $bpkbTersimpan = InspeksiBpkb::updateOrCreate(
                ['mobil_id' => $order->mobil_id],
                $bpkbData
            );
        }

        if ($request->has('buku_service')) {
            $dokumenLainData = $request->only([
                'buku_service', 'buku_manual', 'cek_logo_scanner', 'kir', 'samsat_online'
            ]);

            $dokumenLainTersimpan = InspeksiDokumenLain::updateOrCreate(
                ['mobil_id' => $order->mobil_id],
                $dokumenLainData
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft Dokumen (STNK, BPKB, dll) berhasil disimpan',
            'status_tugas' => $order->status_inspeksi,
            'data' => [
                'stnk' => $stnkTersimpan,
                'bpkb' => $bpkbTersimpan,
                'dokumen_lain' => $dokumenLainTersimpan
            ]
        ]);
    }

    public function getDokumen($order_id)
    {
        $order = Order::with([
            'mobil.inspeksiStnk',
            'mobil.inspeksiBpkb',
            'mobil.inspeksiDokumenLain',
        ])->findOrFail($order_id);

        $mobil = $order->mobil;

        $stnk = $mobil->inspeksiStnk;
        if ($stnk && $stnk->foto_stnk) {
            $stnk->foto_stnk = $this->fileToBase64($stnk->foto_stnk);
        }
        
        $bpkb = $mobil->inspeksiBpkb;
        if ($bpkb) {
            for ($i = 1; $i <= 4; $i++) {
                $fotoKey = 'foto_bpkb_' . $i;
                if (!empty($bpkb->$fotoKey)) {
                    $bpkb->$fotoKey = $this->fileToBase64($bpkb->$fotoKey);
                }
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'stnk'         => $stnk,
                'bpkb'         => $bpkb,
                'dokumen_lain' => $mobil->inspeksiDokumenLain,
            ]
        ]);
    }

    /**
     * Simpan dokumen foto langsung ke folder public/Photo (tanpa storage:link).
     * Struktur folder:
     *   public/Photo/dokumen/{mobil_id}/stnk/
     *   public/Photo/dokumen/{mobil_id}/bpkb/
     * 
     * Endpoint: POST /api/tugas/{order_id}/dokumen-photo
     */
    public function simpanDokumenPhoto(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $mobilId = $order->mobil_id;

        $stnkTersimpan = null;
        $bpkbTersimpan = null;
        $dokumenLainTersimpan = null;

        // ─── STNK ─────────────────────────────────────────────
        if ($request->has('nomor_rangka')) {
            $stnkData = $request->only(['pajak_1_tahun', 'pajak_5_tahun', 'pkb', 'nomor_rangka', 'nomor_mesin']);

            if ($request->hasFile('foto_stnk')) {
                $file = $request->file('foto_stnk');
                $filename = time() . '_stnk_' . $mobilId . '.' . $file->getClientOriginalExtension();

                $dir = public_path('Photo/dokumen/' . $mobilId . '/stnk');
                if (!File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                // Hapus foto lama jika ada
                $stnkLama = InspeksiStnk::where('mobil_id', $mobilId)->first();
                if ($stnkLama && !empty($stnkLama->foto_stnk)) {
                    $oldFile = public_path(ltrim($stnkLama->foto_stnk, '/'));
                    if (File::exists($oldFile)) {
                        File::delete($oldFile);
                    }
                }

                $file->move($dir, $filename);
                $stnkData['foto_stnk'] = '/Photo/dokumen/' . $mobilId . '/stnk/' . $filename;
            }

            $stnkTersimpan = InspeksiStnk::updateOrCreate(
                ['mobil_id' => $mobilId],
                $stnkData
            );
        }

        // ─── BPKB ─────────────────────────────────────────────
        if ($request->has('nomor_bpkb')) {
            $bpkbData = $request->only([
                'nama_pemilik', 'nomor_bpkb', 'kepemilikan_mobil', 'sph',
                'benang_pembatas', 'hologram_polri', 'faktur', 'nik', 'form_a'
            ]);

            $dir = public_path('Photo/dokumen/' . $mobilId . '/bpkb');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Hapus foto BPKB lama
            $bpkbLama = InspeksiBpkb::where('mobil_id', $mobilId)->first();

            for ($i = 1; $i <= 4; $i++) {
                $fotoKey = 'foto_bpkb_' . $i;
                if ($request->hasFile($fotoKey)) {
                    // Hapus file lama
                    if ($bpkbLama && !empty($bpkbLama->$fotoKey)) {
                        $oldFile = public_path(ltrim($bpkbLama->$fotoKey, '/'));
                        if (File::exists($oldFile)) {
                            File::delete($oldFile);
                        }
                    }

                    $file = $request->file($fotoKey);
                    $filename = time() . '_bpkb' . $i . '_' . $mobilId . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $filename);
                    $bpkbData[$fotoKey] = '/Photo/dokumen/' . $mobilId . '/bpkb/' . $filename;
                }
            }

            $bpkbTersimpan = InspeksiBpkb::updateOrCreate(
                ['mobil_id' => $mobilId],
                $bpkbData
            );
        }

        // ─── Dokumen Lain ─────────────────────────────────────
        if ($request->has('buku_service')) {
            $dokumenLainData = $request->only([
                'buku_service', 'buku_manual', 'cek_logo_scanner', 'kir', 'samsat_online'
            ]);

            $dokumenLainTersimpan = InspeksiDokumenLain::updateOrCreate(
                ['mobil_id' => $mobilId],
                $dokumenLainData
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft Dokumen berhasil disimpan ke Photo',
            'status_tugas' => $order->status_inspeksi,
            'data' => [
                'stnk' => $stnkTersimpan,
                'bpkb' => $bpkbTersimpan,
                'dokumen_lain' => $dokumenLainTersimpan
            ]
        ]);
    }

    /**
     * Ambil data dokumen dengan foto sebagai base64 data URI.
     * Support path dari storage (/storage/...) maupun Photo (/Photo/...).
     * 
     * Endpoint: GET /api/tugas/{order_id}/dokumen-photo
     */
    public function getDokumenPhoto($order_id)
    {
        $order = Order::with([
            'mobil.inspeksiStnk',
            'mobil.inspeksiBpkb',
            'mobil.inspeksiDokumenLain',
        ])->findOrFail($order_id);

        $mobil = $order->mobil;

        // Convert foto ke base64
        $stnk = $mobil->inspeksiStnk;
        if ($stnk && $stnk->foto_stnk) {
            $stnk->foto_stnk = $this->fileToBase64($stnk->foto_stnk);
        }

        $bpkb = $mobil->inspeksiBpkb;
        if ($bpkb) {
            for ($i = 1; $i <= 4; $i++) {
                $fotoKey = 'foto_bpkb_' . $i;
                if (!empty($bpkb->$fotoKey)) {
                    $bpkb->$fotoKey = $this->fileToBase64($bpkb->$fotoKey);
                }
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'stnk'         => $stnk,
                'bpkb'         => $bpkb,
                'dokumen_lain' => $mobil->inspeksiDokumenLain,
            ]
        ]);
    }
}

