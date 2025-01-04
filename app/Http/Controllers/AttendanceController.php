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
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Types\Relations\Car;
use App\Jobs\JobPostBatik;

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

        $att = Helper::pagination($att->orderBy('created_at', 'desc'), $request, [
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

        $user = User::select('id', 'name', 'nip', 'status')
            ->withCount([
                'attendance' => function ($q) use ($month, $start_date, $end_date) {
                    if ($month) {
                        $yearMonth = Carbon::parse($month);
                        $q->whereYear('time_check_in', $yearMonth->year)
                            ->whereMonth('time_check_in', $yearMonth->month);
                    }
                    if ($start_date && $end_date) {
                        $q->whereBetween('time_check_in', [$start_date, $end_date]);
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
                        $q->whereBetween('time_check_in', [$start_date, $end_date]);
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
                        $q->whereBetween('time_check_in', [$start_date, $end_date]);
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
                        $q->whereBetween('time_check_in', [$start_date, $end_date]);
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
                        $query->whereBetween('time_check_in', [$start_date, $end_date]);
                    }
                });
            })
            ->paginate($pageSize, ['*'], 'page', $page);




        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function post_att(Request $request)
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
                // 'photo_path'   => env('PROFILE_PHOTO_BASE_URL').$request->photo_path,
                'photo_path'   => $request->photo_path,
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
}
