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
        Schema::create('inspeksi_stnks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete(); 
            
            $table->string('foto_stnk');
            $table->date('pajak_1_tahun');
            $table->date('pajak_5_tahun');
            $table->integer('pkb');
            $table->string('nomor_rangka');
            $table->string('nomor_mesin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_stnks');
    }
};
