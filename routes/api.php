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
use App\Http\Controllers\Api\KomisiController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\TestPhotoController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/lupa-password', [UserController::class, 'lupaPassword']);

Route::post('/test-photo/upload', [TestPhotoController::class, 'upload']);
Route::post('/test-photo/base64', [TestPhotoController::class, 'uploadBase64']);
Route::get('/test-photo/list', [TestPhotoController::class, 'list']);

Route::middleware('auth:api')->group(function () {
    // File serving (pengganti storage:link untuk shared hosting)
    Route::get('/file', [FileController::class, 'serveBase64']);
    Route::get('/file/serve', [FileController::class, 'serveDirect']);
    Route::get('/users', [UserController::class, 'index']); 
    Route::get('/dashboard', [DashboardController::class, 'index']);    

    Route::get('/profile' , [UserController::class, 'profile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);

    Route::post('/tambah-inspeksi', [OrderController::class, 'tambahInspeksi']);

    Route::get('/tugas', [TugasController::class, 'index']);
    Route::get('/tugas/detail/{order_id}', [TugasController::class, 'detailTugas']);
    Route::get('/tugas/{order_id}/dokumen', [DokumenController::class, 'getDokumen']);    
    Route::post('/tugas/{order_id}/informasi', [TugasController::class, 'simpanInformasi']);
    Route::post('/tugas/{order_id}/kesimpulan', [TugasController::class, 'simpanInformasiKesimpulan']);
    Route::post('/tugas/{order_id}/dokumen', [DokumenController::class, 'simpanDokumen']);
    Route::post('/tugas/{order_id}/interior/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/kaki-kaki/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/mesin/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/eksterior/{item_id}', [InteriorController::class, 'simpanHasilItem']);
    Route::post('/tugas/{order_id}/selesai', [TugasController::class, 'selesaikanTugas']);

    // ─── Endpoint Photo (simpan langsung ke public/Photo, tanpa storage:link) ───
    Route::post('/tugas/{order_id}/dokumen-photo', [DokumenController::class, 'simpanDokumenPhoto']);
    Route::get('/tugas/{order_id}/dokumen-photo', [DokumenController::class, 'getDokumenPhoto']);
    Route::post('/tugas/{order_id}/interior-photo/{item_id}', [InteriorController::class, 'simpanHasilItemPhoto']);
    Route::post('/tugas/{order_id}/kaki-kaki-photo/{item_id}', [InteriorController::class, 'simpanHasilItemPhoto']);
    Route::post('/tugas/{order_id}/mesin-photo/{item_id}', [InteriorController::class, 'simpanHasilItemPhoto']);
    Route::post('/tugas/{order_id}/eksterior-photo/{item_id}', [InteriorController::class, 'simpanHasilItemPhoto']);

    Route::get('/laporan/{komisi_id}/pdf', [TugasController::class, 'exportPdf']);

    Route::get('/master/kategori-item', [MasterDataController::class, 'getKategoriItems']);

    Route::get('/komisi', [KomisiController::class, 'index']);
    Route::get('/komisi/{id}', [KomisiController::class, 'show']);
    Route::post('/komisi/{id}/selesai', [KomisiController::class, 'updatePembayaran']);

    Route::post('/logout', [UserController::class, 'logout']);
});