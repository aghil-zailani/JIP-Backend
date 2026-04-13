<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HasilInspeksiDetail;
use App\Models\Order;
use App\Models\ItemInspeksi;
use App\Models\FotoKerusakan;

class InteriorController extends Controller
{
    public function simpanHasilItem(Request $request, $order_id, $item_id)
    {
        $order = Order::findOrFail($order_id);
        
        $dataUpdate = [
            'status_kondisi' => strtolower($request->kondisi ?? 'normal'),
            'catatan' => $request->catatan,
        ];
                
        if ($request->hasFile('foto_utama')) {
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

        return response()->json([
            'success' => true,
            'message' => 'Data item dan foto kerusakan berhasil disimpan',
            'data' => $hasil
        ], 200);
    }
}
