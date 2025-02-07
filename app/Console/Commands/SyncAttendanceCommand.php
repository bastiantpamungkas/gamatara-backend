<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\AttLog;
use App\Models\AccTransaction;
use App\Jobs\JobPostAtt;
use Carbon\Carbon;

class SyncAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncAttendance {--date=} {--pin=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync schedule attendance from bio-security to attendance & att_log usage command:syncAttendance [--date=] [--pin=]';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ?? Carbon::now()->format('Y-m-d'); // Default to current date if not provided
        $pin = $this->option('pin'); // Get the pin option

        // $date = '2025-02-02';   // start implementasi shift2
        // $pin = '10272';     // contoh pak umar

        AttLog::whereDate('time_check_in', $date)
            ->whereHas('user', function($query) use ($pin) {
                $query->when($pin, function ($q) use ($pin) {
                    $q->where('pin', $pin);
                });
            })
            ->delete();
        
        AttLog::whereDate('time_check_out', $date)
            ->whereHas('user', function($query) use ($pin) {
                $query->when($pin, function ($q) use ($pin) {
                    $q->where('pin', $pin);
                });
            })
            ->delete();

        Attendance::whereDate('time_check_in', $date)
            ->whereHas('user', function($query) use ($pin) {
                $query->when($pin, function ($q) use ($pin) {
                    $q->where('pin', $pin);
                });
            })
            ->delete();

        Attendance::whereDate('time_check_out', $date)
            ->whereHas('user', function($query) use ($pin) {
                $query->when($pin, function ($q) use ($pin) {
                    $q->where('pin', $pin);
                });
            })
            ->delete();

        $accTrans = AccTransaction::whereDate('event_time', $date)
            ->whereHas('pers_person', function($query) use ($pin) {
                $query->when($pin, function ($q) use ($pin) {
                    $q->where('pin', $pin);
                });
            })
            ->orderBy('event_time', 'asc')
            ->get();
        if ($accTrans) {
            foreach ($accTrans as $row) {
                $payload = [
                    'id' => $row->id,
                    'pin' => $row->pin,
                    'event_time' => $row->event_time,
                    'dev_sn' => $row->dev_sn,
                    'dept_code' => $row->dept_code,
                    'dept_name' => $row->dept_name,
                ];
                if ($row->pers_person) {
                    $payload['name'] = $row->pers_person->name;
                    $payload['photo_path'] = $row->pers_person->photo_path;
                }
                JobPostAtt::dispatch($payload);
            }
        }
        $this->info("add sync event record data to JobPostAtt");
    }
}
