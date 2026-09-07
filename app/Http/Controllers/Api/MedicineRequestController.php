<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use Exception;
use Illuminate\Http\Request;

class MedicineRequestController extends Controller
{
    // [CREATE] Pharmacist submits a request for a new medicine
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'category'     => 'nullable|string|max:255',
        ]);

        try {
            $user = $request->user();

            $medicineRequest = MedicineRequest::create([
                'user_id'      => $user->id,
                'pharmacy_id'  => $user->pharmacy_id,
                'name'         => $validated['name'],
                'generic_name' => $validated['generic_name'] ?? null,
                'category'     => $validated['category'] ?? null,
                'status'       => 'pending',
            ]);

            return response()->json([
                'message' => 'Medicine request submitted to admin successfully!',
                'request' => $medicineRequest,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to submit request',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // [READ] Admin fetches all requests
    public function index()
    {
        try {
            $requests = MedicineRequest::with(['user', 'pharmacy'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($requests, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch requests',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // [UPDATE] Admin approves request & inserts into global medicines table
    public function approve($id)
    {
        try {
            $medRequest = MedicineRequest::findOrFail($id);

            if ($medRequest->status !== 'pending') {
                return response()->json(['message' => 'Request has already been processed.'], 400);
            }

            // 1. Create global medicine definition
            $medicine = Medicine::create([
                'name'         => $medRequest->name,
                'generic_name' => $medRequest->generic_name,
                'category'     => $medRequest->category ?? 'General',
            ]);

            // 2. Mark request as approved
            $medRequest->update(['status' => 'approved']);

            return response()->json([
                'message'  => 'Medicine request approved and added to global catalog!',
                'medicine' => $medicine,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to approve request',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // [UPDATE] Admin rejects request
    public function reject($id)
    {
        try {
            $medRequest = MedicineRequest::findOrFail($id);

            if ($medRequest->status !== 'pending') {
                return response()->json(['message' => 'Request has already been processed.'], 400);
            }

            $medRequest->update(['status' => 'rejected']);

            return response()->json([
                'message' => 'Medicine request rejected.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to reject request',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}