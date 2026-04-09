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
        Schema::create('inspeksi_bpkbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete(); 
            
            $table->string('foto_bpkb_1');
            $table->string('foto_bpkb_2')->nullable();
            $table->string('foto_bpkb_3')->nullable();
            $table->string('foto_bpkb_4')->nullable();
            $table->string('nama_pemilik');
            $table->string('nomor_bpkb');
            $table->enum('kepemilikan_mobil', ['pribadi', 'perusahaan']);
            $table->enum('sph', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('benang_pembatas', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('hologram_polri', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('faktur', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('nik', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('form_a', ['ada', 'tidak_ada', 'rusak']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_bpkbs');
    }
};
