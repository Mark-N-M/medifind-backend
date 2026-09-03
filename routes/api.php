<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PharmacyController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

// -------------------------------------------------------------
// 🌐 1. Public Routes (Anyone can search catalog/stocks)
// -------------------------------------------------------------
Route::get('/pharmacies', [PharmacyController::class, 'index']);
Route::get('/pharmacies/{id}', [PharmacyController::class, 'show']);
Route::get('/medicines', [MedicineController::class, 'index']);
Route::get('/stocks', [StockController::class, 'index']);
// Public route to find all pharmacies stocking a specific medicine
Route::get('/medicines/{medicineId}/pharmacies', [StockController::class, 'getPharmaciesByMedicine']);

// -------------------------------------------------------------
// 🔑 2. Authentication Routes
// -------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// -------------------------------------------------------------
// 🔐 3. Protected Routes (Requires valid Sanctum Bearer Token)
// -------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    
    // Revoke current session token
    Route::post('/logout', [AuthController::class, 'logout']);//post used to execute the controller class function assigned to the logout function in authcontroller

    // 👨‍⚕️ Gate A: Admin & Pharmacist (Manage Stock)
    Route::middleware('role:admin,pharmacist')->group(function () {
        Route::post('/stocks', [StockController::class, 'store']);
        Route::put('/stocks/{id}', [StockController::class, 'update']);
        Route::delete('/stocks/{id}', [StockController::class, 'destroy']);
    });

    // 👑 Gate B: Super Admin Only (Manage Pharmacies & Master Catalog)
    Route::middleware('role:admin')->group(function () {
        Route::post('/pharmacies', [PharmacyController::class, 'store']);
        Route::put('/pharmacies/{id}', [PharmacyController::class, 'update']);
        Route::delete('/pharmacies/{id}', [PharmacyController::class, 'destroy']);
        
        Route::post('/medicines', [MedicineController::class, 'store']);
        Route::put('/medicines/{id}', [MedicineController::class, 'update']);
        Route::delete('/medicines/{id}', [MedicineController::class, 'destroy']);
    });
    
    //Admin portal/pharmacists approval protected routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/admin/pending-pharmacists', [AdminController::class, 'pendingPharmacists']);
        Route::patch('/admin/approve-pharmacist/{id}', [AdminController::class, 'approvePharmacist']);
        Route::patch('/admin/reject-pharmacist/{id}', [AdminController::class, 'rejectPharmacist']);
    });

    //Pharmacists portal on crud functionality for stocks edits
    Route::middleware(['auth:sanctum', 'role:pharmacist'])->group(function () {
        Route::get('/pharmacist/stocks', [StockController::class, 'index']);
        Route::post('/pharmacist/stocks', [StockController::class, 'store']);
        Route::put('/pharmacist/stocks/{id}', [StockController::class, 'update']);
        Route::delete('/pharmacist/stocks/{id}', [StockController::class, 'destroy']);
    });
});