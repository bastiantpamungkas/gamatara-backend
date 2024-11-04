<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FaceScanController extends Controller
{
    public function facescan(Request $request)
    {
        if ($request->type == 'attlog') {

            $datas = $request->data;

            $user = User::where('pin', $datas['pin']);

            if ($user->exists()) {

                Attendance::create([
                    'user_id' => $user->first()->id,
                    'scan' => Carbon::parse($datas['scan'])->format('Y-m-d H:i:s'),
                    'verify' => $datas['verify'],
                    'status_scan' => $datas['status_scan']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'attlog',
                    'data' => $request->data
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN tidak sesuai',
                ], 400);
            }
        }

        if ($request->type == 'get_userinfo') {
            return response()->json([
                'success' => true,
                'message' => 'get_userinfo',
                'data' => $request->data
            ]);
        }

        if ($request->type == 'set_userinfo') {
            return response()->json([
                'success' => true,
                'message' => 'set_userinfo',
                'data' => $request->data
            ]);
        }

        if ($request->type == 'delete_userinfo') {
            return response()->json([
                'success' => true,
                'message' => 'delete_userinfo',
                'data' => $request->data
            ]);
        }

        if ($request->type == 'get_userid_list') {
            return response()->json([
                'success' => true,
                'message' => 'get_userid_list',
                'data' => $request->data
            ]);
        }

        if ($request->type == 'set_time') {
            return response()->json([
                'success' => true,
                'message' => 'set_time',
                'data' => $request->data
            ]);
        }

        if ($request->type == 'register_online') {
            return response()->json([
                'success' => true,
                'message' => 'register_online',
                'data' => $request->data
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Tidak Ada Type"
        ]);
    }
}
