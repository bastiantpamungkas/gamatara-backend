<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function get_notif()
    {
        $attendances = Cache::remember('missing_checkouts', 60, function () {
            return Attendance::with('user.shift')->whereNull('time_check_out')->get();
        });

        $now = Carbon::now();
        $notif = [];
        foreach ($attendances as $attendance) {
            if($attendance->user && $attendance->user->shift){
                $shiftCheckOut = Carbon::parse($attendance->user->shift->early_check_out);
                $shiftLateCheckOut = Carbon::parse($attendance->user->shift->late_check_out);
        
                if ($now->greaterThanOrEqualTo($shiftCheckOut) && $now->lessThan($shiftLateCheckOut)) {
                    $notif[] = [
                        'user_id' => $attendance->user_id,
                        'title' => "Pengingat Absen",
                        'message' => "Karyawan {$attendance->user->name}, Sudah Saatnya Untuk Absen Keluar",
                    ];
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $notif
        ],200);
    }
}
