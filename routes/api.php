<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PharmacyController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\StockController;

Route::apiResource('pharmacies', PharmacyController::class);
Route::apiResource('medicines', MedicineController::class);
Route::apiResource('stocks', StockController::class);