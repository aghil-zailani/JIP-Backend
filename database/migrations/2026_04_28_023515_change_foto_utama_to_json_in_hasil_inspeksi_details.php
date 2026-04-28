<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrasi data lama: bungkus nilai string menjadi JSON array
        DB::table('hasil_inspeksi_details')
            ->whereNotNull('foto_utama')
            ->get()
            ->each(function ($row) {
                $decoded = json_decode($row->foto_utama, true);
                // Jika belum JSON (masih string biasa), bungkus ke array
                if (!is_array($decoded)) {
                    DB::table('hasil_inspeksi_details')
                        ->where('id', $row->id)
                        ->update(['foto_utama' => json_encode([$row->foto_utama])]);
                }
            });

        // Ubah tipe kolom menjadi JSON
        Schema::table('hasil_inspeksi_details', function (Blueprint $table) {
            $table->json('foto_utama')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke string (ambil elemen pertama dari array)
        DB::table('hasil_inspeksi_details')
            ->whereNotNull('foto_utama')
            ->get()
            ->each(function ($row) {
                $arr = json_decode($row->foto_utama, true);
                $firstPhoto = is_array($arr) ? ($arr[0] ?? null) : $row->foto_utama;
                DB::table('hasil_inspeksi_details')
                    ->where('id', $row->id)
                    ->update(['foto_utama' => $firstPhoto]);
            });

        Schema::table('hasil_inspeksi_details', function (Blueprint $table) {
            $table->string('foto_utama')->nullable()->change();
        });
    }
};
