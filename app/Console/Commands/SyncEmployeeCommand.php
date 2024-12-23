<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\JobPersPerson;

class SyncEmployeeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncEmployee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync schedule employee from persperson to user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        JobPersPerson::dispatch();
        $this->info("add sync persperson data to jobs");
    }
}
