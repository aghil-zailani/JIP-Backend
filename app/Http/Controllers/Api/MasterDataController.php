<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriInspeksi;

class MasterDataController extends Controller
{
    public function getKategoriItems()
    {        
        $data = KategoriInspeksi::with('itemInspeksis')->get();
        
        $formatData = $data->map(function ($kategori) {
            return [
                'kategori_id' => $kategori->id,
                'nama_kategori' => $kategori->nama_kategori,
                'daftar_item' => $kategori->itemInspeksis->map(function ($item) {
                    return [
                        'item_id' => $item->id,
                        'nama_item' => $item->nama_item
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar Kategori dan Item Inspeksi',
            'data' => $formatData
        ], 200);
    }
}