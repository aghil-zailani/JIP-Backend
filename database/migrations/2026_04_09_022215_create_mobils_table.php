<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mobils', function (Blueprint $table) {
            $table->id(); 
            $table->string('nama_mobil');
            $table->year('tahun_mobil');
            $table->string('jenis_inspeksi');
            $table->string('nomor_polisi');
            $table->string('transmisi');
            $table->string('bahan_bakar');
            $table->integer('jarak_tempuh');
            $table->string('warna_mobil');
            $table->string('tipe_mobil');
            $table->integer('kapasitas_mesin');
            $table->string('kondisi_tabrak');
            $table->string('kondisi_banjir');
            $table->text('catatan_tambahan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobils');
    }
};
