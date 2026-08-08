<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Exception;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    // [CREATE] Add a new medicine
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'generic_name' => 'nullable|string|max:100',
            'category'     => 'nullable|string|max:50',
        ]);

        try {
            $medicine = Medicine::create($validated);

            return response()->json([
                'message'  => 'Medicine created successfully',
                'medicine' => $medicine
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to create medicine',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [READ ALL] Fetch all medicines
    public function index()
    {
        try {
            $medicines = Medicine::with('stocks.pharmacy')->get();

            return response()->json($medicines, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch medicines',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [READ ONE] Fetch specific medicine by ID
    public function show($id)
    {
        try {
            $medicine = Medicine::with('stocks.pharmacy')->where('id', $id)->first();

            if (!$medicine) {
                return response()->json([
                    'message' => 'Medicine not found'
                ], 404);
            }

            return response()->json($medicine, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch medicine',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [UPDATE] Update existing medicine
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'         => 'sometimes|string|max:100',
            'generic_name' => 'nullable|string|max:100',
            'category'     => 'nullable|string|max:50',
        ]);

        try {
            $medicine = Medicine::where('id', $id)->first();

            if (!$medicine) {
                return response()->json([
                    'message' => 'Medicine not found'
                ], 404);
            }

            $medicine->update($validated);

            return response()->json([
                'message'  => 'Medicine updated successfully!',
                'medicine' => $medicine
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to update medicine',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    // [DELETE] Remove a medicine
    public function destroy($id)
    {
        try {
            $medicine = Medicine::where('id', $id)->first();

            if ($medicine) {
                $medicine->delete();

                return response()->json([
                    'message' => 'Medicine deleted successfully!'
                ], 200);
            }

            return response()->json([
                'message' => 'Medicine not found'
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to delete medicine',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }
}