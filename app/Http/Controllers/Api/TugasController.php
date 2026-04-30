<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Mobil;
use App\Models\InformasiUmum;
use Illuminate\Support\Facades\Validator;
use App\Models\Komisi;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TugasController extends Controller
{
    public function index()
    {
        $user = auth()->guard('api')->user();

        $tugas = Order::with(['mobil', 'komisi']) 
            ->where('user_id', $user->id)
            // ->where('status_inspeksi', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $formatTugas = $tugas->map(function ($order) {
            return [
                'id'=> $order->komisi->id ?? null,
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

        $slip = 'SLIP-' . now()->format('YmdHis') . rand(10, 99);
        
        $user = auth()->guard('api')->user();
        Komisi::create([
            'user_id'           => $user->id,
            'order_id'          => $order->id,
            'nomor_slip'        => $slip,
            'jumlah_pendapatan' => $order->biaya_inspeksi,
            'metode_bayar'      => '-',
            'status'            => 'pending',
        ]);        

        return response()->json([
            'success' => true,
            'message' => 'Laporan Inspeksi berhasil dikirim dan tugas diselesaikan!',
            'data'    => $order
        ], 200);
    }

    // public function selesaikanTugas($order_id)
    // {        
    //     $order = Order::with([
    //         'mobil.informasiUmum',
    //         'mobil.inspeksiStnk',
    //         'mobil.inspeksiBpkb',
    //         'mobil.inspeksiDokumenLain',
    //         'hasilInspeksiDetails'
    //     ])->findOrFail($order_id);
                
    //     if ($order->status_inspeksi === 'selesai') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Tugas ini sudah diselesaikan sebelumnya.'
    //         ], 400);
    //     }
                        
    //     $errors = [];
    //     $mobil = $order->mobil;
        
    //     if (!$mobil->informasiUmum) {
    //         $errors[] = 'Informasi Umum Kendaraan belum diisi.';
    //     }
    //     if (!$mobil->inspeksiStnk) {
    //         $errors[] = 'Dokumen STNK belum diperiksa / difoto.';
    //     }
    //     if (!$mobil->inspeksiBpkb) {
    //         $errors[] = 'Dokumen BPKB belum diperiksa / difoto.';
    //     }
                
    //     $requiredItemIds = \App\Models\ItemInspeksi::pluck('id')->toArray();
        
    //     $filledItemIds = $order->hasilInspeksiDetails->pluck('item_inspeksi_id')->toArray();
        
    //     $missingItemIds = array_diff($requiredItemIds, $filledItemIds);

    //     if (!empty($missingItemIds)) {
    //         $missingCount = count($missingItemIds);
    //         $errors[] = "Masih ada {$missingCount} titik inspeksi yang belum diperiksa.";
    //     }
        
    //     $itemTanpaKondisi = $order->hasilInspeksiDetails->whereNull('status_kondisi')->count();
    //     if ($itemTanpaKondisi > 0) {
    //         $errors[] = "Terdapat {$itemTanpaKondisi} titik inspeksi yang belum dipilih kondisinya (Normal/Rusak).";
    //     }

    //     $itemTanpaFoto = $order->hasilInspeksiDetails->whereNull('foto_utama')->count();
    //     if ($itemTanpaFoto > 0) {
    //         $errors[] = "Terdapat {$itemTanpaFoto} titik inspeksi yang belum dilengkapi dengan foto.";
    //     }

    //     if (count($errors) > 0) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menyelesaikan tugas. Masih ada data yang belum lengkap!',
    //             'errors' => $errors
    //         ], 400);
    //     }
        
    //     $order->update([
    //         'status_inspeksi' => 'selesai'
    //     ]); 

    //     $slip = 'SLIP-' . now()->format('YmdHis') . rand(10, 99);
        
    //     $user = auth()->guard('api')->user();
    //     Komisi::create([
    //         'user_id' => $user->id,
    //         'order_id' => $order->id,
    //         'nomor_slip' => $slip,
    //         'jumlah_pendapatan' => $order->biaya_inspeksi,
    //         'metode_bayar' => '-',
    //         'status' => 'pending',
    //     ]);        

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Laporan Inspeksi berhasil dikirim dan tugas diselesaikan!',
    //         'data' => $order
    //     ], 200);
    // }

    public function detailTugas($order_id)
    {
        $user = auth()->guard('api')->user();
        
        $komisi = Komisi::with([
            'order.mobil.inspeksiStnk', 
            'order.mobil.inspeksiBpkb', 
            'order.mobil.inspeksiDokumenLain',
            'order.mobil.informasiUmum',
            'order.hasilInspeksiDetails.itemInspeksi.kategoriInspeksi'
        ])->find($order_id);

        if (!$komisi) {
            return response()->json(['status' => 'error', 'message' => 'Data komisi tidak ditemukan'], 404);
        }

        $order = $komisi->order;
        $mobil = $order->mobil;

        $hasil_inspeksi = [];
        $total_titik = 0;
        $titik_normal = 0;
        $titik_tidak_normal = 0;

        foreach ($order->hasilInspeksiDetails as $detail) {
            $nama_kategori = $detail->itemInspeksi->kategoriInspeksi->nama_kategori;

            $total_titik++;
            if ($detail->status_kondisi === 'normal') {
                $titik_normal++;
            } else {
                $titik_tidak_normal++;
            }

            $hasil_inspeksi[$nama_kategori][] = [
                'nama_item'      => $detail->itemInspeksi->nama_item,
                'status_kondisi' => $detail->status_kondisi,
                'foto'           => !empty($detail->foto_utama)
                                        ? array_map(fn($path) => url($path), $detail->foto_utama)
                                        : [],
                'catatan'        => $detail->catatan
            ];
        }
    
        $dataLaporan = [
            'header' => [
                'nama_kendaraan' => $mobil->nama_mobil ?? '-',
                'spesifikasi' => ($mobil->tahun_mobil ?? '-') . ' • ' . ($mobil->informasiUmum->kapasitas_mesin ?? '-') . ' CC',
                'inspektor' => $user->name,
                'tanggal_inspeksi' => $order->updated_at->translatedFormat('d M Y'),
            ],
            'ringkasan_inspeksi' => [
                'total_titik_inspeksi' => $total_titik,
                'titik_normal' => $titik_normal,
                'titik_tidak_normal' => $titik_tidak_normal,
            ],
            'informasi_dokumen' => [
                // Info STNK
                'nomor_rangka' => $mobil->inspeksiStnk->nomor_rangka ?? '-',
                'nomor_mesin' => $mobil->inspeksiStnk->nomor_mesin ?? '-',
                'pajak_1_tahun' => $mobil->inspeksiStnk->pajak_1_tahun ?? '-',
                'pajak_5_tahun' => $mobil->inspeksiStnk->pajak_5_tahun ?? '-',
                'pkb' => $mobil->inspeksiStnk->pkb ? 'Rp ' . number_format($mobil->inspeksiStnk->pkb, 0, ',', '.') : '-',
                // Info BPKB
                'nama_pemilik_bpkb' => $mobil->inspeksiBpkb->nama_pemilik ?? '-',
                'nomor_bpkb' => $mobil->inspeksiBpkb->nomor_bpkb ?? '-',
                'kepemilikan' => $mobil->inspeksiBpkb->kepemilikan_mobil ?? '-',
                // Lainnya
                'buku_service' => $mobil->inspeksiDokumenLain->buku_service ?? '-',
            ],            
            'rincian_foto_inspeksi' => $hasil_inspeksi 
        ];

        return response()->json([
            'success' => true,
            'data' => $dataLaporan,
        ], 200);
    }    

    public function exportPDF($komisi_id)
    {        
        $user = auth()->guard('api')->user()->load('instansi');         
        $komisi = Komisi::with([
            'order.mobil.inspeksiStnk', 
            'order.mobil.inspeksiBpkb',
            'order.mobil.informasiUmum', 
            'order.hasilInspeksiDetails.itemInspeksi.kategoriInspeksi',
            'order.hasilInspeksiDetails.fotoKerusakans',
        ])->findOrFail($komisi_id);
        
        $order = $komisi->order;
        $mobil = $order->mobil;        
        $hasil_inspeksi    = [];
        $titik_normal      = 0;
        $titik_tidak_normal = 0;
        // Statistik per kategori: ['NamaKategori' => ['normal' => N, 'tidak_normal' => N]]
        $statistik_per_kategori = [];
        // Daftar item rusak untuk ringkasan: [['kategori' => '...', 'nama_item' => '...', 'status' => '...']]
        $item_rusak = [];

        foreach ($order->hasilInspeksiDetails as $detail) {
            $kategori = $detail->itemInspeksi->kategoriInspeksi->nama_kategori;

            if (!isset($statistik_per_kategori[$kategori])) {
                $statistik_per_kategori[$kategori] = ['normal' => 0, 'tidak_normal' => 0];
            }

            if ($detail->status_kondisi === 'normal') {
                $titik_normal++;
                $statistik_per_kategori[$kategori]['normal']++;
            } else {
                $titik_tidak_normal++;
                $statistik_per_kategori[$kategori]['tidak_normal']++;
                $item_rusak[] = [
                    'kategori'   => $kategori,
                    'nama_item'  => $detail->itemInspeksi->nama_item,
                    'status'     => $detail->status_kondisi,
                ];
            }

            $hasil_inspeksi[$kategori][] = $detail;
        }

        $dataLaporan = [
            'instansi'               => $user->instansi,
            'inspektor'              => $user->name,
            'tanggal'                => $order->updated_at->translatedFormat('d F Y'),
            'mobil'                  => $mobil,
            'stnk'                   => $mobil->inspeksiStnk,
            'bpkb'                   => $mobil->inspeksiBpkb,
            'informasi_umum'         => $mobil->informasiUmum,
            'dokumen_lain'           => $mobil->inspeksiDokumenLain,
            'hasil_inspeksi'         => $hasil_inspeksi,
            'statistik_per_kategori' => $statistik_per_kategori,
            'total_titik'            => $titik_normal + $titik_tidak_normal,
            'titik_normal'           => $titik_normal,
            'titik_tidak_normal'     => $titik_tidak_normal,
            'item_rusak'             => $item_rusak,
            'catatan_tambahan'       => $mobil->informasiUmum->catatan_tambahan ?? null,
            'kondisi_banjir'         => $mobil->informasiUmum->kondisi_banjir ?? null,
            'kondisi_tabrak'         => $mobil->informasiUmum->kondisi_tabrak ?? null,
            'titik_banjir'           => $mobil->informasiUmum->titik_banjir ?? null,
            'titik_tabrak'           => $mobil->informasiUmum->titik_tabrak ?? null,
        ];

        $pdf = Pdf::loadView('laporan.pdf_template', $dataLaporan);
        
        $pdf->setPaper('A4', 'portrait')->setWarnings(false);

        return $pdf->stream('Laporan_Inspeksi_' . $mobil->nopol . '.pdf');
    }
}