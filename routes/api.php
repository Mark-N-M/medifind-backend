<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PharmacyController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\MedicineRequestController;
use App\Http\Controllers\Api\PublicSearchController;
use Illuminate\Http\Request;

// -------------------------------------------------------------
// 1. Public Routes (Anyone can search catalog/stocks)
// -------------------------------------------------------------
Route::get('/pharmacies', [PharmacyController::class, 'index']);
Route::get('/pharmacies/{id}', [PharmacyController::class, 'show']);
Route::get('/medicines', [MedicineController::class, 'index']);
Route::get('/medicines/{id}', [MedicineController::class, 'show']);
Route::get('/stocks', [StockController::class, 'index']);
Route::get('/medicines/{medicineId}/pharmacies', [StockController::class, 'getPharmaciesByMedicine']);
Route::get('/search/medicines', [PublicSearchController::class, 'searchMedicines']);
Route::get('/public/pharmacies', [PublicSearchController::class, 'getPharmacies']);

// -------------------------------------------------------------
// 2. Authentication Routes
// -------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// -------------------------------------------------------------
// 3. Protected Routes (Requires valid Sanctum Bearer Token)
// -------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('pharmacy'));
    });

    // Gate A: Admin & Pharmacist (Manage Stock)
    Route::middleware('role:admin,pharmacist')->group(function () {
        Route::post('/stocks', [StockController::class, 'store']);
        Route::put('/stocks/{id}', [StockController::class, 'update']);
        Route::delete('/stocks/{id}', [StockController::class, 'destroy']);
    });

    // Gate B: Super Admin Only
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Pharmacy Management
        Route::post('/pharmacies', [PharmacyController::class, 'store']);
        Route::put('/pharmacies/{id}', [PharmacyController::class, 'update']);
        Route::delete('/pharmacies/{id}', [PharmacyController::class, 'destroy']);
        
        // Medicine Catalog Management
        Route::post('/medicines', [MedicineController::class, 'store']);
        Route::put('/medicines/{id}', [MedicineController::class, 'update']);
        Route::delete('/medicines/{id}', [MedicineController::class, 'destroy']);

        // Pharmacist Account Approvals (Matches Axios requests)
        Route::get('/pending-pharmacists', [AdminController::class, 'pendingPharmacists']);
        Route::patch('/pharmacists/{id}/approve', [AdminController::class, 'approvePharmacist']);
        Route::patch('/pharmacists/{id}/reject', [AdminController::class, 'rejectPharmacist']);

        // Admin Medicine Requests Management
        Route::get('/medicine-requests', [MedicineRequestController::class, 'index']);
        Route::patch('/medicine-requests/{id}/approve', [MedicineRequestController::class, 'approve']);
        Route::patch('/medicine-requests/{id}/reject', [MedicineRequestController::class, 'reject']);
    });

    //  Pharmacist Portal (Stock management & Medicine requests)
    Route::middleware('role:pharmacist')->group(function () {
        Route::get('/pharmacist/stocks', [StockController::class, 'index']);
        Route::post('/pharmacist/stocks', [StockController::class, 'store']);
        Route::put('/pharmacist/stocks/{id}', [StockController::class, 'update']);
        Route::delete('/pharmacist/stocks/{id}', [StockController::class, 'destroy']);

        Route::post('/medicine-requests', [MedicineRequestController::class, 'store']);
        Route::get('/medicine-requests/my-requests', [MedicineRequestController::class, 'userRequests']);
    });

});