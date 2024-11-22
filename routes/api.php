<?php

use App\Events\AttendanceRealtimeEvent;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceGuestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaceScanController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\TypeEmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/post_att', [AttendanceController::class, 'post_att']);

Route::post('/', [FaceScanController::class, 'facescan']);

Route::post('login', [AuthController::class, 'login']); 

Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('type_employee', [TypeEmployeeController::class, 'list']);

    Route::prefix('dashboard')->group(function () {
        Route::get('counts', [DashboardController::class, 'counts']);
        Route::get('charts_employee', [DashboardController::class, 'charts_employee']);
        Route::get('charts_late_and_not_present', [DashboardController::class, 'charts_late_and_not_present']);
        Route::get('charts_guest', [DashboardController::class, 'charts_guest']);
    });

    Route::prefix('employee')->group(function () {
        Route::get('list', [UserController::class, 'list']);
        Route::post('store', [UserController::class, 'store']);
        Route::get('detail/{id}', [UserController::class, 'detail']);
        Route::put('update/{id}', [UserController::class, 'update']);
        Route::put('update_status', [UserController::class, 'update_status']);
        Route::put('update_shift', [UserController::class, 'update_shift']);
        Route::delete('delete/{id}', [UserController::class, 'delete']);
    });

    Route::prefix('shift')->group(function () {
        Route::get('list', [ShiftController::class, 'list']);
        Route::post('store', [ShiftController::class, 'store']);
        Route::get('detail/{id}', [ShiftController::class, 'detail']);
        Route::put('update/{id}', [ShiftController::class, 'update']);
        Route::delete('delete/{id}', [ShiftController::class, 'delete']);
    });
    
    Route::prefix('guest')->group(function () {
        Route::get('list', [GuestController::class, 'list']);
        Route::post('store', [GuestController::class, 'store']);
        Route::get('detail/{id}', [GuestController::class, 'detail']);
        Route::put('update/{id}', [GuestController::class, 'update']);
        Route::delete('delete/{id}', [GuestController::class, 'delete']);
    });

    Route::prefix('attendance')->group(function () {
        Route::get('list', [AttendanceController::class, 'list']);
        Route::get('list_per_employee/{user_id}', [AttendanceController::class, 'list_per_employee']);
        Route::get('detail/{id}', [AttendanceController::class, 'detail']);
        Route::get('report', [AttendanceController::class, 'report']);
    });
    
    Route::prefix('attendance_guest')->group(function () {
        Route::get('list', [AttendanceGuestController::class, 'list']);
        Route::get('list_per_guest/{guest_id}', [AttendanceGuestController::class, 'list_per_guest']);
        Route::post('store', [AttendanceGuestController::class, 'store']);
        Route::post('store', [AttendanceGuestController::class, 'store']);
        Route::put('update/{id}', [AttendanceGuestController::class, 'update']);
        Route::get('report', [AttendanceGuestController::class, 'report']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('list', [RolePermissionController::class, 'role']);
        Route::get('permissions', [RolePermissionController::class, 'permission']);
        Route::get('edit-role/{id}', [RolePermissionController::class, 'editRole']);
        Route::post('update-role/{id}', [RolePermissionController::class, 'updateRole']);
    });
    
    Route::prefix('companies')->group(function () {
        Route::get('list', [CompanyController::class, 'list']);
        Route::get('detail/{id}', [CompanyController::class, 'detail']);
        Route::post('store', [CompanyController::class, 'store']);
        Route::put('update/{id}', [CompanyController::class, 'update']);
    });
    

    // Router lainnya
});

// Ably 
Route::get('/attendance_realtime', function (Request $request) {
    $channelName = 'attendance-gamatara-channel';
    $attendanceController = new AttendanceController();
    $data = $attendanceController->list($request)->getData();

    broadcast(new AttendanceRealtimeEvent( $channelName, $data ));

    return response()->json([
        'success' => true,
        'message' => 'Data berhasil dibroadcast',
        'data' => $data
    ]);

})->middleware('throttle:60,1');
