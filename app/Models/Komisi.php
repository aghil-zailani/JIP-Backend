<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komisi extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'order_id', 
        'nomor_slip',
        'jumlah_pendapatan',
        'metode_bayar',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}