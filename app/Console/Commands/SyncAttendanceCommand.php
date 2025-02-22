<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Helpers\Helper;
use Exception;

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
        try {
            $date = $this->option('date') ?? Carbon::now()->format('Y-m-d'); // Default to current date if not provided
            $pin = $this->option('pin') ?? null; // Get the pin option

            Helper::syncAttendance($date, $pin);
            $this->info("add sync event record data to JobPostAtt on " . $date);
        } catch (Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
