<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBatik;
use App\Models\UserBatik;
use Carbon\Carbon;

class SyncBatikCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncBatik';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync schedule from attendance to Batik';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch all users from the User model
        $users = User::select('id', 'name', 'email', 'pin', 'shift_id', 'type_employee_id', 'company_id', 'status', 'shift_id2', 'department')->get();

        // Prepare data for upsert
        if ($users && $users->count()) {
            $userBatikData = $users->toArray();
            // Perform upsert operation
            UserBatik::upsert($userBatikData, ['id'], ['name', 'email', 'pin', 'shift_id', 'type_employee_id', 'company_id', 'status', 'shift_id2', 'department']);
        }

        // Get the date a month ago
        $startDate = Carbon::now()->subMonth(2)->format('Y-m-d');

        // Select attendance records where the date is aMonthAgo
        $attendance = Attendance::with('user')->whereDate('time_check_in', '>=', $startDate)->get();

        if ($attendance && $attendance->count()) {
            AttendanceBatik::whereDate('time_check_in', '>=', $startDate)->delete();
            $attendanceBatik = $attendance->map(function ($item) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'name' => ($item->user) ? $item->user->name : null,
                    'pin' => ($item->user) ? $item->user->pin : null,
                    'time_check_in' => $item->time_check_in,
                    'time_check_out' => $item->time_check_out,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'status' => $item->status,
                    'shift_id' => $item->shift_id,
                ];
            })->toArray();

            // Batch the upsert operations
            $batchSize = 1000; // Adjust the batch size as needed
            foreach (array_chunk($attendanceBatik, $batchSize) as $batch) {
                AttendanceBatik::upsert($batch, ['id'], ['user_id', 'name', 'pin', 'time_check_in', 'time_check_out', 'created_at', 'updated_at', 'status', 'shift_id']);
            }
            
        }
        $this->info("Sync schedule from attendance to Batik at " . $startDate);
    }
}
