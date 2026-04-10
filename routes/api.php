<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TugasController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']); 
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/tugas', [TugasController::class, 'index']);

    Route::post('/logout', [UserController::class, 'logout']);
});