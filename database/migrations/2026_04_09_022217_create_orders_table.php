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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete();
                        
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                        
            $table->string('nama_pelanggan');
            $table->string('no_hp_pelanggan');

            $table->string('status_inspeksi')->default('pending');
            $table->string('lokasi_inspeksi')->nullable();
            $table->integer('biaya_inspeksi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
