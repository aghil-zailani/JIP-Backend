<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriInspeksi extends Model
{
    protected $guarded = ['id'];

    public function itemInspeksis()
    {
        return $this->hasMany(ItemInspeksi::class);
    }
}