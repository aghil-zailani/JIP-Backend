<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inspeksi_dokumen_lains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete(); 
            
            $table->enum('buku_service', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('buku_manual', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('cek_logo_scanner', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('kir', ['ada', 'tidak_ada', 'rusak']);
            $table->enum('samsat_online', ['ada', 'tidak_ada', 'rusak']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_dokumen_lains');
    }
};
