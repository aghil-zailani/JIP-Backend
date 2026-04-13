<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Mobil;
use App\Models\InformasiUmum;
use Illuminate\Support\Facades\Validator;

class TugasController extends Controller
{
    public function index()
    {
        $user = auth()->guard('api')->user();

        $tugas = Order::with('mobil') 
            ->where('user_id', $user->id)
            ->where('status_inspeksi', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $formatTugas = $tugas->map(function ($order) {
            return [
                'order_id' => $order->id,                
                'nama_pelanggan' => $order->nama_pelanggan, 
                'no_hp' => $order->no_hp_pelanggan,
                'lokasi_inspeksi' => $order->lokasi_inspeksi,                
                'paket_layanan' => $order->mobil->jenis_inspeksi ?? 'Standar',
                'tanggal_waktu' => $order->created_at->translatedFormat('d F Y | H:i'),
                'nama_mobil' => $order->mobil->nama_mobil ?? 'Menunggu Input',    
                'status_inspeksi' => $order->status_inspeksi,          
            ];
        });

        $countTugas = $tugas->count();

        return response()->json([
            'success' => true,
            'message' => 'Daftar tugas berhasil diambil',
            'jumlah_tugas' => $countTugas,
            'data' => $formatTugas            
        ], 200);
    }

    public function simpanInformasi(Request $request, $order_id)
    {        
        $validator = Validator::make($request->all(), [
            'nomor_polisi' => 'required|string',
            'tipe_mobil' => 'required|string',
            'transmisi' => 'required|string',
            'kapasitas_mesin' => 'required|numeric',
            'bahan_bakar' => 'required|string',
            'warna_mobil' => 'required|string',
            'jarak_tempuh' => 'required|numeric',
            'kondisi_tabrak' => 'required|string',
            'kondisi_banjir' => 'required|string',
            'catatan_tambahan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $order = Order::findOrFail($order_id);
        $mobil = InformasiUmum::updateOrCreate(
            ['mobil_id' => $order->mobil_id], 
            $request->all() 
        );        

        return response()->json([
            'success' => true,
            'message' => 'Draft Informasi Kendaraan berhasil diperbarui',
            'data' => $mobil
        ]);
    }

    public function selesaikanTugas($order_id)
    {
        $order = Order::findOrFail($order_id);
        
        if ($order->status_inspeksi === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Tugas ini sudah diselesaikan sebelumnya.'
            ], 400);
        }
        
        $order->update([
            'status_inspeksi' => 'selesai'
        ]);                                

        return response()->json([
            'success' => true,
            'message' => 'Laporan Inspeksi berhasil dikirim dan tugas diselesaikan!',
            'data' => $order
        ], 200);
    }
}