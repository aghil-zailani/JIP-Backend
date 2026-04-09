<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilInspeksiDetail extends Model
{
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function itemInspeksi()
    {
        return $this->belongsTo(ItemInspeksi::class);
    }

    public function fotoKerusakans()
    {
        return $this->hasMany(FotoKerusakan::class);
    }
}