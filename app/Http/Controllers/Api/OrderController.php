<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Mobil;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function tambahInspeksi(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'nama_pelanggan'  => 'required|string|max:255',
            'email_pelanggan' => 'required|email|max:255',
            'no_hp_pelanggan' => 'required|string|max:20',   
            'alamat_pelanggan' => 'required|string|max:255',         
            'merek_mobil'     => 'required|string',
            'model_mobil'     => 'required|string',
            'tahun_mobil'     => 'required|digits:4',        
            'lokasi'          => 'required|string',
            'tanggal_inspeksi'=> 'required|date_format:Y-m-d',
            'waktu_inspeksi'  => 'required|date_format:H:i',
            'biaya'           => 'required|numeric',            
        ]);

        $user = auth()->guard('api')->user();

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();

        try {                        
            $namaMobilLengkap = $request->merek_mobil . ' ' . $request->model_mobil;
            
            $mobil = Mobil::create([
                'nama_mobil'     => $namaMobilLengkap,
                'tahun_mobil'    => $request->tahun_mobil,
                'jenis_inspeksi' => $request->jenis_inspeksi ?? 'Inspeksi Standar',
            ]);
            
            $jadwalInspeksi = Carbon::parse($request->tanggal_inspeksi . ' ' . $request->waktu_inspeksi);
            
            $order = Order::create([
                'mobil_id'        => $mobil->id,
                'user_id'         => $user->id,
                'nama_pelanggan'  => $request->nama_pelanggan,
                'email_pelanggan' => $request->email_pelanggan,
                'no_hp_pelanggan' => $request->no_hp_pelanggan,
                'alamat_pelanggan' => $request->alamat_pelanggan,
                'lokasi_inspeksi' => $request->lokasi,
                'jadwal_inspeksi' => $jadwalInspeksi,
                'biaya_inspeksi'  => $request->biaya,
                'status_inspeksi' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data inspeksi kendaraan berhasil ditambahkan dan ditugaskan.',
                'data'    => [
                    'order' => $order,
                    'mobil' => $mobil
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }
}