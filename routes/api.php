<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FlowController;
use App\Http\Controllers\Api\FlowNodeController;
use App\Http\Controllers\Api\FlowConnectionController;
use App\Http\Controllers\Api\SimulationController;
use App\Http\Controllers\Api\NodeExecutionController;
use App\Http\Controllers\Api\NodeTemplateController;

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
        Route::middleware('perm:flows,read')->get('/{flow}/nodes', [FlowNodeController::class, 'index']);
        Route::middleware('perm:flows,create')->post('/{flow}/nodes', [FlowNodeController::class, 'store']);

        // Nested: koneksi antar node di dalam sebuah flow
        Route::middleware('perm:connection,read')->get('/{flow}/connections', [FlowConnectionController::class, 'index']);
        Route::middleware('perm:connection,create')->post('/{flow}/connections', [FlowConnectionController::class, 'store']);

        // Nested: simulasi milik sebuah flow
        Route::middleware('perm:flows,read')->get('/{flow}/simulations', [SimulationController::class, 'index']);
        Route::middleware('perm:flows,create')->post('/{flow}/simulations', [SimulationController::class, 'store']);
    });

    Route::prefix('nodes')->group(function () {
        Route::middleware('perm:flows,read')->get('/{node}', [FlowNodeController::class, 'show']);
        Route::middleware('perm:flows,update')->put('/{node}', [FlowNodeController::class, 'update']);
        Route::middleware('perm:flows,delete')->delete('/{node}', [FlowNodeController::class, 'destroy']);
    });

    // ============ FLOW CONNECTIONS ============
    Route::prefix('connections')->group(function () {
        Route::middleware('perm:connection,read')->get('/{connection}', [FlowConnectionController::class, 'show']);
        Route::middleware('perm:connection,update')->put('/{connection}', [FlowConnectionController::class, 'update']);
        Route::middleware('perm:connection,delete')->delete('/{connection}', [FlowConnectionController::class, 'destroy']);
    });

    // ============ SIMULATIONS ============
    Route::prefix('simulations')->group(function () {
        Route::middleware('perm:flows,read')->get('/{simulation}', [SimulationController::class, 'show']);
        Route::middleware('perm:flows,delete')->delete('/{simulation}', [SimulationController::class, 'destroy']);
        Route::middleware('perm:flows,update')->put('/{id}/complete', [SimulationController::class, 'complete']);

        // ============ NODE EXECUTIONS ============
        Route::middleware('perm:flows,read')->get('/{simulation}/executions', [NodeExecutionController::class, 'index']);
    });

    // ============ TEMPLATES ============
    Route::prefix('templates')->group(function () {
        Route::middleware('perm:templates,read')->get('/', [NodeTemplateController::class, 'index']);
        Route::middleware('perm:templates,create')->post('/', [NodeTemplateController::class, 'store']);
        Route::middleware('perm:templates,read')->get('/{template}', [NodeTemplateController::class, 'show']);
        Route::middleware('perm:templates,update')->put('/{template}', [NodeTemplateController::class, 'update']);
        Route::middleware('perm:templates,delete')->delete('/{template}', [NodeTemplateController::class, 'destroy']);
    });

    // ============ USERS ============
    // middleware('role:admin') dari branch azka -- pastikan RoleMiddleware
    // sudah didaftarkan aliasnya di bootstrap/app.php sebagai 'role'.
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::middleware('perm:users,read')->get('/', [UserController::class, 'index']);
        Route::middleware('perm:users,create')->post('/', [UserController::class, 'store']);
        Route::middleware('perm:users,read')->get('/{user}', [UserController::class, 'show']);
        Route::middleware('perm:users,update')->put('/{user}', [UserController::class, 'update']);
        Route::middleware('perm:users,delete')->delete('/{user}', [UserController::class, 'destroy']);
    });

    // ============ PERMISSIONS (manajemen role x module x option) ============
    Route::prefix('permissions')->group(function () {
        Route::middleware('perm:permissions,read')->get('/', [PermissionController::class, 'index']);
        Route::middleware('perm:permissions,read')->get('/{id}', [PermissionController::class, 'show']);
        Route::middleware('perm:permissions,create')->post('/', [PermissionController::class, 'store']);
        Route::middleware('perm:permissions,update')->put('/{id}', [PermissionController::class, 'update']);
        Route::middleware('perm:permissions,delete')->delete('/{id}', [PermissionController::class, 'destroy']);
    });
});