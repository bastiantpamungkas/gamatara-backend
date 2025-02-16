<?php

namespace App\Http\Controllers;

use Ably\AblyRest;
use Ably\Http;
use App\Helpers\Helper;
use App\Models\Attendance;
use App\Models\AttLog;
use App\Models\AttLogBatik;
use App\Models\MachineSetting;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\User;
use App\Models\AccTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Types\Relations\Car;
use App\Jobs\JobPostBatik;
use App\Jobs\JobPostAtt;
use App\Jobs\JobResetAttendance;
use App\Exports\AttendanceReport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $shift = $request->input('shift') ?? null;
        $status_checkin = $request->input('status_checkin') ?? null;
        $status_checkout = $request->input('status_checkout') ?? null;
        $company = $request->input('company') ?? null;
        $type_employee_id = $request->input('type_employee_id') ?? null;
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->addDay() : null;
        $keyword = $request->input('keyword') ?? null;
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $att = Attendance::with(['user.shift', 'user.company', 'user.type'])
        ->whereHas('user', function ($q) use ($keyword) {
            $q->where('status', 1)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'ilike', '%'.$keyword.'%');
                $query->orWhere("nip", $keyword);
            });
        })
        ->when($shift, function ($query) use ($shift) {
            $query->whereHas('user.shift', function ($q) use ($shift) {
                $q->where('id', $shift);
            });
        })
        ->when($company, function ($query) use ($company) {
            $query->whereHas('user.company', function ($q) use ($company) {
                $q->where('id', $company);
            });
        })
        ->when($type_employee_id, function ($query) use ($type_employee_id) {
            $query->whereHas('user.type', function ($q) use ($type_employee_id) {
                $q->where('id', $type_employee_id);
            });
        })
        ->when($status_checkin, function ($query) use ($status_checkin) {
            $query->where('status_check_in', $status_checkin);
        })
        ->when($status_checkout, function ($query) use ($status_checkout) {
            $query->where('status_check_out', $status_checkout);
        })
        ->when(($start_date && $end_date), function ($query) use ($start_date, $end_date) {
            $query->whereBetween('time_check_in', [$start_date, $end_date]);
        });

        $att = Helper::pagination($att->orderBy($sort, $sortDirection), $request, [
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
        $att = Helper::pagination(Attendance::with('user')->where('user_id', $id)->orderBy('created_at', 'desc'), $request, [
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

    public function report(Request $request)
    {
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));
        $status = $request->input('status') ?? null;
        $most_present = filter_var($request->input('most_present', false), FILTER_VALIDATE_BOOLEAN);
        $smallest_late = filter_var($request->input('smallest_late', false), FILTER_VALIDATE_BOOLEAN);
        $most_late = filter_var($request->input('most_late', false), FILTER_VALIDATE_BOOLEAN);
        $month = $request->input('month') ?? null;
        $type_employee_id = $request->input('type_employee_id') ?? null;
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');
        $exportToExcel = $request->input('exportToExcel', false);

        $user = User::select('id', 'name', 'nip', 'status')
            ->with(['attendance' => function ($query) use ($month, $start_date, $end_date) {
                if ($month) {
                    $yearMonth = Carbon::parse($month);
                    $query->whereYear('time_check_in', $yearMonth->year)
                        ->whereMonth('time_check_in', $yearMonth->month);
                }
                if ($start_date && $end_date) {
                    $query->whereBetween('time_check_in', [$start_date, $end_date . ' 23:59:59' ]);
                }
            }])
            ->withCount([
                'attendance' => function ($q) use ($month, $start_date, $end_date) {
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                            ->whereMonth('time_check_in', $yearMonth->month);
                    }
                    if ($start_date && $end_date) {
                        $q->whereBetween('time_check_in', [$start_date, $end_date . ' 23:59:59' ]);
                    }
                },
                'attendance as ontime_attendance' => function ($q) use ($month, $start_date, $end_date) {
                    $q->where('status_check_in', 2);
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                            ->whereMonth('time_check_in', $yearMonth->month);
                    }
                    if ($start_date && $end_date) {
                        $q->whereBetween('time_check_in', [$start_date, $end_date . ' 23:59:59']);
                    }
                },
                'attendance as late_attendance' => function ($q) use ($month, $start_date, $end_date) {
                    $q->where('status_check_in', 3);
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                            ->whereMonth('time_check_in', $yearMonth->month);
                    }
                    if ($start_date && $end_date) {
                        $q->whereBetween('time_check_in', [$start_date, $end_date . ' 23:59:59']);
                    }
                },
                'attendance as early_checkout' => function ($q) use ($month, $start_date, $end_date) {
                    $q->where('status_check_out', 1);
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                            ->whereMonth('time_check_in', $yearMonth->month);
                    }
                    if ($start_date && $end_date) {
                        $q->whereBetween('time_check_in', [$start_date, $end_date . ' 23:59:59']);
                    }
                },
            ])
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $q->where('name', 'ilike', '%' . $keyword . '%')
                        ->orWhere('nip', 'ilike', '%' . $keyword . '%');
                });
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
            ->when($sort && $sortDirection, function ($q) use ($sort, $sortDirection) {
                $q->orderBy($sort, $sortDirection);
            })
            ->when($type_employee_id, function ($q) use ($type_employee_id) {
                $q->where('type_employee_id', $type_employee_id);
            })
            ->when($start_date && $end_date, function ($q) use ($month, $start_date, $end_date) {
                $q->whereHas('attendance', function ($query) use ($month, $start_date, $end_date) {
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $query->whereYear('time_check_in', $yearMonth->year)
                            ->whereMonth('time_check_in', $yearMonth->month);
                    }
                    if ($start_date && $end_date) {
                        $query->whereBetween('time_check_in', [$start_date, $end_date . ' 23:59:59']);
                    }
                });
            })->paginate($pageSize, ['*'], 'page', $page);

        if ($exportToExcel) {
            $excel_collection = collect();
            $user->map(function ($item) use ($excel_collection) {
                if ($item->attendance) {
                    $item->attendance->map(function ($att) use ($item, $excel_collection) {
                        $excel_collection->add([
                            'Nama' => $item->name,
                            'NIK' => $item->nip,
                            'Jam Masuk' => $att->time_check_in,
                            'Jam Keluar' => $att->time_check_out,
                        ]);
                    });
                }
            });

            if ($excel_collection && $excel_collection->count()) {
                $data = $excel_collection->toArray();
            }

            return Excel::download(new AttendanceReport($data), 'attendance_report.xlsx');
        } else {
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        }
    }

    public function report_detail(Request $request)
    {
        $shift = $request->input('shift') ?? null;
        $status_checkin = $request->input('status_checkin') ?? null;
        $status_checkout = $request->input('status_checkout') ?? null;
        $company = $request->input('company') ?? null;
        $type_employee_id = $request->input('type_employee_id') ?? null;
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->addDay() : null;
        $keyword = $request->input('keyword') ?? null;
        $status = $request->input('status') ?? null;
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $att = Attendance::with(['user.shift', 'user.company', 'user.type', 'shift'])
        ->whereHas('user', function ($q) use ($keyword, $status) {
            $q->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'ilike', '%'.$keyword.'%');
                $query->orWhere("nip", $keyword);
            });
        })
        ->when($shift, function ($query) use ($shift) {
            $query->whereHas('user.shift', function ($q) use ($shift) {
                $q->where('id', $shift);
            });
        })
        ->when($company, function ($query) use ($company) {
            $query->whereHas('user.company', function ($q) use ($company) {
                $q->where('id', $company);
            });
        })
        ->when($type_employee_id, function ($query) use ($type_employee_id) {
            $query->whereHas('user.type', function ($q) use ($type_employee_id) {
                $q->where('id', $type_employee_id);
            });
        })
        ->when($status_checkin, function ($query) use ($status_checkin) {
            $query->where('status_check_in', $status_checkin);
        })
        ->when($status_checkout, function ($query) use ($status_checkout) {
            $query->where('status_check_out', $status_checkout);
        })
        ->when(($start_date && $end_date), function ($query) use ($start_date, $end_date) {
            $query->whereBetween('time_check_in', [$start_date, $end_date]);
        });

        $att = Helper::pagination($att->orderBy($sort, $sortDirection), $request, [
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

    public function post_att(Request $request)
    {
        return response()->json([
            'success'    => true,
            'message'    => 'Attendance recorded successfully',
        ], 200);
    }

    public function post_att_v1(Request $request)
    {
        $check_time = str_replace("T", " ", $request->event_time);
        $attender = User::with('shift')->where('pin', $request->pin)->first();
        $st_inorout = MachineSetting::where('sn_machine', $request->dev_sn)->first();

        $status = function ($time, $shift, $early, $normal) {
            return $time < $shift->$early ? 1 : ($time < $shift->$normal ? 2 : 3);
        };

        if (!$attender) {
            $attender = $this->userCreate($request);
        }

        $shift = $attender->shift_id ? $attender->shift : null;
        $time = Carbon::parse($check_time)->format('H:i:s');
        $status_in = $shift ? $status($time, $shift, 'early_check_in', 'check_in') : 2;
        $status_out = $shift ? $status($time, $shift, 'early_check_out', 'check_out') : 2;

        // dd($st_inorout);

        try {
            $greeting = $st_inorout && $st_inorout->status == "IN" ? "Selamat Datang" : "Sampai Jumpa";
            $entry_time_status = $st_inorout && $st_inorout->status == "IN" ? "Masuk " : "Keluar ";
            $access_status = $st_inorout && $st_inorout->status == "IN" ? "Check In" : "Check Out";

            $this->publishToAbly('gate', [
                'name'         => $attender->name,
                'greeting'     => $greeting,
                'accessStatus' => $access_status,
                'role'         => $attender->getRoleNames(),
                'entryTime'    => $entry_time_status . Carbon::parse($check_time)->format('Y-m-d H:i:s'),
                'status'       => Helper::statusAtt($status_in ?? 2),
                'photo_path'   => env('PROFILE_PHOTO_LARAVEL_URL').$request->photo_path,
            ]);

            if ($st_inorout && $st_inorout->status == "IN") {
                $check_present = Attendance::where('user_id', $attender->id)->whereDate('time_check_in', Carbon::parse($check_time)->format('Y-m-d'));

                // dd($check_present->exists());
                
                if ($check_present->exists()) {
                    $att_out = AttLog::where('user_id', $attender->id)->whereDate('time_check_out', Carbon::parse($check_time)->format('Y-m-d'))->latest()->first();

                    // AttLog
                    $this->attLog(
                        $attender->id,
                        $att_out->time_check_out,
                        $check_time,
                        $st_inorout->status
                    );

                    // return response()->json([
                    //     'status' => true,
                    //     'message' => 'Selamat datang ' . $attender->name,
                    // ], 200);
                } else {
                    $this->attLog(
                        $attender->id,
                        $check_time,
                        $check_time,
                        $st_inorout->status
                    );                   
                    Attendance::create([
                        'user_id'         => $attender->id,
                        'time_check_in'   => $check_time,
                        'status_check_in' => $status_in,
                    ]);
                }
            } else {
                $att_in = Attendance::where('user_id', $attender->id)->whereDate('time_check_in', Carbon::parse($check_time)->format('Y-m-d'))->latest()->first();

                $timeCheckIn = Carbon::parse($att_in->time_check_in);
                $diffInSeconds = $timeCheckIn->diffInSeconds($check_time);
                $hours = floor($diffInSeconds / 3600);
                $minutes = floor(($diffInSeconds % 3600) / 60);
                $seconds = $diffInSeconds % 60;
                $diff = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                if (!$attender->shift) {
                    $att_in->update([
                        'time_check_out'  => $check_time,
                        'status_check_out' => $status_out,
                        'time_total' => $diff
                    ]);
                }

                if ($attender->shift && $attender->shift->early_check_out <= Carbon::parse($check_time)->format('H:i:s')) {
                    $att_in->update([
                        'time_check_out'  => $check_time,
                        'status_check_out' => $status_out,
                        'time_total' => $diff
                    ]);
                }

                $this->attLog(
                    $attender->id,
                    $att_in->time_check_out,
                    $check_time,
                    $st_inorout->status
                );
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        $sti = Setting::find(1);

        if ($sti->status == "ON") {
            if ($attender->type_employee_id == 1 || $attender->type_employee_id == 2) {
                // masuk ke queue job
                // $this->post_batik($request, $st_inorout->status == "IN" ? 1 : 2);
                JobPostBatik::dispatch([$request, $st_inorout->status == "IN" ? 1 : 2]);
            }
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Attendance recorded successfully',
        ], 200);
    }

    public function post_att_v2(Request $request)
    {
        // event_time, pin, dev_sn, name, dept_code, photo_path
        $valid = Helper::validator($request->all(), [
            'event_time' => 'required',
            'pin' => 'required',
            'dev_sn' => 'required'
        ]);

        if ($valid === true) {
            $check_time = str_replace("T", " ", $request->event_time);
            $attender = User::with('shift', 'shift2')->where('pin', $request->pin)->first();
            $st_inorout = MachineSetting::where('sn_machine', $request->dev_sn)->first();

            $status = function ($time, $shift, $early, $normal) {
                return $time < $shift->$early ? 1 : ($time < $shift->$normal ? 2 : 3);
            };

            if (!$attender) {
                $attender = $this->userCreate_v1($request);
            }

            if ($attender->shift_id && $attender->shift_id2) {
                // check shift yang di pakai
                $date_event = Carbon::parse($request->event_time);
                $date = Carbon::parse($date_event->format('Y-m-d') . ' ' . $attender->shift->check_in);
                $date2 = Carbon::parse($date_event->format('Y-m-d') . ' ' . $attender->shift2->check_in);
                $diffShift = abs($date_event->diffInSeconds($date));
                $diffShift2 = abs($date_event->diffInSeconds($date2));

                if ($diffShift <=  $diffShift2) {
                    // use shift1
                    $shift = $attender->shift_id ? $attender->shift : null;
                } else {
                    // use shift2
                    $shift = $attender->shift_id2 ? $attender->shift2 : null;
                }
            } else {
                $shift = $attender->shift_id ? $attender->shift : null;
            }
            $time = Carbon::parse($check_time)->format('H:i:s');
            $status_in = $shift ? $status($time, $shift, 'early_check_in', 'check_in') : 2;
            $status_out = $shift ? $status($time, $shift, 'early_check_out', 'check_out') : 2;

            try {
                $greeting = $st_inorout && $st_inorout->status == "IN" ? "Selamat Datang" : "Sampai Jumpa";
                $entry_time_status = $st_inorout && $st_inorout->status == "IN" ? "Masuk " : "Keluar ";
                $access_status = $st_inorout && $st_inorout->status == "IN" ? "Check In" : "Check Out";

                if ($st_inorout && $st_inorout->status == "IN") {
                    $check_present = Attendance::where('user_id', $attender->id)->where('status', 1);
                    if ($check_present->exists()) {
                        $att_out = AttLog::where('user_id', $attender->id)->whereNull('time_check_in')->whereDate('time_check_out', '>=' , Carbon::parse($check_time)->subDay()->format('Y-m-d'))->latest()->first();
                        if ($att_out) {

                            // AttLog
                            $this->attLog_v2(
                                $attender->id,
                                $att_out->time_check_out,
                                $check_time,
                                $st_inorout->status,
                                $shift
                            );
                        }
                    } else {
                        if ($shift && $shift->is_overnight) {
                            Attendance::create([
                                'user_id'         => $attender->id,
                                'time_check_in'   => $check_time,
                                'status_check_in' => $status_in,
                                'status'          => 1,
                                'shift_id'        => ($shift) ? $shift->id : null,
                            ]);
                        } else {
                            $check_present = Attendance::where('user_id', $attender->id)->whereDate('time_check_in', Carbon::parse($check_time)->format('Y-m-d'))->where('status', 0);
                            if (!$check_present->exists()) {
                                Attendance::create([
                                    'user_id'         => $attender->id,
                                    'time_check_in'   => $check_time,
                                    'status_check_in' => $status_in,
                                    'status'          => 1,
                                    'shift_id'        => ($shift) ? $shift->id : null,
                                ]);
                            }
                        }
                        $this->attLog_v2(
                            $attender->id,
                            $check_time,
                            $check_time,
                            $st_inorout->status,
                            $shift
                        );
                    }
                } else {
                    $att_in = Attendance::where('user_id', $attender->id)->where('status', 1)->latest()->first();
                    if ($att_in) {
                        $shiftDuration = 0;
                        if ($att_in->shift) {
                            $shiftCheckIn = Carbon::parse($att_in->shift->check_in);
                            $shiftCheckOut = Carbon::parse($att_in->shift->check_out);
                            $shiftDuration = abs($shiftCheckOut->diffInSeconds($shiftCheckIn));
                            if ($att_in->shift->is_overnight) {
                                $timeCheckIn = Carbon::parse(Carbon::parse($att_in->time_check_in)->format('Y-m-d') . ' ' . $att_in->shift->check_in);
                            } else {
                                $timeCheckIn = Carbon::parse(Carbon::parse($check_time)->format('Y-m-d') . ' ' . $att_in->shift->check_in);
                            }
                        } else {
                            $timeCheckIn = Carbon::parse($att_in->time_check_in);
                        }

                        $diffInSeconds = abs($timeCheckIn->diffInSeconds($check_time));
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $seconds = $diffInSeconds % 60;
                        $diff = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                        $dataPayload = [
                            'time_check_out'  => $check_time,
                            'status_check_out' => $status_out,
                            'time_total' => $diff,
                        ];
                        if ($diffInSeconds >= $shiftDuration) {
                            $dataPayload['status'] = 0;
                        }
                        $att_in->update($dataPayload);

                        $this->attLog_v2(
                            $attender->id,
                            $att_in->time_check_out,
                            $check_time,
                            $st_inorout->status,
                            ($att_in->shift) ? $att_in->shift : null
                        );
                    }
                }

                if ($request->is_reposting) {
                    // do nothing
                } else {
                    $this->publishToAbly('gate', [
                        'name'         => $attender->name,
                        'greeting'     => $greeting,
                        'accessStatus' => $access_status,
                        'role'         => $attender->getRoleNames(),
                        'entryTime'    => $entry_time_status . Carbon::parse($check_time)->format('Y-m-d H:i:s'),
                        'status'       => Helper::statusAtt($status_in ?? 2),
                        'photo_path'   => env('PROFILE_PHOTO_LARAVEL_URL').$request->photo_path,
                    ]);
                }
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            $sti = Setting::find(1);

            if ($sti->status == "ON") {
                if ($attender->type_employee_id == 1 || $attender->type_employee_id == 2) {
                    // masuk ke queue job
                    // $this->post_batik($request, $st_inorout->status == "IN" ? 1 : 2);
                    JobPostBatik::dispatch([$request, $st_inorout->status == "IN" ? 1 : 2]);
                }
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Attendance recorded successfully',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Attendance recorded",
        ], 200);
    }

    private function userCreate($request)
    {
        try {
            $attender = User::create([
                'name'             => $request->name,
                'email'            => strtolower(str_replace(" ", "", $request->name)) . '@gmail.com',
                'pin'              => $request->pin,
                'nip'              => $request->pin,
                'type_employee_id' => $request->dept_code ?? 0,
            ]);

            return $attender;
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function userCreate_v1($request)
    {
        try {
            $attender = User::create([
                'name'             => $request->name,
                'email'            => Str::slug($request->name) . $request->pin . '@gmail.com',
                'pin'              => $request->pin,
                'nip'              => $request->pin,
                'type_employee_id' => 1,    // default 1 to Gamatara
                'password'         => Hash::make('1235678'),    
                'department'       => ($request->dept_name) ? $request->dept_name : null
            ]);
            return $attender;
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function attLog($attenderId, $time_start, $check_time, $status)
    {
        $timeStartCheck = Carbon::parse($time_start);
        $diffInSeconds = $timeStartCheck->diffInSeconds(Carbon::parse($check_time));
        $hours = floor($diffInSeconds / 3600);
        $minutes = floor(($diffInSeconds % 3600) / 60);
        $seconds = $diffInSeconds % 60;
        $diff = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        if ($status == "IN") {
            $attLog = AttLog::where('user_id', $attenderId)->whereDate('time_check_out', $check_time)->latest()->first();

            if ($attLog) {
                $attLog->update([
                    'time_check_in'  => $check_time,
                    'total_time' => $diff
                ]);
            } else {
                AttLog::create([
                    'user_id'         => $attenderId,
                    'time_check_in'  => $check_time
                ]);
            }
        } else {
            AttLog::create([
                'user_id'         => $attenderId,
                'time_check_out'  => $check_time
            ]);
        }
    }

    private function attLog_v2($attenderId, $time_start, $check_time, $status, $shift = null)
    {
        $timeStartCheck = Carbon::parse($time_start);
        $diffInSeconds = $timeStartCheck->diffInSeconds(Carbon::parse($check_time));
        $hours = floor($diffInSeconds / 3600);
        $minutes = floor(($diffInSeconds % 3600) / 60);
        $seconds = $diffInSeconds % 60;
        $diff = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        if ($status == "IN") {
            if ($shift && $shift->is_overnight) {
                $attLog = AttLog::where('user_id', $attenderId)->whereNull('time_check_in')->whereDate('time_check_out', Carbon::parse($check_time)->subDay()->format('Y-m-d'))->latest()->first();
            } else {
                $attLog = AttLog::where('user_id', $attenderId)->whereDate('time_check_out', $check_time)->latest()->first();
            }
            
            if ($attLog) {
                $attLog->update([
                    'time_check_in'  => $check_time,
                    'total_time' => $diff
                ]);
            } else {
                AttLog::create([
                    'user_id'         => $attenderId,
                    'time_check_in'  => $check_time
                ]);
            }
        } else {
            AttLog::create([
                'user_id'         => $attenderId,
                'time_check_out'  => $check_time
            ]);
        }
    }

    private function publishToAbly($channelName, $data)
    {
        $ably = new AblyRest(env('ABLY_KEY'));
        $channel = $ably->channels->get($channelName);
        $channel->publish('message', $data);
    }

    private function post_batik($data, $status)
    {
        try {
            AttLogBatik::create([
                'sn' => $data->dev_sn,
                'scan_date' => Carbon::parse(str_replace("T", " ", $data->event_time))->format('Y-m-d H:i:s'),
                'pin' => $data->pin,
                'verifymode' => 0,
                'inoutmode' => $status,
                'reserved' => 0,
                'work_code' => 0,
                'att_id' => 0,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function sync(Request $request)
    {
        // event_time, pin, dev_sn, name, dept_code, photo_path
        $valid = Helper::validator($request->all(), [
            'date' => 'required',
            'user_id' => 'required',
        ]);

        if ($valid === true) {
            try {
                $user = User::find($request->user_id);
                if ($user && $user->pin) {
                    $date = $request->date ?? Carbon::now()->format('Y-m-d'); // Default to current date if not provided
                    $pin = $user->pin ?? null; // Get the pin option

                    $currentDate = Carbon::now()->format('Y-m-d');

                    AttLog::whereDate('time_check_in', '>=', $date)
                        ->whereHas('user', function($query) use ($pin) {
                            $query->when($pin, function ($q) use ($pin) {
                                $q->where('pin', $pin);
                            });
                        })
                        ->delete();
                    
                    AttLog::whereDate('time_check_out', '>=', $date)
                        ->whereHas('user', function($query) use ($pin) {
                            $query->when($pin, function ($q) use ($pin) {
                                $q->where('pin', $pin);
                            });
                        })
                        ->delete();

                    Attendance::whereDate('time_check_in', '>=', $date)
                        ->whereHas('user', function($query) use ($pin) {
                            $query->when($pin, function ($q) use ($pin) {
                                $q->where('pin', $pin);
                            });
                        })
                        ->delete();

                    // Attendance::whereDate('time_check_out', '>=', $date)
                    //     ->whereHas('user', function($query) use ($pin) {
                    //         $query->when($pin, function ($q) use ($pin) {
                    //             $q->where('pin', $pin);
                    //         });
                    //     })
                    //     ->delete();

                    while (Carbon::parse($date)->lte(Carbon::parse($currentDate))) {
                        $accTrans = AccTransaction::whereDate('event_time', $date)
                            ->whereHas('pers_person', function($query) use ($pin) {
                                $query->when($pin, function ($q) use ($pin) {
                                    $q->where('pin', $pin);
                                });
                            })
                            ->orderBy('event_time', 'asc')
                            ->get();
                        if ($accTrans && $accTrans->count()) {
                            foreach ($accTrans as $row) {
                                $payload = [
                                    'id' => $row->id,
                                    'pin' => $row->pin,
                                    'event_time' => $row->event_time,
                                    'dev_sn' => $row->dev_sn,
                                    'dept_code' => $row->dept_code,
                                    'dept_name' => $row->dept_name,
                                    'is_reposting' => true,
                                ];
                                if ($row->pers_person) {
                                    $payload['name'] = $row->pers_person->name;
                                    $payload['photo_path'] = $row->pers_person->photo_path;
                                }
                                JobPostAtt::dispatch($payload);
                            }

                            if ($date == $currentDate) {
                                // do nothing
                            } else {
                                $payload = [
                                    'date' => $date,
                                    'pin' => $pin,
                                ];
                                JobResetAttendance::dispatch($payload);
                            }
                        }
                        $date = Carbon::parse($date)->addDay()->format('Y-m-d');
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Success Sync Attendance',
                ],200);

            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ],422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Sync Attendance",
        ], 422);
    }
}
