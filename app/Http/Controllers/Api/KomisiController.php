<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Komisi;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Models\Mobil;

class KomisiController extends Controller
{
    public function index()
    {
        $user = auth()->guard('api')->user()->load('instansi');        

        $komisi = Komisi::with('order')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                // ->take(10)
                ->get();      
                        
        $komisi = $komisi->map(function ($item) {            
            return [
                'komisi_id' => $item->id,
                'nama_pelanggan' => $item->order ? $item->order->nama_pelanggan : 'Pelanggan',
                'email_pelanggan' => $item->order ? $item->order->email_pelanggan : '-',
                'no_hp_pelanggan' => $item->order ? $item->order->no_hp_pelanggan : '-',
                'lokasi_inspeksi' => $item->order ? $item->order->lokasi_inspeksi : '-',
                'order_id' => '#'.$item->order_id,
                'slip_komisi' => $item->nomor_slip ?? '-',
                'biaya_inspeksi' => $item->order ? 'Rp ' . number_format($item->order->biaya_inspeksi, 0, ',', '.') : 'Rp 0',
                'status' => $item->status == 'cair' ? 'Cair' : 'Proses',
                'waktu' => $item->order ? $item->order->created_at->format('d M Y H:i') : '-'
            ];
        });        

        return response()->json([
            'status' => 'success',
            'data' => $komisi,
            'instansi' => $user->instansi ? [
                'instansi_id' => $user->instansi->id,
                'nama_instansi' => $user->instansi->nama_instansi,
                'alamat_instansi' => $user->instansi->alamat,
            ] : null,            
        ]);
    }

    public function show($id)
    {        
        $user = auth()->guard('api')->user()->load('instansi');
        
        $komisi = Komisi::with('order')->find($id);
        
        if (!$komisi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Detail komisi tidak ditemukan'
            ], 404);
        }
        
        if ($komisi->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke data komisi ini'
            ], 403);
        }

        $order = Order::with('mobil')->find($komisi->order_id);
        $mobilInfo = $order && $order->mobil ? $order->mobil->nama_mobil . ' (' . $order->mobil->tahun_mobil . ')' : 'Mobil Tidak Diketahui';
                
        $dataKomisi = [
            'komisi_id' => $komisi->id,
            'order_id' => '#' . $komisi->order_id,
            'slip_komisi' => $komisi->nomor_slip ?? '-',
            'jumlah_pendapatan' => 'Rp ' . number_format($komisi->jumlah_pendapatan, 0, ',', '.'),
            'metode_bayar' => $komisi->metode_bayar ?? 'Transfer',
            'status' => $komisi->status == 'cair' ? 'Cair' : 'Proses',
            'waktu' => $komisi->order ? $komisi->order->created_at->format('d M Y H:i') : '-',            
            'nama_pelanggan' => $komisi->order ? $komisi->order->nama_pelanggan : '-',
            'email_pelanggan' => $komisi->order ? $komisi->order->email_pelanggan : '-',
            'no_hp_pelanggan' => $komisi->order ? $komisi->order->no_hp_pelanggan : '-',
            'biaya_inspeksi' => $komisi->order ? 'Rp ' . number_format($komisi->order->biaya_inspeksi, 0, ',', '.') : '-',
            'mobil_info' => $mobilInfo
        ];
        
        return response()->json([
            'status' => 'success',
            'data' => $dataKomisi,
            'instansi' => $user->instansi ? [
                'instansi_id' => $user->instansi->id,
                'nama_instansi' => $user->instansi->nama_instansi,
                'alamat' => $user->instansi->alamat                
            ] : null
        ]);
    }

    public function updatePembayaran(Request $request, $id)
    {
        $user = auth()->guard('api')->user();
        $komisi = Komisi::find($id);

        if (!$komisi) {
            return response()->json(['status' => 'error', 'message' => 'Data komisi tidak ditemukan'], 404);
        }

        $request->validate([
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_bukti_' . $komisi->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $komisi->bukti_pembayaran = '/storage/' . $path;
        }
        
        $komisi->update([
            'status' => 'cair',
            'metode_bayar' => $request->metode_pembayaran
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran komisi berhasil diselesaikan!',
            'data' => [
                'komisi_id' => $komisi->id,
                'status' => 'Selesai',
                'metode_pembayaran' => $komisi->metode_bayar,
                'bukti_pembayaran' => $komisi->bukti_pembayaran
            ]
        ], 200);
    }
}
