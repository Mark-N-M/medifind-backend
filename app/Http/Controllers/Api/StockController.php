<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Exception;
use Illuminate\Http\Request;

class StockController extends Controller
{
    // [CREATE] Link a medicine to the authenticated pharmacist's pharmacy
    public function store(Request $request)
    {
        // Automatically inject pharmacy_id from authenticated user
        $pharmacyId = $request->user()->pharmacy_id ?? $request->user()->pharmacy?->id;

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'price'       => 'required|numeric|min:0',
            'in_stock'    => 'required|boolean',
        ]);

        try {
            $stock = Stock::create([
                'pharmacy_id' => $pharmacyId,
                'medicine_id' => $validated['medicine_id'],
                'price'       => $validated['price'],
                'in_stock'    => $validated['in_stock'],
            ]);

            return response()->json([
                'message' => 'Stock entry created successfully',
                'stock'   => $stock->load(['pharmacy', 'medicine'])
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to create stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [READ ALL] Fetch stock records ONLY for the logged-in pharmacist's pharmacy
    public function index(Request $request)
    {
        try {
            $pharmacyId = $request->user()->pharmacy_id ?? $request->user()->pharmacy?->id;

            $stocks = Stock::with(['pharmacy', 'medicine'])
                ->where('pharmacy_id', $pharmacyId)
                ->get();

            return response()->json([
                'status' => 'success',
                'count'  => $stocks->count(),
                'data'   => $stocks
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch stock list',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [READ BY MEDICINE] Public endpoint: Find all pharmacies that have a specific medicine in stock
    public function getPharmaciesByMedicine($medicineId)
    {
        try {
            $stocks = Stock::with(['pharmacy', 'medicine'])
                ->where('medicine_id', $medicineId)
                ->where('in_stock', true)
                ->get();

            if ($stocks->isEmpty()) {
                return response()->json([
                    'message' => 'No pharmacies currently have this medicine in stock.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'count'  => $stocks->count(),
                'data'   => $stocks
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch pharmacies for this medicine',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [READ ONE] Fetch specific stock entry by ID
    public function show($id)
    {
        try {
            $stock = Stock::with(['pharmacy', 'medicine'])->find($id);

            if (!$stock) {
                return response()->json([
                    'message' => 'Stock entry not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data'   => $stock
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [UPDATE] Update stock price or availability (Secured to owner)
    public function update(Request $request, $id)
    {
        $pharmacyId = $request->user()->pharmacy_id ?? $request->user()->pharmacy?->id;

        $validated = $request->validate([
            'medicine_id' => 'sometimes|exists:medicines,id',
            'price'       => 'sometimes|numeric|min:0',
            'in_stock'    => 'sometimes|boolean',
        ]);

        try {
            // Find stock ensuring it belongs to the authenticated pharmacist's pharmacy
            $stock = Stock::where('id', $id)
                ->where('pharmacy_id', $pharmacyId)
                ->first();

            if (!$stock) {
                return response()->json([
                    'message' => 'Stock entry not found or unauthorized'
                ], 404);
            }

            $stock->update($validated);

            return response()->json([
                'message' => 'Stock entry updated successfully!',
                'stock'   => $stock->load(['pharmacy', 'medicine'])
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to update stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [DELETE] Remove a stock entry (Secured to owner)
    public function destroy(Request $request, $id)
    {
        try {
            $pharmacyId = $request->user()->pharmacy_id ?? $request->user()->pharmacy?->id;

            $stock = Stock::where('id', $id)
                ->where('pharmacy_id', $pharmacyId)
                ->first();

            if ($stock) {
                $stock->delete();

                return response()->json([
                    'message' => 'Stock entry deleted successfully!'
                ], 200);
            }

            return response()->json([
                'message' => 'Stock entry not found or unauthorized'
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to delete stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }
}