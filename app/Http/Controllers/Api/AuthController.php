<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 📝 Register new user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8',
            'role'          => 'nullable|string|in:patient,pharmacist,admin',
            // Conditional pharmacy fields (required only if role is pharmacist)
            'pharmacy_name' => 'required_if:role,pharmacist|nullable|string|max:255',
            'location'      => 'required_if:role,pharmacist|nullable|string|max:255',
        ]);

        $role = $validated['role'] ?? 'patient';
        // Pharmacists default to 'pending', others to 'approved'
        $status = ($role === 'pharmacist') ? 'pending' : 'approved';

        // Execute pharmacy and user creation in a single transaction
        $user = DB::transaction(function () use ($validated, $role, $status) {
            $pharmacyId = null;

            // If the user registering is a pharmacist, create their pharmacy location first
            if ($role === 'pharmacist') {
                $pharmacy = Pharmacy::create([
                    'name'     => $validated['pharmacy_name'],
                    'location' => $validated['location'],
                ]);

                $pharmacyId = $pharmacy->id;
            }

            // Create the user account with the linked pharmacy_id
            return User::create([
                'name'        => $validated['name'],
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
                'role'        => $role,
                'status'      => $status,
                'pharmacy_id' => $pharmacyId,
            ]);
        });

        // If pending, do not issue a token
        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'Registration submitted successfully. Your pharmacy account is pending admin approval.',
                'user'    => $user->load('pharmacy'),
            ], 201);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user->load('pharmacy'),
        ], 201);
    }

    // 🔑 Login existing user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Block unapproved accounts from logging in
        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'Your account is currently pending admin approval.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user->load('pharmacy'),
        ], 200);
    }

    // 🚪 Logout user (Revoke token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}