<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mobil;
use App\Models\Order;
use App\Models\Komisi;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $inspektor = User::where('role', 'inspektor')->first();

        if (!$inspektor) {
            $this->command->error('User Inspektur belum ada! Jalankan UserSeeder dulu.');
            return;
        }

        $mobil1 = Mobil::create([
            'nama_mobil' => 'LC LC',
            'tahun_mobil' => '2023',
            'jenis_inspeksi' => 'Inspeksi Standar',                        
        ]);

        $order1 = Order::create([
            'mobil_id' => $mobil1->id,
            'user_id' => $inspektor->id,
            'nama_pelanggan' => 'Agung',           
            'no_hp_pelanggan' => '085837119884',
            'status_inspeksi' => 'pending',
            'lokasi_inspeksi' => 'Jl. Arifin Ahmad',
            'biaya_inspeksi' => 50000,
            'created_at' => now()->subHours(2), 
        ]);

        Komisi::create([
            'user_id' => $inspektor->id,
            'nomor_slip' => 'SLIP-' . time() . '1',
            'jumlah_pendapatan' => 50000,
            'metode_bayar' => 'Tunai',
            'status' => 'pending' 
        ]);

        $mobil2 = Mobil::create([
            'nama_mobil' => 'XL7 Alpha',
            'tahun_mobil' => '2023',
            'jenis_inspeksi' => 'Inspeksi Standar',            
        ]);

        $order2 = Order::create([
            'mobil_id' => $mobil2->id,
            'user_id' => $inspektor->id,
            'status_inspeksi' => 'selesai', 
            'nama_pelanggan' => 'Bayu',        
            'no_hp_pelanggan' => '085837119884',
            'lokasi_inspeksi' => 'Jl. Sudirman',
            'biaya_inspeksi' => 50000,
            'created_at' => now()->subDays(1), 
        ]);

        Komisi::create([
            'user_id' => $inspektor->id,
            'nomor_slip' => 'SLIP-' . time() . '2',
            'jumlah_pendapatan' => 50000,
            'metode_bayar' => 'Tunai',
            'status' => 'cair' 
        ]);

        $mobil3 = Mobil::create([
            'nama_mobil' => 'Avanza Type G',
            'tahun_mobil' => '2017',
            'jenis_inspeksi' => 'Inspeksi Standar',            
        ]);

        $order3 = Order::create([
            'mobil_id' => $mobil3->id,
            'user_id' => $inspektor->id,
            'nama_pelanggan' => 'Zura',        
            'no_hp_pelanggan' => '085837119884',
            'status_inspeksi' => 'selesai',
            'lokasi_inspeksi' => 'Jl. Suka Karya',
            'biaya_inspeksi' => 50000,
            'created_at' => now()->subDays(2), 
        ]);

        Komisi::create([
            'user_id' => $inspektor->id,
            'nomor_slip' => 'SLIP-' . time() . '3',
            'jumlah_pendapatan' => 50000,
            'metode_bayar' => 'Tunai',
            'status' => 'cair'
        ]);

        $mobil4 = Mobil::create([
            'nama_mobil' => 'BMW M4 COMPETITION',
            'tahun_mobil' => '2022',
            'jenis_inspeksi' => 'Inspeksi Standar',            
        ]);

        $order5 = Order::create([
            'mobil_id' => $mobil4->id,
            'user_id' => $inspektor->id,
            'nama_pelanggan' => 'Aghil',        
            'no_hp_pelanggan' => '085837119884',
            'status_inspeksi' => 'selesai',
            'lokasi_inspeksi' => 'Jl. Suka Karya',
            'biaya_inspeksi' => 50000,
            'created_at' => now()->subDays(2), 
        ]);

        Komisi::create([
            'user_id' => $inspektor->id,
            'nomor_slip' => 'SLIP-' . time() . '3',
            'jumlah_pendapatan' => 50000,
            'metode_bayar' => 'Tunai',
            'status' => 'cair'
        ]);
        
        $mobil6 = Mobil::create([
            'nama_mobil' => 'LC LC',
            'tahun_mobil' => '2023',
            'jenis_inspeksi' => 'Inspeksi Standar',                        
        ]);

        $order6 = Order::create([
            'mobil_id' => $mobil6->id,
            'user_id' => $inspektor->id,
            'nama_pelanggan' => 'Jeri',        
            'no_hp_pelanggan' => '085837119884',
            'status_inspeksi' => 'pending',
            'lokasi_inspeksi' => 'Jl. Arifin Ahmad',
            'biaya_inspeksi' => 50000,
            'created_at' => now()->subHours(2), 
        ]);

        Komisi::create([
            'user_id' => $inspektor->id,
            'nomor_slip' => 'SLIP-' . time() . '1',
            'jumlah_pendapatan' => 50000,
            'metode_bayar' => 'Tunai',
            'status' => 'pending' 
        ]);

        $this->command->info('Data Dummy Dashboard berhasil ditambahkan!');
    }
}