<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemInspeksi extends Model
{
    protected $guarded = ['id'];

    public function kategoriInspeksi()
    {
        return $this->belongsTo(KategoriInspeksi::class);
    }

    public function hasilInspeksiDetails()
    {
        return $this->hasMany(HasilInspeksiDetail::class);
    }
}