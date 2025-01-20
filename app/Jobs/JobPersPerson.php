<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\PersPerson;
use App\Models\User;
use App\Jobs\JobPostPhoto;
use App\Jobs\JobPersLeavePerson;

class JobPersPerson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $person = PersPerson::select('pin', 'name', 'photo_path')->get();
        if ($person) {
            foreach ($person as $row) {
                $user = User::where('nip', $row->pin)->first();
                if ($user) {
                    $user->name = $row->name;
                    $user->pin = $row->pin;
                    $user->nip = $row->pin;
                    $user->email = Str::slug($row->name) . $row->pin . '@gmail.com';
                    $user->password = Hash::make('1235678');
                    // $user->type_employee_id = 1;
                    $user->save();
                } else {
                    User::create(
                        [
                            'name' => $row->name,
                            'pin' => $row->pin,
                            'nip' => $row->pin,
                            'email' => Str::slug($row->name) . $row->pin . '@gmail.com',
                            'password' => Hash::make('1235678'),
                            'type_employee_id' => 1,
                        ]
                    );
                }
                JobPostPhoto::dispatch(['photo_path' => $row->photo_path]);
            };
        }
        JobPersLeavePerson::dispatch();
    }
}
