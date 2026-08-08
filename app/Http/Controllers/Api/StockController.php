<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Exception;
use Illuminate\Http\Request;

class StockController extends Controller
{
    // [CREATE] Link a medicine to a pharmacy with price & availability
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'medicine_id' => 'required|exists:medicines,id',
            'price'       => 'required|numeric|min:0',
            'in_stock'    => 'required|boolean',
        ]);

        try {
            $stock = Stock::create($validated);

            return response()->json([
                'message' => 'Stock entry created successfully',
                'stock'   => $stock
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to create stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [READ ALL] Fetch all stock records with pharmacy & medicine details
    public function index()
    {
        try {
            $stocks = Stock::with(['pharmacy', 'medicine'])->get();

            return response()->json($stocks, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch stock list',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [READ ONE] Fetch specific stock entry by ID
    public function show($id)
    {
        try {
            $stock = Stock::with(['pharmacy', 'medicine'])->where('id', $id)->first();

            if (!$stock) {
                return response()->json([
                    'message' => 'Stock entry not found'
                ], 404);
            }

            return response()->json($stock, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [UPDATE] Update stock price or availability
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pharmacy_id' => 'sometimes|exists:pharmacies,id',
            'medicine_id' => 'sometimes|exists:medicines,id',
            'price'       => 'sometimes|numeric|min:0',
            'in_stock'    => 'sometimes|boolean',
        ]);

        try {
            $stock = Stock::where('id', $id)->first();

            if (!$stock) {
                return response()->json([
                    'message' => 'Stock entry not found'
                ], 404);
            }

            $stock->update($validated);

            return response()->json([
                'message' => 'Stock entry updated successfully!',
                'stock'   => $stock
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to update stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [DELETE] Remove a stock entry
    public function destroy($id)
    {
        try {
            $stock = Stock::where('id', $id)->first();

            if ($stock) {
                $stock->delete();

                return response()->json([
                    'message' => 'Stock entry deleted successfully!'
                ], 200);
            }

            return response()->json([
                'message' => 'Stock entry not found'
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to delete stock entry',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }
}