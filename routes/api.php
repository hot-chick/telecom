<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentTypeController;

Route::get('/equipment-types', [EquipmentTypeController::class, 'index']);

Route::prefix('equipment')->group(function () {
    Route::get('/', [EquipmentController::class, 'index']); 
    Route::get('{id}', [EquipmentController::class, 'show']);
    Route::post('/', [EquipmentController::class, 'store']); 
    Route::put('{id}', [EquipmentController::class, 'update']);
    Route::delete('{id}', [EquipmentController::class, 'destroy']); 
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
