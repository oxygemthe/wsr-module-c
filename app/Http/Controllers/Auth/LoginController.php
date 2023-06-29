<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function authentication(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (Auth::attempt($validated)) {
            $token = $request->user()->createToken('Bearer')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'token' => $token
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Login failed'
        ], 401);
    }

    public function registration(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => ['required', Password::min(3)->mixedCase()->numbers()],
            'first_name' => 'required',
            'last_name' => 'required'
        ]);
        $user = User::query()->create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'token' => $user->createToken('Bearer')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout'
        ]);
    }
}
