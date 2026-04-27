<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Laravel\Facades\Image;
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
                
        $imageManager = new ImageManager(new Driver());

        if ($request->hasFile('foto_utama')) {

            $arrayFoto = $request->file('foto_utama');

            if ($hasilLama && $hasilLama->foto_utama) {
                $oldPath = str_replace('/storage/', '', $hasilLama->foto_utama);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $arrayFoto[0];            
            $filename = time() . '_utama_order' . $order_id . '_item' . $item_id . '.jpg';
            $path = 'inspeksi/item/' . $filename; 
            
            $image = $imageManager->read($file->getRealPath());
            $image->scaleDown(width: 800);
            Storage::disk('public')->put($path, $image->toJpeg(75));

            $dataUpdate['foto_utama'] = '/storage/' . $path;
        }
                
        $hasil = HasilInspeksiDetail::updateOrCreate(
            ['order_id' => $order_id, 'item_inspeksi_id' => $item_id],
            $dataUpdate
        );
                
        if ($request->hasFile('foto') && count($request->file('foto')) > 1) {
            $arrayFoto = $request->file('foto');
                    
            if ($hasilLama) {
                $fotoLamaList = FotoKerusakan::where('hasil_inspeksi_detail_id', $hasilLama->id)->get();
                foreach ($fotoLamaList as $fotoLama) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $fotoLama->path_foto));
                    $fotoLama->delete();
                }
            }
        
            for ($i = 1; $i < count($arrayFoto); $i++) {
                $fileTambahan = $arrayFoto[$i];
                $filenameTambahan = time() . '_tambahan_' . $i . '_order' . $order_id . '_item' . $item_id . '.jpg';
                $pathTambahan = 'inspeksi/kerusakan/' . $filenameTambahan;            
                $imgTambahan = $imageManager->read($fileTambahan->getRealPath());
                $imgTambahan->scaleDown(width: 800);
                Storage::disk('public')->put($pathTambahan, $imgTambahan->toJpeg(75));
                                
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