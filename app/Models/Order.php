<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'mobil_id', 'user_id', 'nama_pelanggan', 'email_pelanggan','alamat_pelanggan', 
        'no_hp_pelanggan', 'status_inspeksi', 'lokasi_inspeksi', 
        'jadwal_inspeksi', 'biaya_inspeksi'
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasilInspeksiDetails()
    {
        return $this->hasMany(HasilInspeksiDetail::class);
    }
}