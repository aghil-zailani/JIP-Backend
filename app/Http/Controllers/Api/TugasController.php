<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Mobil;
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
}