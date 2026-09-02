<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 📝 Register new user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'nullable|string|in:patient,pharmacist,admin',
        ]);

        $role = $validated['role'] ?? 'patient';
        // Pharmacists default to 'pending', others to 'approved'
        $status = ($role === 'pharmacist') ? 'pending' : 'approved';

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $role,
            'status'   => $status,
        ]);

        // If pending, do not issue a token
        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'Registration submitted successfully. Your pharmacy account is pending admin approval.',
                'user'    => $user,
            ], 201);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user,
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
            'user'    => $user,
        ], 200);
    }

    // 🚪 Logout user (Revoke token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}