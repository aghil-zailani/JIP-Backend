<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformasiInstansi extends Model
{
    protected $table = 'informasi_instansi';

    protected $fillable = [
        'user_id',
        'nama_instansi',
        'alamat',
        'logo_instansi'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}