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

Route::middleware('auth:api')->group(function () {

    Route::apiResource('users', UserController::class);
    Route::apiResource('flows', FlowController::class);

    // CRUD Permission
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{id}', [PermissionController::class, 'show']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);

    // CRUD flownode
    Route::get('/flows/{flow}/nodes', [FlowNodeController::class, 'index']);
    Route::post('/flows/{flow}/nodes', [FlowNodeController::class, 'store']);
    Route::get('/nodes/{node}', [FlowNodeController::class, 'show']);
    Route::put('/nodes/{node}', [FlowNodeController::class, 'update']);
    Route::delete('/nodes/{node}', [FlowNodeController::class, 'destroy']);
});