<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => "Login Success",
                'token' => $token
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => "Login Gagal",
        ], 401);
    }
}
