<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Exception;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
   //[CREATE] Create a new pharmacy
   public function store(Request $request)
   {
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'location' => 'required|string|max:100',
        'phone' => 'nullable|string|max:20',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        ]);
   

   try {
    $pharmacy = Pharmacy::create($validated);
    
    return response()->json([
        'message' => 'Pharmacy created successfully',
        'pharmacy' => $pharmacy,
    ], 201);
  } 
   catch (Exception $exception) {
    return response()->json([
        'message' => 'Failed to create pharamacy',
        'error' => $exception->getMessage(),
    ],500);
   }
  
}


    //[READ] Fetch/Read all pharmacies
    public function index()
    {
        try {
            $pharmacies = Pharmacy::with('stocks.medicine')->get();

            return response()->json($pharmacies, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message'=>'Failed to retrieve pharmacies',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

   // [READ ONE] Fetch specific pharmacy by ID
    public function show($id)
    {
        try {
            $pharmacy = Pharmacy::with('stocks.medicine')->where('id', $id)->first();

            if (!$pharmacy) {
                return response()->json([
                    'message' => 'Pharmacy not found'
                ], 404);
            }

            return response()->json($pharmacy, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to fetch pharmacy',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }


    // [UPDATE] Update existing pharmacy
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'      => 'sometimes|string|max:100',
            'location'  => 'sometimes|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $pharmacy = Pharmacy::where('id', $id)->first();

            if (!$pharmacy) {
                return response()->json([
                    'message' => 'Pharmacy not found(updating non-existant pharmacy'
                ], 404);
            }

            $pharmacy->update($validated);

            return response()->json([
                'message'  => 'Pharmacy updated successfully!',
                'pharmacy' => $pharmacy
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to update pharmacy',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }

    
    // [DELETE] Remove a pharmacy
    public function destroy($id)
    {
        try {
            $pharmacy = Pharmacy::where('id', $id)->first();

            if ($pharmacy) {
                $pharmacy->delete();

                return response()->json([
                    'message' => 'Pharmacy deleted successfully!'
                ], 200);
            }

            return response()->json([
                'message' => 'Pharmacy not found'
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to delete pharmacy',
                'error'   => $exception->getMessage()
            ], 500);
        }
    }
}