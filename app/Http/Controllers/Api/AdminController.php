<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 📋 Get all pending pharmacist applications
    public function pendingPharmacists()
    {
        $pendingUsers = User::with('pharmacy')
                            ->where('role', 'pharmacist')
                            ->where('status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'count' => $pendingUsers->count(),
            'users' => $pendingUsers
        ]);
    }

    // ✅ Approve a pharmacist
    public function approvePharmacist($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'approved']);

        return response()->json([
            'message' => "Pharmacist {$user->name} approved successfully."
        ]);
    }

    // ❌ Reject a pharmacist
    public function rejectPharmacist($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'rejected']);

        return response()->json([
            'message' => "Pharmacist {$user->name} request rejected."
        ]);
    }
    
    public function approve($id)
    {
        $request = MedicineRequest::findOrFail($id);

        // 1. Create global medicine entry
        $medicine = Medicine::create([
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'category' => $request->category,
        ]);

        // 2. Mark request as approved
        $request->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Medicine request approved and added to global catalog.',
            'medicine' => $medicine,
        ]);
    }
}