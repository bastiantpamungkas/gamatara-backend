<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            if (Auth::attempt($credentials)) {

                if (Auth::user()->hasRole(['Admin', 'Security'])) {
                    $user = Auth::user();
                    $user->getRoleNames();
                    $user->getAllPermissions();

                    $token = $user->createToken('API Token')->plainTextToken;

                    return response()->json([
                        'success' => true,
                        'message' => "Login Success",
                        'token' => $token,
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
        $token = Auth::user()->currentAccessToken();

        if ($token) {
            $token->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout Success'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token Not Found'
        ], 422);
    }
}
