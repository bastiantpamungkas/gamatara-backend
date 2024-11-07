<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $valid = Helper::validator($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($valid == true) {
            if ($token = Auth::attempt($credentials)) {

                if (Auth::user()->hasRole(['Admin', 'Security'])) {
                    $user = Auth::user();
                    $user->getRoleNames();
                    $user->getAllPermissions();

                    return response()->json([
                        'success' => true,
                        'message' => "Login Success",
                        'token' => $token,
                        'token_type' => 'bearer',
                        'user' => $user
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "You Don't Have Access",
                    ], 401);
                }
            }

            return response()->json([
                'success' => false,
                'message' => "Login Failed",
            ], 422);
        }
    }

    public function me()
    {
        $user = Auth::user();
        $user->getRoleNames();
        $user->getAllPermissions();

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
    
            return response()->json([
                'success' => true,
                'message' => 'Logout Success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'succes' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
