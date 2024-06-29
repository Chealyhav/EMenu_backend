<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAllUser()
    {
        $users = User::with('roles')->get();

        return response()->json([
            'users' => $users,
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user->password == $request->password) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response([
                                'user' => $user,
                                'token' => $token,
                            ], 200);
            }
        }

        return response([
                    'message' => 'Invalid email or password',
                ], 401);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logged out',
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}
