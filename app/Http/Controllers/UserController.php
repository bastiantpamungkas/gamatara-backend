<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $user = Helper::pagination(User::with('type', 'company', 'shift'), $request, ['name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function store(Request $request)
    {
        $valid = Helper::validator($request->all(), [
            'nip' => 'required',
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'password' => 'required',
            'type_employee_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($valid == true) {
            try {
                $user = User::create($request->all());

                $user->assignRole($request->role);

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
        $user = User::with('type', 'company', 'shift')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Employee Not Found",
            ], 404);
        }

        $user->getRoleNames();

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $valid = Helper::validator($request->all(), [
            'nip' => 'required',
            'name' => 'required',
            'email' => 'email|required|unique:users,email,' . $id,
            'password' => 'required',
            'type_employee_id' => 'required',
            'company_id' => 'required',
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

                $user->syncRoles([]);

                $user->assignRole($request->role);

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

    public function update_status(Request $request){
        $ids = $request->input('ids');
        $status = $request->input('status', 1);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No IDs provided.'
            ], 422);
        }

        $user = User::whereIn('id', $ids);

        $user->update(['status' => $status]);


        return response()->json([
            'success' => true,
            'message' => 'Success Update Status Employee',
            'data' => User::whereIn('id', $ids)->get()
        ],200);
    }
    
    public function update_shift(Request $request){
        $ids = $request->input('ids');
        $shift_id = $request->input('shift_id', 1);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No IDs provided.'
            ], 422);
        }

        $user = User::whereIn('id', $ids);

        $user->update(['shift_id' => $shift_id]);


        return response()->json([
            'success' => true,
            'message' => 'Success Update Shift Employee',
            'data' => User::whereIn('id', $ids)->get()
        ],200);
    }
}
