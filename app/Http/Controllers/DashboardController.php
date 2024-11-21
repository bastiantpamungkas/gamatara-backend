<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\TypeEmployee;
use App\Models\AttendanceGuest;
use App\Models\Guest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function counts(){

        $emp = User::count();

        $emp_present = User::whereHas('attendance', function($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'));
        })->count();

        $emp_late_in = User::whereHas('attendance', function($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->where('status_check_in', 3);
        })->count();
        
        $emp_early_out = User::whereDoesntHave('attendance', function($q) {
            $q->where('time_check_out', Carbon::now()->format('Y-m-d'))->where('status_check_out', 2);
        })->count();

        $employee_in = User::whereHas('attendance', function($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereNotNull('time_check_in')->whereNull('time_check_out');
        })->count();

        $guest_in = Guest::whereHas('attendance_guest',function($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereNotNull('time_check_in')->whereNull('time_check_out');
        })->count();
        
        $employee_out = User::whereHas('attendance', function($q) {
            $q->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->whereNotNull('time_check_in')->whereNotNull('time_check_out');
        })->count();
        
        $guest_out = Guest::whereHas('attendance_guest', function($q) {
            $q->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->whereNotNull('time_check_in')->whereNotNull('time_check_out');
        })->count();

        $people_in = intval($employee_in) + intval($guest_in);
        $people_out = intval($employee_out) + intval($guest_out);

        return response()->json([
            'employee_count' =>  $emp ?? 0,
            'employee_present' => $emp_present ?? 0,
            'employee_late_in' => $emp_late_in ?? 0,
            'employee_early_out' => $emp_early_out ?? 0,
            'people_in' => $people_in ?? 0,
            'people_out' => $people_out ?? 0
        ], 200);
    }

    public function charts_employee(Request $request) {
        $date_start = $request->input('date_start') ? Carbon::parse($request->input('date_start'))->format('Y-m-d') : Carbon::now()->subDays(6)->format('Y-m-d');
        $date_end = $request->input('date_end') ? Carbon::parse($request->input('date_end'))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $type_employee = $request->input('type_employee') ?? null;
    
        Carbon::setLocale('id');
    
        // Generate all days in the range
        $get_all_days = [];
        foreach (Carbon::parse($date_start)->toPeriod(Carbon::parse($date_end), '1 day') as $date) {
            $get_all_days[] = $date->translatedFormat('l');
        }
    
        // Fetch all employee types
        $type_employees = \App\Models\TypeEmployee::all();
        $data = [];
    
        foreach ($type_employees as $type) {
            // Check if type_employee is specified
            if ($type_employee && $type->id != $type_employee) {
                continue;
            }
    
            $current_date = Carbon::parse($date_start);
            $end_date = Carbon::parse($date_end);
            $attendance_counts = [];
    
            // Loop through each day in the range
            while ($current_date <= $end_date) {
                $attendance_count = Attendance::with('user')
                    ->whereHas('user', function ($query) use ($type) {
                        $query->where('type_employee_id', $type->id);
                    })
                    ->whereDate('time_check_in', $current_date->format('Y-m-d'))
                    ->whereNotNull('time_check_out')
                    ->count();
    
                $attendance_counts[] = $attendance_count; // Add count for this day
                $current_date->addDay();
            }
    
            // Add data for this type
            $data[] = [
                'type_employee_id' => $type->id,
                'type_employee_name' => $type->name,
                'attendance' => $attendance_counts,
            ];
        }
    
        return response()->json([
            'filter' => [
                'type_employee' => $type_employee,
                'date_start' => $date_start,
                'date_end' => $date_end,
            ],
            'days' => $get_all_days,
            'data' => $data, // Contains categorized attendance data
        ], 200);
    }    

    public function charts_late_and_not_present(Request $request){
        $year = $request->input('year') ?? Carbon::now()->year;

        if($year){
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;

            $var = [];
            $late = [];
            $not_present = [];

            $endMonth = ($year == $currentYear) ? $currentMonth : 12;

            for ($month = 1; $month <= $endMonth; $month++) {
                $var[] = [
                    'month' => $month,
                    'month_name' => Carbon::create($year, $month)->format('F') 
                ];

                $attendanceCountLate = Attendance::whereYear('time_check_in', $year)
                                 ->whereMonth('time_check_in', $month)
                                 ->where('status_check_in', 3)
                                 ->count();
                
                $attendanceCountNotPresent = User::role('Employee')->whereDoesntHave('attendance', function ($query) use ($year, $month) {
                    $query->whereYear('time_check_in', $year)
                            ->whereMonth('time_check_in', $month);
                })->count();

                $late[] = $attendanceCountLate;
                $not_present[] = $attendanceCountNotPresent;
            } 

        }
        return response()->json([
            'year' => $year ?? null,
            'var' => $var ?? [],
            'late' => $late ?? [],
            'not_present' => $not_present ?? [],
        ],200);
    }

    public function charts_guest(Request $request){
        $year = $request->input('year') ?? Carbon::now()->year;

        if($year){
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;

            $var = [];
            $val = [];

            $endMonth = ($year == $currentYear) ? $currentMonth : 12;

            for ($month = 1; $month <= $endMonth; $month++) {
                $var[] = [
                    'month' => $month,
                    'month_name' => Carbon::create($year, $month)->format('F') 
                ];

                $attendanceCount = AttendanceGuest::whereYear('time_check_in', $year)
                                  ->whereMonth('time_check_in', $month)
                                  ->whereNotNull('time_check_out')
                                  ->count();

                $val[] = $attendanceCount;
            } 
        }

        return response()->json([
            'year' => $year ?? null,
            'var' => $var ?? [],
            'val' => $val ?? [],
        ],200);   
    }
}
