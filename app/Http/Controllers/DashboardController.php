<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\TypeEmployee;
use App\Models\AttendanceGuest;
use App\Models\Guest;
use App\Models\User;
use App\Models\AttLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function counts()
    {

        $emp = User::where('status', 1)->count();

        $emp_present = User::where('status', 1)->whereHas('attendance', function ($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'));
        })->count();

        $emp_absent = User::where('status', 1)->whereDoesntHave('attendance', function ($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'));
        })->count();

        $emp_late_in = User::where('status', 1)->whereHas('attendance', function ($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->where('status_check_in', 3);
        })->count();

        $emp_early_out = User::where('status', 1)->whereHas('attendance', function ($q) {
            $q->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->where('status_check_out', 2);
        })->count();

        $employee_in = User::where('status', 1)->whereHas('att_log', function ($q) {
            $q->where(function ($q_group) {
                $q_group->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereNull('time_check_out')->whereNull('total_time');
            });
            $q->orWhere(function ($q_group) {
                $q_group->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->whereNotNull('total_time');
            });
        })
        ->where(function ($q_group) {
            $q_group->whereHas('roles', function ($q) {
                $q->where('name', '!=', 'Tamu');
            })
            ->orWhereDoesntHave('roles');
        })
        ->whereDoesntHave('att_log', function ($q) {
            $q->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->whereNull('time_check_in')->whereNull('total_time');
        })
        ->count();

        $guest_in = AttendanceGuest::whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereNull('time_check_out')->sum('total_guest');

        $employee_out = User::where('status', 1)->whereHas('att_log', function ($q) {
            $q->where(function ($q_group) {
                $q_group->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->whereNull('time_check_in')->whereNull('total_time');
            });
            // $q->where(function ($q_group) {
            //     $q_group->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereNull('time_check_out')->whereNull('total_time');
            // });
        })
        ->where(function ($q_group) {
            $q_group->whereHas('roles', function ($q) {
                $q->where('name', '!=', 'Tamu');
            })
            ->orWhereDoesntHave('roles');
        })
        // ->whereDoesntHave('att_log', function ($q) {
        //     $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->whereNotNull('total_time');
        // })
        ->count();

        $guest_out = AttendanceGuest::whereDate('time_check_out', Carbon::now()->format('Y-m-d'))->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->sum('total_guest');

        $tap_card = AttLog::where(function ($q_group) {
            $q_group->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->orWhereDate('time_check_out', Carbon::now()->format('Y-m-d'));
        })
        ->whereHas('user', function ($q) {
            $q->where('status', 1);
            $q->whereHas('roles', function ($q_role) {
                $q_role->where('name', 'Tamu');
            });
        })->count();

        $total_guest = AttendanceGuest::whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->orWhereDate('time_check_out', Carbon::now()->format('Y-m-d'))->sum('total_guest');

        $people_in = intval($employee_in) + intval($guest_in);
        $people_out = intval($employee_out) + intval($guest_out);

        $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->where('status', 1)->with('type')->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        $type = TypeEmployee::get();
        if ($type) {
            $type = $type->map(function ($item, $key) use ($users) {
                $item->total = 0;
                foreach ($users as $row) {
                    if ($row->type_employee_id == $item->id) {
                        $item->total = $row->total;
                    }
                }
                return $item;
            });
        }
        $employee_count_data = $type;

        $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->where('status', 1)->with('type', 'attendance')->whereHas('attendance', function ($q) {
            $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'));
        })->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        $type = TypeEmployee::get();
        if ($type) {
            $type = $type->map(function ($item, $key) use ($users) {
                $item->total = 0;
                foreach ($users as $row) {
                    if ($row->type_employee_id == $item->id) {
                        $item->total = $row->total;
                    }
                }
                return $item;
            });
        }
        $employee_present_data = $type;

        // $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->with('type', 'attendance')->whereDoesntHave('attendance', function ($q) {
        //     $start_date = Date("Y-m-d");
        //     $end_date = Date("Y-m-d", strtotime("+1 day"));

        //     $q->whereBetween('time_check_out', [$start_date, $end_date])
        //         ->orWhereBetween('time_check_in', [$start_date, $end_date]);
        // })->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        // $type = TypeEmployee::get();
        // if ($type) {
        //     $type = $type->map(function ($item, $key) use ($users) {
        //         $item->total = 0;
        //         foreach ($users as $row) {
        //             if ($row->type_employee_id == $item->id) {
        //                 $item->total = $row->total;
        //             }
        //         }
        //         return $item;
        //     });
        // }
        // $employee_absent_data = $type;
        $employee_absent_data = [];

        // $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->with('type', 'attendance')->whereHas('attendance', function ($q) {
        //     $start_date = Date("Y-m-d");
        //     $end_date = Date("Y-m-d", strtotime("+1 day"));

        //     $q->where('status_check_in', 3);
        //     $q->whereBetween('time_check_out', [$start_date, $end_date])
        //         ->orWhereBetween('time_check_in', [$start_date, $end_date]);
        // })->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        // $type = TypeEmployee::get();
        // if ($type) {
        //     $type = $type->map(function ($item, $key) use ($users) {
        //         $item->total = 0;
        //         foreach ($users as $row) {
        //             if ($row->type_employee_id == $item->id) {
        //                 $item->total = $row->total;
        //             }
        //         }
        //         return $item;
        //     });
        // }
        // $employee_late_in_data = $type;
        $employee_late_in_data = [];

        // $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->with('type', 'attendance')->whereHas('attendance', function ($q) {
        //     $start_date = Date("Y-m-d");
        //     $end_date = Date("Y-m-d", strtotime("+1 day"));

        //     $q->where('status_check_out', 2);
        //     $q->whereBetween('time_check_out', [$start_date, $end_date])
        //         ->orWhereBetween('time_check_in', [$start_date, $end_date]);
        // })->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        // $type = TypeEmployee::get();
        // if ($type) {
        //     $type = $type->map(function ($item, $key) use ($users) {
        //         $item->total = 0;
        //         foreach ($users as $row) {
        //             if ($row->type_employee_id == $item->id) {
        //                 $item->total = $row->total;
        //             }
        //         }
        //         return $item;
        //     });
        // }
        // $employee_early_out_data = $type;
        $employee_early_out_data = [];

        // $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->with('type', 'att_log')->whereHas('att_log', function ($q) {
        //     $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))
        //         ->whereNotNull('time_check_in');
        // })->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        // $type = TypeEmployee::get();
        // if ($type) {
        //     $type = $type->map(function ($item, $key) use ($users) {
        //         $item->total = 0;
        //         foreach ($users as $row) {
        //             if ($row->type_employee_id == $item->id) {
        //                 $item->total = $row->total;
        //             }
        //         }
        //         return $item;
        //     });
        // }
        // $people_in_data = $type;
        $people_in_data = [];

        // $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->with('type', 'att_log')->whereHas('att_log', function ($q) {
        //     $q->whereDate('time_check_out', Carbon::now()->format('Y-m-d'))
        //         ->whereNotNull('time_check_out');
        // })->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        // $type = TypeEmployee::get();
        // if ($type) {
        //     $type = $type->map(function ($item, $key) use ($users) {
        //         $item->total = 0;
        //         foreach ($users as $row) {
        //             if ($row->type_employee_id == $item->id) {
        //                 $item->total = $row->total;
        //             }
        //         }
        //         return $item;
        //     });
        // }
        // $people_out_data = $type;
        $people_out_data = [];

        // $users = User::selectRaw('type_employee_id, count(type_employee_id) as total')->with('type', 'att_log')->whereHas('att_log', function ($q) {
        //     $q->whereDate('time_check_in', Carbon::now()->format('Y-m-d'))->orWhereDate('time_check_out', Carbon::now()->format('Y-m-d'));
        // })->orderBy('type_employee_id')->groupBy('type_employee_id')->get();

        // $type = TypeEmployee::get();
        // if ($type) {
        //     $type = $type->map(function ($item, $key) use ($users) {
        //         $item->total = 0;
        //         foreach ($users as $row) {
        //             if ($row->type_employee_id == $item->id) {
        //                 $item->total = $row->total;
        //             }
        //         }
        //         return $item;
        //     });
        // }
        // $tap_card_data = $type;
        $total_guest_data = [];

        return response()->json([
            'employee_count' =>  $emp ?? 0,
            'employee_count_data' => $employee_count_data ?? [],
            'employee_present' => $emp_present ?? 0,
            'employee_present_data' => $employee_present_data ?? [],
            'employee_absent' => $emp_absent ?? 0,
            'employee_absent_data' => $employee_absent_data ?? [],
            'employee_late_in' => $emp_late_in ?? 0,
            'employee_late_in_data' => $employee_late_in_data ?? [],
            'employee_early_out' => $emp_early_out ?? 0,
            'employee_early_out_data' => $employee_early_out_data ?? [],
            'people_in' => $people_in ?? 0,
            'people_in_data' => $people_in_data ?? [],
            'people_out' => $people_out ?? 0,
            'people_out_data' => $people_out_data ?? [],
            'tap_card' => $tap_card ?? 0,
            'tap_card_data' => $tap_card_data ?? [],
            'total_guest' => $total_guest ?? 0,
            'total_guest_data' => $total_guest_data ?? [],
        ], 200);
    }

    public function charts_employee(Request $request)
    {
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
        $data_type = TypeEmployee::query();
        $data_type->when($type_employee, function ($query) use ($type_employee) {
            $query->where('id', $type_employee);
        });
        $type_employees = $data_type->get();
        $colors[1] = '#1E88E5';
        $colors[2] = '#FDD835';
        $colors[3] = '#E91E63';

        $data = [];
        $color_cart = [];

        foreach ($type_employees as $type) {
            // Check if type_employee is specified
            // if ($type_employee && $type->id != $type_employee) {
            //     continue;
            // }

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

            if (isset($colors[$type->id])) {
                $color_cart[] = $colors[$type->id];
            }
        }

        return response()->json([
            'filter' => [
                'type_employee' => $type_employee,
                'date_start' => $date_start,
                'date_end' => $date_end,
            ],
            'colors' => $color_cart,
            'days' => $get_all_days,
            'data' => $data, // Contains categorized attendance data
        ], 200);
    }

    public function charts_late_and_not_present(Request $request)
    {
        $year = $request->input('year') ?? Carbon::now()->year;

        if ($year) {
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
        ], 200);
    }

    public function charts_guest(Request $request)
    {
        $year = $request->input('year') ?? Carbon::now()->year;

        if ($year) {
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
        ], 200);
    }
}
