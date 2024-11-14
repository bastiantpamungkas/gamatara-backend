<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Guest;
use App\Models\AttendanceGuest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceGuestController extends Controller
{
    public function list(Request $request)
    {
        $att_guest = Helper::pagination(AttendanceGuest::with('guest'), $request, ['guest.name', 'date', 'time_check_in', 'time_check_out']);

        return response()->json([
            'success' => true,
            'data' => $att_guest
        ], 200);
    }
   
    public function list_per_guest(Request $request, $id)
    {
        $att_guest = Helper::pagination(AttendanceGuest::with('guest')->where('guest_id', $id), $request, ['guest.name', 'date', 'time_check_in', 'time_check_out']);

        return response()->json([
            'success' => true,
            'data' => $att_guest
        ], 200);
    }

    public function store(Request $request)
    {
        if($request->guest_id){
           
            $guest = Guest::find($request->guest_id);

            if(!$guest){
                return response()->json([
                    'success' => false,
                    'message' => 'Guest Not Found',
                ], 404);
            }

        }else{
            try{
                $guest = Guest::create([
                    'nik' => $request->nik,
                    'name' => $request->name,
                    'institution' => $request->institution,
                    'phone_number' => $request->phone_number,
                ]);

            }catch(\Exception $e){
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        }

        $valid = Helper::validator($request->all(), [
            'need' => 'required'
        ]);

        if($valid == true){
            try{
                $att_guest = AttendanceGuest::create([
                    'guest_id' => $guest->id,
                    'institution' => $request->institution,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'time_check_in' => Carbon::now()->format('H:i:s'),
                    'need' => $request->need 
                ]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Success Added Guest And Attendance',
                    'data' => [
                        'guest' => $guest,
                        'attendace' => $att_guest
                    ]
                ], 200);
    
            }catch(\Exception $e){
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed Added Guest And Attendance',
        ], 422);
    }

    public function update($id)
    {
        try{
            $att_guest = AttendanceGuest::find($id);

            if(!$att_guest){
                return response()->json([
                    'success' => false,
                    'message' => 'Attendace Not Found',
                ], 404);
            }

            $att_guest->update([
                'time_check_out' => Carbon::now()->format('H:i:s')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Success Update Attendance',
                'data' => $att_guest
            ], 200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }


        return response()->json([
            'success' => false,
            'message' => 'Failed Update Attendance',
        ], 422);
       
    }

    public function report(Request $request){
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));

        $guest = Guest::select('id', 'name', 'phone_number')
            ->withCount([
                'attendance_guest',
            ])
            ->when($keyword, function ($q) use ($keyword) {
                $q->orWhereRaw("LOWER(CAST(name AS TEXT)) LIKE ?", ['%' . $keyword . '%'])
                ->orWhereRaw("LOWER(CAST(phone_number AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
            })
            ->paginate($pageSize, ['*'], 'page', $page);

        $guest->getCollection()->transform(function($q) {
            $lastAttendance = $q->attendance_guest()->latest()->first();

            if ($lastAttendance) {
                $q->last_visit = $lastAttendance->created_at->format('Y-m-d H:i:s');
            } else {
                $q->last_visit = null;
            }

            return $q;
        });

        return response()->json([
            'success' => true,
            'data' => $guest
        ], 200);
    }
}
