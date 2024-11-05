<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaceScanController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/', [FaceScanController::class, 'facescan']);

Route::post('login', [AuthController::class, 'login']); 

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('karyawan')->group(function () {
        Route::get('list', [UserController::class, 'list']);
        Route::post('store', [UserController::class, 'store']);
        Route::get('detail/{id}', [UserController::class, 'detail']);
        Route::put('update/{id}', [UserController::class, 'update']);
        Route::delete('delete/{id}', [UserController::class, 'delete']);
    });

    Route::prefix('shift')->group(function () {
        Route::get('list', [ShiftController::class, 'list']);
        Route::post('store', [ShiftController::class, 'store']);
        Route::get('detail/{id}', [ShiftController::class, 'detail']);
        Route::put('update/{id}', [ShiftController::class, 'update']);
        Route::delete('delete/{id}', [ShiftController::class, 'delete']);
    });

    Route::prefix('attendance')->group(function () {
        Route::get('list', [AttendanceController::class, 'list']);
        Route::get('list_per_employee/{user_id}', [AttendanceController::class, 'list_per_employee']);
        Route::get('detail/{id}', [AttendanceController::class, 'detail']);
    });


    // Router lainnya
});
