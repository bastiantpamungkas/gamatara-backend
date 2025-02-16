<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use Carbon\Carbon;

class JobResetAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $data = $this->data;
            $date = isset($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');   // auto
            $pin = isset($data['pin']) ? $data['pin'] :  null;

            $attendance = Attendance::where('status', 1)->whereDate('time_check_in', $date)
            ->whereHas('user', function($query) use ($pin) {
                $query->when($pin, function ($q) use ($pin) {
                    $q->where('pin', $pin);
                });
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
                        $timeCheckIn = Carbon::parse(Carbon::parse($check_time)->format('Y-m-d') . ' ' . $row->shift->check_in);
                    } else {
                        $timeCheckIn = Carbon::parse($row->time_check_in);
                    }                
                    $diffInSeconds = abs($timeCheckIn->diffInSeconds($check_time));
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
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
