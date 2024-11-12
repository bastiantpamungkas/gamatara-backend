<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceGuest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function counts(){

        $emp = User::role('Employee')->count();

        $emp_present = User::role('Employee')->whereHas('attendance', function($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'));
        })->count();

        $emp_late = User::role('Employee')->whereHas('attendance', function($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->where('status_check_in', 3);
        })->count();
        
        $emp_not_present = User::role('Employee')->whereDoesntHave('attendance', function($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'));
        })->count();

        return response()->json([
            'employee_count' =>  $emp ?? 0,
            'employee_present' => $emp_present ?? 0,
            'employee_late' => $emp_late ?? 0,
            'employee_not_present' => $emp_not_present ?? 0,
        ], 200);
    }

    public function charts_employee(Request $request){

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

                $attendanceCount = Attendance::whereYear('time_check_in', $year)
                                 ->whereMonth('time_check_in', $month)
                                 ->whereNotNull('time_check_out')
                                 ->count();

                $val[] = $attendanceCount;
            } 
        }

        return response()->json([
            'year' => $year ?? null,
            'var' => $var ?? [],
            'val' => $val ?? []
        ],200);
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

                $attendanceCount = AttendanceGuest::whereYear('date', $year)
                                  ->whereMonth('date', $month)
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
