<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FlowController;
use App\Http\Controllers\Api\FlowNodeController;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// auth:api  -> tetap JWT punya kamu, cek user login
// load.permissions -> eager load role.permissions.module sekali di awal (hindari N+1)
// perm:module,action -> baru dipasang PER ROUTE, sesuai module & action masing-masing
Route::middleware(['auth:api', 'load.permissions'])->group(function () {

    // ============ FLOWS ============
    Route::prefix('flows')->group(function () {
        Route::middleware('perm:flows,read')->get('/', [FlowController::class, 'index']);
        Route::middleware('perm:flows,create')->post('/', [FlowController::class, 'store']);
        Route::middleware('perm:flows,read')->get('/{flow}', [FlowController::class, 'show']);
        Route::middleware('perm:flows,update')->put('/{flow}', [FlowController::class, 'update']);
        Route::middleware('perm:flows,delete')->delete('/{flow}', [FlowController::class, 'destroy']);

        // Nested: nodes di dalam sebuah flow -- pakai module "flows" juga
        // (ganti ke module terpisah "flow_nodes" kalau mentor mau permission-nya independen)
        Route::middleware('perm:flows,read')->get('/{flow}/nodes', [FlowNodeController::class, 'index']);
        Route::middleware('perm:flows,create')->post('/{flow}/nodes', [FlowNodeController::class, 'store']);
    });

    Route::prefix('nodes')->group(function () {
        Route::middleware('perm:flows,read')->get('/{node}', [FlowNodeController::class, 'show']);
        Route::middleware('perm:flows,update')->put('/{node}', [FlowNodeController::class, 'update']);
        Route::middleware('perm:flows,delete')->delete('/{node}', [FlowNodeController::class, 'destroy']);
    });

    // ============ USERS (contoh read-only) ============
    Route::prefix('users')->group(function () {
        Route::middleware('perm:users,read')->get('/', [UserController::class, 'index']);
        Route::middleware('perm:users,read')->get('/{user}', [UserController::class, 'show']);
        // create/update/delete sengaja TIDAK didaftarkan -- module ini read-only.
        // Kalau ternyata butuh create/update/delete juga, tambahkan seperti pola "flows" di atas.
    });

    // ============ PERMISSIONS (manajemen role x module x option) ============
    Route::prefix('permissions')->group(function () {
        Route::middleware('perm:permissions,read')->get('/', [PermissionController::class, 'index']);
        Route::middleware('perm:permissions,read')->get('/{id}', [PermissionController::class, 'show']);
        Route::middleware('perm:permissions,create')->post('/', [PermissionController::class, 'store']);
        Route::middleware('perm:permissions,update')->put('/{id}', [PermissionController::class, 'update']);
        Route::middleware('perm:permissions,delete')->delete('/{id}', [PermissionController::class, 'destroy']);
    });
    // tambahkan sementara di routes/api.php, di dalam group auth:api + load.permissions
    Route::get('/debug-permission', function () {
    $user = auth()->user();
    return response()->json([
        'user_id'    => $user->id,
        'role_id'    => $user->role_id,
        'role'       => $user->role,
        'permissions'=> $user->permissionMap(),
        'check_result' => $user->hasPermission('permissions', 'read'), // <-- baris baru
    ]);
});
});