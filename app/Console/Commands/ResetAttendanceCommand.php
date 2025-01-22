<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\AccTransaction;
use App\Jobs\JobPostAtt;
use Carbon\Carbon;

class ResetAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:resetAttendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync schedule attendance from bio-security to attendance & att_log';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->format('Y-m-d');   // auto
        $date = '2025-01-22';   // reset status attendance
        $pin = '10174';     // contoh pak umar
        //reset attendance status
        $attendance = Attendance::where('status', 1)->whereDate('time_check_in', $date)
        ->whereHas('user', function($q) use ($pin) {
            $q->where('pin', $pin);
        })
        ->get();

        $check_time = Carbon::parse($date . ' 23:59:59');
        if ($attendance) {
            foreach ($attendance as $row) {
                $shiftDuration = 0;
                if ($row->shift) {
                    $shiftCheckIn = Carbon::parse($row->shift->check_in);
                    $shiftCheckOut = Carbon::parse($row->shift->check_out);
                    $shiftDuration = abs($shiftCheckOut->diffInSeconds($shiftCheckIn));
                }

                $timeCheckIn = Carbon::parse($row->time_check_in);
                $diffInSeconds = $timeCheckIn->diffInSeconds($check_time);
                $hours = floor($diffInSeconds / 3600);
                $minutes = floor(($diffInSeconds % 3600) / 60);
                $seconds = $diffInSeconds % 60;
                $diff = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                if ($diffInSeconds >= $shiftDuration) {
                    $row->status = 0;
                    $row->save();
                } else {
                    if ($row->shift && $row->shift->is_overnight) {
                        // do nothing
                    } else {
                        $row->status = 0;
                        $row->save();
                    }
                }
            }
        }
        $this->info("reset attendance finished");
    }
}
