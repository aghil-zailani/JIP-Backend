<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\InspeksiStnk;
use App\Models\InspeksiBpkb;
use App\Models\InspeksiDokumenLain;
use App\Models\Order;

class DokumenController extends Controller
{
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
        $order = Order::with('dokumen')->findOrFail($order_id);

        return response()->json([
            'success' => true,
            'data' => $order->dokumen
        ]);
    }
}
