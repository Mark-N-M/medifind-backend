<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 📋 Get all pending pharmacist applications
    public function pendingPharmacists()
    {
        $pendingUsers = User::where('role', 'pharmacist')
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
}