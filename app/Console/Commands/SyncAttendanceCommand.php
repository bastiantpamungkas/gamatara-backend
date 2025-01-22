<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
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
    protected $signature = 'command:syncAttendance';

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
        $date = '2025-01-22';   // start implementasi shift2
        $pin = '10174';     // contoh pak umar
        $accTrans = AccTransaction::whereDate('event_time', $date)
            ->whereHas('pers_person', function($q) use ($pin) {
                $q->where('pin', $pin);
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
