<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Guest;
use App\Models\AttendanceGuest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceGuestController extends Controller
{
    public function list(Request $request)
    {
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->addDay(): null;

        $data = AttendanceGuest::with('guest')->orderBy('created_at', 'desc');
        
        if ($start_date && $end_date) {
            $data->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('time_check_in', [$start_date, $end_date])
                      ->orWhereBetween('time_check_out', [$start_date, $end_date]);
            });
        }

        $att_guest = Helper::pagination($data, $request, ['guest.name']);

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
                    'phone_number' => $request->phone_number
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
                    'time_check_in' => Carbon::now()->format('Y-m-d H:i:s'),    
                    'need' => $request->need,
                    'type_vehicle' => $request->type_vehicle,
                    'no_police' => $request->no_police,
                    'total_guest' => $request->total_guest
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

            $timeCheckIn = Carbon::parse($att_guest->time_check_in);
            
            $diffInSeconds = $timeCheckIn->diffInSeconds(Carbon::now());
            $hours = floor($diffInSeconds / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);
            $seconds = $diffInSeconds % 60;
            $diff = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $att_guest->update([
                'time_check_out' => Carbon::now()->format('Y-m-d H:i:s'),
                'duration' => Carbon::parse($diff)->format('H:i:s')
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

    public function report(Request $request)
    {
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));
        $most_present = filter_var($request->input('most_present', false), FILTER_VALIDATE_BOOLEAN);
        $smallest_present = filter_var($request->input('smallest_present', false), FILTER_VALIDATE_BOOLEAN);
        $longest_duration = filter_var($request->input('longest_duration', false), FILTER_VALIDATE_BOOLEAN);
        $shortest_duration = filter_var($request->input('shortest_duration', false), FILTER_VALIDATE_BOOLEAN);
        $year = $request->input('year') ?? null;

        $guest = Guest::select('id', 'name', 'phone_number')
            ->when($year, function ($q) use ($year) {
                $q->whereHas('attendance_guest', function ($query) use ($year) {
                    $query->whereRaw("EXTRACT(YEAR FROM time_check_in) = ?", [$year]);
                });
            })
            ->withCount([
                'attendance_guest as total_attendance' => function ($query) use ($year) {
                    if ($year) {
                        $query->whereRaw("EXTRACT(YEAR FROM time_check_in) = ?", [$year]);
                    }
                },
            ])
            ->when($most_present, function ($q) {
                $q->orderBy('total_attendance', 'desc');
            })
            ->when($smallest_present, function ($q) {
                $q->orderBy('total_attendance', 'asc');
            })
            ->when($longest_duration, function ($q) {
                $q->addSelect([
                    'total_duration' => AttendanceGuest::selectRaw(
                        "SUM(EXTRACT(EPOCH FROM CAST(duration AS INTERVAL)))"
                    )
                        ->whereColumn('attendance_guests.guest_id', 'guests.id'),
                ])->orderBy('total_duration', 'desc');
            })
            ->when($shortest_duration, function ($q) {
                $q->addSelect([
                    'total_duration' => AttendanceGuest::selectRaw(
                        "SUM(EXTRACT(EPOCH FROM CAST(duration AS INTERVAL)))"
                    )
                        ->whereColumn('attendance_guests.guest_id', 'guests.id'),
                ])->orderBy('total_duration', 'asc');
            })
            ->when($keyword, function ($q) use ($keyword) {
                $q->orWhereRaw("LOWER(CAST(name AS TEXT)) LIKE ?", ['%' . strtolower($keyword) . '%'])
                    ->orWhereRaw("LOWER(CAST(phone_number AS TEXT)) LIKE ?", ['%' . strtolower($keyword) . '%']);
            })
            ->paginate($pageSize, ['*'], 'page', $page);

        $guest->getCollection()->transform(function ($q) use ($year) {
            $lastAttendance = AttendanceGuest::where('guest_id', $q->id)
                ->when($year, function ($query) use ($year) {
                    $query->whereRaw("EXTRACT(YEAR FROM time_check_in) = ?", [$year]);
                })
                ->latest()
                ->first();

            if ($lastAttendance) {
                $q->last_visit = $lastAttendance->created_at->format('Y-m-d H:i:s');
            } else {
                $q->last_visit = null;
            }

            $totalDuration = AttendanceGuest::where('guest_id', $q->id)
                ->when($year, function ($query) use ($year) {
                    $query->whereRaw("EXTRACT(YEAR FROM time_check_in) = ?", [$year]);
                })
                ->selectRaw(
                    "TO_CHAR(SUM(EXTRACT(EPOCH FROM CAST(duration AS INTERVAL))) * interval '1 second', 'HH24:MI:SS') as total_duration"
                )
                ->value('total_duration');

            $q->total_duration = $totalDuration;

            return $q;
        });

        return response()->json([
            'success' => true,
            'data' => $guest
        ], 200);
    }
}
