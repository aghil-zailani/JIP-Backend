<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoKerusakan extends Model
{
    protected $guarded = ['id'];

    public function hasilInspeksiDetail()
    {
        return $this->belongsTo(HasilInspeksiDetail::class);
    }
}