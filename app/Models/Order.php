<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

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