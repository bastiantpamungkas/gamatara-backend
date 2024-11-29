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

        $att_logs = AttLog::with(['user.type', 'user.company']);

        if ($start_date && $end_date) {
            $att_logs->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('time_check_out', [$start_date, $end_date])
                      ->orWhereBetween('time_check_in', [$start_date, $end_date]);
            });
        }
        
        $data = Helper::pagination($att_logs->orderBy('created_at', 'desc'), $request, [
            'user.name',
            'user.nip'
        ]);

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }
}
