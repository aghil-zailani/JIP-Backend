<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformasiUmum extends Model
{
    protected $guarded = ['id'];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }
}
