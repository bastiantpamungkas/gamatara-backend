<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaceScanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/', [FaceScanController::class, 'facescan']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('list-karyawan', [UserController::class, 'list']);
    // Router lainnya
});
