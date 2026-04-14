<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\HasilInspeksiDetail;
use App\Models\Order;
use App\Models\ItemInspeksi;
use App\Models\FotoKerusakan;

class InteriorController extends Controller
{
    public function simpanHasilItem(Request $request, $order_id, $item_id)
    {
        $order = Order::findOrFail($order_id);
        
        $hasilLama = HasilInspeksiDetail::where('order_id', $order_id)
                        ->where('item_inspeksi_id', $item_id)
                        ->first();

        $dataUpdate = [
            'status_kondisi' => strtolower($request->kondisi ?? 'normal'),
            'catatan' => $request->catatan,
            'is_draft' => $request->input('is_draft', true) 
        ];
                
        if ($request->hasFile('foto_utama')) {
            if ($hasilLama && $hasilLama->foto_utama) {
                $oldPath = str_replace('/storage/', '', $hasilLama->foto_utama);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('foto_utama');            
            $filename = time() . '_utama_order' . $order_id . '_item' . $item_id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('inspeksi/item', $filename, 'public'); 
            $dataUpdate['foto_utama'] = '/storage/' . $path;
        }
                
        $hasil = HasilInspeksiDetail::updateOrCreate(
            ['order_id' => $order_id, 'item_inspeksi_id' => $item_id],
            $dataUpdate
        );
                
        if ($request->hasFile('foto_tambahan')) {
            
            if ($hasilLama) {
                $fotoLama = FotoKerusakan::where('hasil_inspeksi_detail_id', $hasilLama->id)->get();
                foreach ($fotoLama as $foto) {
                    $oldTambahanPath = str_replace('/storage/', '', $foto->path_foto);
                    Storage::disk('public')->delete($oldTambahanPath);
                    $foto->delete();
                }
            }

            foreach ($request->file('foto_tambahan') as $index => $fileTambahan) {
                $filenameTambahan = time() . '_rusak' . $index . '_order' . $order_id . '_item' . $item_id . '.' . $fileTambahan->getClientOriginalExtension();
                $pathTambahan = $fileTambahan->storeAs('inspeksi/kerusakan', $filenameTambahan, 'public');
                
                FotoKerusakan::create([
                    'hasil_inspeksi_detail_id' => $hasil->id,
                    'path_foto' => '/storage/' . $pathTambahan,
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
}