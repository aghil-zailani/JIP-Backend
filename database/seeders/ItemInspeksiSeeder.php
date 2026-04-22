<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriInspeksi;
use App\Models\ItemInspeksi;

class ItemInspeksiSeeder extends Seeder
{
    public function run(): void
    {
        // Menyusun data kategori dan anak-anak itemnya berdasarkan desain Figma & Excel
        $dataInspeksi = [
            'Interior' => [
                'Dashboard', 'Stir', 'Handle Porsneling', 'Doortrim', 'Speedometer', 
                'Kolong Stir', 'Jok dan Kolong Jok', 'Karpet', 'Plafon dan Pilar', 
                'Headunit', 'Door Lock', 'Power Window', 'Elektrik Spion', 'AC', 
                'Airbag', 'Sun Roof / Moon Roof'
            ],
            'Eksterior' => [
                'Foto Depan Kendaraan','Kap Mesin', 'Bumper Depan', 'Lampu Depan', 'Fender Depan Kiri',
                'Fender Depan Kanan', 'Pintu Depan Kiri', 'Pilar A', 'Pilar B', 'Pilar C',
                'Pintu Belakang Kiri', 'Quarter Kiri', 'Pintu Bagasi', 'End Panel', 'Stop Lamp',
                'Quarter Kanan', 'Pintu Belakang Kanan', 'Fender Depan Kanan', 'Kaca Mobil / Seal',
                'List Plang Bawah', 'Spion'
            ],
            'Mesin' => [
                'Bullhead Depan', 'Bullhead Kiri', 'Bullhead Kanan', 
                'Support Depan Kiri', 'Support Depan Kanan', 'Crossbeam Depan ', 
                'Tiang Vertikal', 'Kondisi Tampak Mesin', 'Tutup Pengisian Oli dan Dipstick',
                'Kopling dan Transmisi', 'Starter', 'Aki/Baterai', 'Perangkat Air Condisioner',
                'Hasil Scanner', 'Knalpot'
            ],
            'Kaki Kaki' => [
                'Rack Stir', 'Power Steering', 'Rem', 'Suspensi', 
                'Tahun Ban dan Ketebalan', 'Ban Serap', 'Velg'
            ]
        ];

        // Looping untuk memasukkan data ke database
        foreach ($dataInspeksi as $namaKategori => $items) {
            
            // 1. Buat Kategori (Jika belum ada agar tidak duplikat)
            $kategori = KategoriInspeksi::firstOrCreate([
                'nama_kategori' => $namaKategori
            ]);

            // 2. Buat Item-item di dalam kategori tersebut
            foreach ($items as $namaItem) {
                ItemInspeksi::firstOrCreate([
                    'kategori_inspeksi_id' => $kategori->id,
                    'nama_item' => $namaItem
                ]);
            }
        }

        $this->command->info('Data Kategori dan Item Inspeksi berhasil ditambahkan!');
    }
}