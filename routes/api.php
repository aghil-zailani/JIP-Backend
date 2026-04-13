<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TugasController;
use App\Http\Controllers\Api\DokumenController;
use App\Http\Controllers\Api\InteriorController;
use App\Http\Controllers\Api\MasterDataController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']); 
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/tugas', [TugasController::class, 'index']);
    Route::post('/tugas/{order_id}/informasi', [TugasController::class, 'simpanInformasi']);
    Route::post('/tugas/{order_id}/dokumen', [DokumenController::class, 'simpanDokumen']);
    Route::post('/tugas/{order_id}/item/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/selesai', [TugasController::class, 'selesaikanTugas']);

    Route::get('/master/kategori-item', [MasterDataController::class, 'getKategoriItems']);

    Route::post('/logout', [UserController::class, 'logout']);
});