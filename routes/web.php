<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentTypeController;

Route::post('/equipment', [EquipmentController::class, 'store']);
Route::put('/equipment/{id}', [EquipmentController::class, 'update']);
