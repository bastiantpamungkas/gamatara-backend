<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $shift = $request->input('shift') ?? null;
        $status_checkin = $request->input('status_checkin') ?? null;
        $status_checkout = $request->input('status_checkout') ?? null;
        $company = $request->input('company') ?? null;

        $att = Attendance::with(['user.shift', 'user.company'])->whereHas('user.shift', function ($q) use ($shift) {
                if ($shift) {
                    $q->where('id', $shift);
                }
            })->whereHas('user.company', function ($q) use ($company) {
                if ($company) {
                    $q->where('id', $company);
                }
            });

        if($status_checkin){
            $att->where('status_check_in', $status_checkin);
        }
        
        if($status_checkout){
            $att->where('status_check_out', $status_checkout);
        }

        $att = Helper::pagination($att, $request, [
            'time_check_in',
            'time_check_out',
            'user.name',
            'user.nip'
        ]);

        return response()->json([
            'success' => true,
            'data' => $att
        ], 200);
    }

    public function list_per_employee(Request $request, $id)
    {
        $att = Helper::pagination(Attendance::with('user')->where('user_id', $id), $request, [
            'time_check_in',
            'time_check_out'
        ]);

        return response()->json([
            'success' => true,
            'data' => $att
        ], 200);
    }

    public function detail($id)
    {
        $att = Attendance::with('user')->where('id', $id)->first();

        if (!$att) {
            return response()->json([
                'success' => false,
                'message' => "Attendance Not Found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $att ?? []
        ], 200);
    }

    public function report(Request $request){
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));
        $status = $request->input('status') ?? null;
        $most_present = filter_var($request->input('most_present', false), FILTER_VALIDATE_BOOLEAN);
        $smallest_late = filter_var($request->input('smallest_late', false), FILTER_VALIDATE_BOOLEAN);
        $most_late = filter_var($request->input('most_late', false), FILTER_VALIDATE_BOOLEAN);

        $user = User::select('id', 'name', 'nip', 'status')->withCount(['attendance','attendance as ontime_attendance' => function ($q) {
                    $q->where('status_check_in', 2);
                },
                'attendance as late_attendance' => function ($q) {
                    $q->where('status_check_in', 3);
                },
                'attendance as early_checkout' => function ($q) {
                    $q->where('status_check_out', 1);
                },
            ])->when($keyword, function ($q) use ($keyword) {
                $q->orWhereRaw("LOWER(CAST(name AS TEXT)) LIKE ?", ['%' . $keyword . '%'])
                ->orWhereRaw("LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
            })->when($status, function($q) use ($status){
                $q->where('status', $status);
            })->when($most_present, function($q){
                $q->orderBy('attendance_count', 'desc');
            })->when($smallest_late, function($q){
                $q->orderBy('late_attendance', 'asc');
            })->when($most_late, function($q){
                $q->orderBy('late_attendance', 'desc');
            })->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function post_att(Request $request){

        $attender = User::where('pin', $request->pin)->first();

        $checkin_time = str_replace("T", " ", $request->event_time);

        if (!$attender) {
            $new_attender = User::create([
                'name'              => $request->name,
                'email'             => strtolower(str_replace(" ", "", $request->name)) . '@gmail.com',
                'pin'               => $request->pin,
                'type_employee_id'  => $request->dept_code == 1 ? 1 : 0,
            ]);

            $attendance = Attendance::create([
                'user_id' => $new_attender->id,
                'time_check_in' => $checkin_time,
                'status_check_in' => 2,
                'status_check_out' => 0,
            ]);
    
            if ($attendance) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance recorded successfully',
                    'attendance' => $attendance
                ], 200);
            }
        }

        // TODO: create attender in attendances table  
        $attendance = Attendance::create([
            'user_id' => $attender->id,
            'time_check_in' => $checkin_time,
            'status_check_in' => 2,
            'status_check_out' => 0,
        ]);

        if ($attendance) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully',
                'attendance' => $attendance
            ], 200);
        }

        // TODO: insert data (injection) from ZKTeco to Batik Payroll in table att_log
        // TODO: connect to ably and send message with channel gate
        
        Log::info([
            'data' => $request->all()
        ]);

        // return response()->json([
        //     'data' => [
        //         'nama_karyawan' => $request->name,
        //         'SN' => $request->dev_sn,
        //         'user_id' => $request->pin,
        //     ]
        // ]);
    }
}
