<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InspeksiStnk extends Model
{
    protected $guarded = ['id'];
    public function mobil() { return $this->belongsTo(Mobil::class); }
}