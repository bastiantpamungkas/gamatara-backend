<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\AttLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttLogController extends Controller
{
    public function list(Request $request){
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->addDay(): null;
        $type_employee = $request->input('type_employee') ?? null;
        $role = $request->input('role') ?? null;
        $duration = $request->input('duration') ?? null;
        $keyword = $request->input('keyword') ?? null;
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $att_logs = AttLog::with(['user.type', 'user.company', 'user.roles']);
        $att_logs->whereHas('user', function ($q) use ($type_employee, $role, $keyword) {
            $q->where('status', 1)
            ->when($type_employee, function ($q) use ($type_employee) {
                $q->where('type_employee_id', $type_employee);
            })
            ->when($role, function ($q) use ($role) {
                $q->whereHas('roles', function ($q_roles) use ($role) {
                    $q_roles->where('id', $role);
                });
            })
            ->when($keyword, function ($q) use ($keyword) {
                $q->where("name",  "ilike", '%' . $keyword . '%')
                ->orWhere("nip", $keyword);
            });
        })
        ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
            $q->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('time_check_in', [$start_date, $end_date]);
            });
        })
        ->when($duration, function ($q) use ($duration) {
            if ($duration == 'Terlama') {
                $q->orderBy('total_time', 'desc');
            } 
            if ($duration == 'Singkat') {
                $q->orderBy('total_time', 'asc');
            }
        });
    
        $data = Helper::pagination($att_logs->orderBy($sort, $sortDirection), $request, [
            'user.name',
            'user.nip'
        ]);

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function list_log_card(Request $request){
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->addDay(): null;
        $keyword = $request->input('keyword') ?? null;
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('type', 'desc');

        $att_logs = AttLog::with(['user.type', 'user.company', 'user.roles']);
        $att_logs->whereHas('user', function ($q) use ($keyword) {
            $q->where('status', 1)->whereHas('roles', function ($q_roles) {
                $q_roles->where('name', 'Tamu');
            })
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $q->where("name",  "ilike", '%' . $keyword . '%')
                    ->orWhere("nip", $keyword);
                });
            });
        })
        ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
            $q->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('time_check_in', [$start_date, $end_date])
                ->orWhereBetween('time_check_out', [$start_date, $end_date]);
            });
        });
        $data = Helper::pagination($att_logs->orderBy($sort, $sortDirection), $request, [
            'user.name',
            'user.nip'
        ]);

        $log_cards = collect();
        if ($data) {
            foreach($data as $row) {
                $log_card_in = $log_card_out = $row;
                $log_card_in->status = 'IN';
                if ($row->time_check_in) {
                    $log_cards->add([
                        "name" => ($row->user) ? $row->user->name : null,
                        "nip" => ($row->user) ? $row->user->nip : null,
                        "time_check_in" => $row->time_check_in,
                        "time_check_out" => $row->time_check_out,
                        "time" => $row->time_check_in,
                        "status" => 'IN',
                    ]);
                }
                if ($row->time_check_out) {
                    $log_cards->add([
                        "name" => ($row->user) ? $row->user->name : null,
                        "nip" => ($row->user) ? $row->user->nip : null,
                        "time_check_in" => $row->time_check_in,
                        "time_check_out" => $row->time_check_out,
                        "time" => $row->time_check_out,
                        "status" => 'OUT',
                    ]);
                }
            }
        }
        $data->setCollection($log_cards);

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
