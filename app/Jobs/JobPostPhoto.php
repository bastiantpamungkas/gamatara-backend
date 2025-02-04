<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class JobPostPhoto implements ShouldQueue
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
        $data = $this->data;
        $photoUrl = env('PROFILE_PHOTO_BASE_URL') . $data['photo_path'];

        if ($data['photo_path']) {
            try {
                // Fetch the file from the URL
                $response = Http::get($photoUrl);
                if ($response->successful()) {
                    $fileContents = $response->body();
                    $fileName = basename($photoUrl);
                    $pathInfo = pathinfo($photoUrl);
                    $dirName = $pathInfo['dirname'];
                    $relativeDirPath = parse_url($dirName, PHP_URL_PATH);
                    $filePath = $relativeDirPath . '/' . $fileName;
    
                    // Ensure the directory exists
                    Storage::disk('public')->makeDirectory($relativeDirPath);
    
                    // Save the resized and compressed image to the directory
                    Storage::disk('public')->put($filePath, $fileContents);
    
                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $image = $manager->read(public_path('storage' . $filePath));
                    // Image Crop
                    $image->scale(width:450);
                    $image->save(public_path('storage' . $filePath));
                }
            } catch (\Throwable $th) {
                Log::error($photoUrl);
                Log::error($th->getMessage());
            }
        }  
    }
}
