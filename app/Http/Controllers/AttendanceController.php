<?php

namespace App\Http\Controllers;

use Ably\AblyRest;
use Ably\Http;
use App\Helpers\Helper;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Types\Relations\Car;

class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $shift = $request->input('shift') ?? null;
        $status_checkin = $request->input('status_checkin') ?? null;
        $status_checkout = $request->input('status_checkout') ?? null;
        $company = $request->input('company') ?? null;
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        $att = Attendance::with(['user.shift', 'user.company', 'user.type'])->whereHas('user.shift', function ($q) use ($shift) {
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

        if ($start_date && $end_date) {
            $att->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('time_check_in', [$start_date, $end_date])
                      ->orWhereBetween('time_check_out', [$start_date, $end_date]);
            });
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
        $month = $request->input('month') ?? null;

        $user = User::select('id', 'name', 'nip', 'status')
            ->withCount([
                'attendance' => function ($q) use ($month) {
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                        ->whereMonth('time_check_in', $yearMonth->month);
                    }
                },
                'attendance as ontime_attendance' => function ($q) use ($month) {
                    $q->where('status_check_in', 2);
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                        ->whereMonth('time_check_in', $yearMonth->month);
                    }
                },
                'attendance as late_attendance' => function ($q) use ($month) {
                    $q->where('status_check_in', 3);
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                        ->whereMonth('time_check_in', $yearMonth->month);
                    }
                },
                'attendance as early_checkout' => function ($q) use ($month) {
                    $q->where('status_check_out', 1);
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                        ->whereMonth('time_check_in', $yearMonth->month);
                    }
                },
            ])
            ->when($keyword, function ($q) use ($keyword) {
                $q->orWhereRaw("LOWER(CAST(name AS TEXT)) LIKE ?", ['%' . $keyword . '%'])
                ->orWhereRaw("LOWER(CAST(nip AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($most_present, function ($q) {
                $q->orderBy('attendance_count', 'desc');
            })
            ->when($smallest_late, function ($q) {
                $q->orderBy('late_attendance', 'asc');
            })
            ->when($most_late, function ($q) {
                $q->orderBy('late_attendance', 'desc');
            })
            ->paginate($pageSize, ['*'], 'page', $page);




        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function post_att(Request $request){

        $checkin_time = str_replace("T", " ", $request->event_time);

        $attender = User::where('pin', $request->pin);

        if ($attender->exists()) {

            $attender = $attender->first();

            if($attender && $attender->shift_id){
                $shift = Shift::find($attender->shift_id);

                $time = Carbon::parse($checkin_time)->format('H:i:s');

                if($time < $shift->early_check_in){
                    $status = 1;
                }else if($time > $shift->early_check_in && $time < $shift->check_in){
                    $status = 2;
                }else{
                    $status = 3;
                }
            }

            $attendance = Attendance::create([
                'user_id' => $attender->id,
                'time_check_in' => $checkin_time,
                'status_check_in' => $status ?? 2,
            ]);
        }else{
            $new_attender = User::create([
                'name'              => $request->name,
                'email'             => strtolower(str_replace(" ", "", $request->name)) . '@gmail.com',
                'pin'               => $request->pin,
                'type_employee_id'  => $request->dept_code ?? 0,
            ]);

            $attendance = Attendance::create([
                'user_id' => $new_attender->id,
                'time_check_in' => $checkin_time,
                'status_check_in' => 2,
            ]);
    
            if ($attendance) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance recorded successfully',
                    'attendance' => $attendance
                ], 200);
            }
        }

        $data = [
            'name' => $attender->name,
            'accessStatus' => "Check In",
            'role' => $attender->getRoleNames(),
            'entryTime' => Carbon::now()->format('Y-m-d H:i:s'),
            'status' => Helper::statusAtt($status ?? 2)
        ];

        $ably = new AblyRest(env('ABLY_KEY'));

        $channel = $ably->channels->get('gate');
        $channel->publish('message', $data);

        if ($attendance) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully',
                'attendance' => $attendance
            ], 200);
        }
    }
}
