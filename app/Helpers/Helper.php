<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use App\Models\Attendance;
use App\Models\AttLog;
use App\Models\AccTransaction;
use App\Jobs\JobPostAtt;
use App\Jobs\JobResetAttendance;
use Carbon\Carbon;

class Helper
{

    public static function validator($data, $rules)
    {
        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 422);    
        } else {
            return true;
        }
    }

    public static function pagination($model, $request, $params)
    {
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));

        // Mulai query dengan pencarian
        $data = $model->when($keyword, function ($query) use ($keyword, $params) {
            foreach ($params as $param) {
                // Cek jika parameter mengandung "." untuk relasi
                if (strpos($param, '.') !== false) {
                    [$relation, $column] = explode('.', $param, 2);

                    // Gunakan orWhereHas untuk relasi
                    // $query->orWhereHas($relation, function ($q) use ($column, $keyword) {
                    //     $q->whereRaw("LOWER(CAST($column AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
                    // });
                } else {
                    // Cek jika keyword adalah tahun
                    // if (preg_match('/^\d{4}$/', $keyword)) {
                    //     // Gunakan whereYear untuk kolom tanggal
                    //     $query->orWhereYear($param, $keyword);
                    // } else {
                    //     // Gunakan LIKE untuk kolom teks
                    //     $query->orWhereRaw("LOWER(CAST($param AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
                    // }
                }
            }
        });

        return $data->paginate($pageSize, ['*'], 'page', $page);
    }

    public static function statusAtt($status){
        if($status == 1){
            return 'Masuk Lebih Awal';
        }else if($status == 2){
            return 'Masuk Tepat Waktu';
        }else{
            return 'Terlambat Masuk';
        }
    }

    public static function syncAttendance($start_date = null, $pin = null){
        $date = $start_date ?? Carbon::now()->format('Y-m-d'); // Default to current date if not provided
        
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
                ->when($pin, function ($query) use ($pin) {
                    $query->where('pin', $pin);
                })
                ->orderBy('event_time', 'asc')
                ->get();
            if ($accTrans && $accTrans->count()) {
                foreach ($accTrans as $row) {
                    if ($row->pin && $row->name) {
                        $payload = [
                            'id' => $row->id,
                            'pin' => $row->pin,
                            'name' => $row->name,
                            'photo_path' => null,
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
}
