<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TugasController;
use App\Http\Controllers\Api\DokumenController;
use App\Http\Controllers\Api\InteriorController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\OrderController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/lupa-password', [UserController::class, 'lupaPassword']);

Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']); 
    Route::get('/dashboard', [DashboardController::class, 'index']);    

    Route::get('/profile' , [UserController::class, 'profile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);

    Route::post('/tambah-inspeksi', [OrderController::class, 'tambahInspeksi']);

    Route::get('/tugas', [TugasController::class, 'index']);

    Route::get('/tugas/{order_id}/dokumen', [DokumenController::class, 'getDokumen']);

    Route::post('/tugas/{order_id}/informasi', [TugasController::class, 'simpanInformasi']);
    Route::post('/tugas/{order_id}/dokumen', [DokumenController::class, 'simpanDokumen']);
    Route::post('/tugas/{order_id}/interior/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/kaki-kaki/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/mesin/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/eksterior/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/selesai', [TugasController::class, 'selesaikanTugas']);

    Route::get('/master/kategori-item', [MasterDataController::class, 'getKategoriItems']);

    Route::post('/logout', [UserController::class, 'logout']);
});