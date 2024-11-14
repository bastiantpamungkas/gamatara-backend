<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaceScanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/', [FaceScanController::class, 'facescan']);

Route::post('login', [AuthController::class, 'login']);

Route::get('roles', [RolePermissionController::class, 'role']);
Route::get('edit-role/{id}', [RolePermissionController::class, 'editRole']);

Route::get('permissions', [RolePermissionController::class, 'permission']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('list-karyawan', [UserController::class, 'list']);

    // Router lainnya
});
