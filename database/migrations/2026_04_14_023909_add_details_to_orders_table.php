<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('email_pelanggan')->after('nama_pelanggan')->nullable();
            $table->dateTime('jadwal_inspeksi')->after('lokasi_inspeksi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['email_pelanggan', 'jadwal_inspeksi']);
        });
    }
};