<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AttLogBatik;
use Carbon\Carbon;

class JobPostBatik implements ShouldQueue
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
        $data = $this->data[0];
        $status = $this->data[1];

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
    }
}
