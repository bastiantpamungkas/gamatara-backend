<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $user = Helper::pagination(User::with('type', 'company'), $request, ['name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function store(Request $request)
    {
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'required',
            'shift_id' => 'required'
        ]);

        if ($valid == true) {
            try {
                $user = User::create($request->all());

                $user->assignRole('Employee');

                return response()->json([
                    'success' => true,
                    'message' => "Success Added Employee",
                    'data' => $user
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Added Employee",
        ], 422);
    }

    public function detail($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Employee Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $valid = Helper::validator($request->all(), [
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'required',
            'shift_id' => 'required'
        ]);

        if ($valid == true) {
            try {
                $user = User::find($id);

                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => "Employee Not Found",
                    ], 404);
                }

                $user->update($request->all());

                return response()->json([
                    'success' => true,
                    'message' => "Success Updated Employee",
                    'data' => $user
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Updated Employee",
        ], 422);
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'message' => "Employee Not Found",
                ], 200);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => "Success Deleted Employee",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
