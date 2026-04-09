<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    protected $guarded = ['id'];

    public function inspeksiBpkb()
    {
        return $this->hasOne(InspeksiBpkb::class);
    }

    public function inspeksiStnk()
    {
        return $this->hasOne(InspeksiStnk::class);
    }

    public function inspeksiDokumenLain()
    {
        return $this->hasOne(InspeksiDokumenLain::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}