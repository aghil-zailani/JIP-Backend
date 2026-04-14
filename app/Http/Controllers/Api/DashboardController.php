<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Komisi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->guard('api')->user();

        $totalTugas = Order::where('user_id', $user->id)->count();
        
        $inspeksiSelesai = Order::where('user_id', $user->id)
            ->where('status_inspeksi', 'selesai')
            ->count();
  
        $saldoKomisi = Komisi::where('user_id', $user->id)
            ->where('status', 'cair') 
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('jumlah_pendapatan');
       
        $riwayatOrders = Order::with(['mobil'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10) 
            ->get();
        
        $riwayatFormatted = $riwayatOrders->map(function ($order) {
            return [
                'order_id' => '#'.$order->id,
                'nama_pelanggan' => $order->nama_pelanggan ?? 'Pelanggan', 
                'no_hp_pelanggan' => $order->no_hp_pelanggan ?? '08xxxxxxxx',                                
                'info_layanan' => ($order->mobil ? $order->mobil->nama_mobil . ' (' . $order->mobil->tahun_mobil . ')' : 'Mobil Tidak Diketahui') . ' - ' . ($order->paket_layanan ?? 'Inspeksi Standar'),                                                
                'komisi_rp' => $order->biaya_inspeksi ? 'Rp ' . number_format($order->biaya_inspeksi, 0, ',', '.') : 'Rp 0',
                'metode_bayar' => 'Tunai',                                
                'status' => $order->status_inspeksi == 'selesai' ? 'Cair' : 'Proses', 
            ];
        });

        
        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                'header' => [
                    'nama_inspektor' => $user->name,
                    'saldo_komisi' => $saldoKomisi,
                ],
                'statistik' => [
                    'pendapatan_selesai' => $saldoKomisi,
                    'total_tugas' => $totalTugas,
                    'inspeksi_selesai' => $inspeksiSelesai,
                ],
                'riwayat_transaksi' => $riwayatFormatted
            ]
        ], 200);
    }
}