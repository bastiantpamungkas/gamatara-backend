<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\JobResetAttendance;

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
        JobResetAttendance::dispatch([]);
        $this->info("reset attendance finished");
    }
}
