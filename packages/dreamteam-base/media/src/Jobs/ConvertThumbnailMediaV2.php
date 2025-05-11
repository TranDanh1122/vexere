<?php

namespace DreamTeam\Media\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DreamTeam\JobStatus\Trackable;
use DreamTeam\Media\Facades\RvMedia;
use Illuminate\Support\Str;

class ConvertThumbnailMediaV2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Trackable;

    public $tries = 2;
    public $failOnTimeout = true;

    protected $fileList;
    protected $moduleName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileList, $moduleName)
    {
        $this->fileList = $fileList;
        $this->moduleName = $moduleName;
        $this->prepareStatus([]);
        $this->setInput(['fileList' => $this->fileList, 'moduleName' => $moduleName]); // Optional
    }

    public function handle()
    {
        try{
            $this->setProgressNow(1);
            $this->setProgressMax(100);
            $converted = 0;
            foreach ($this->fileList as $filePath) {
                $filePath = urldecode($filePath);
                Log::info(sprintf('Converting %s', $filePath));
    
                if (Storage::exists($filePath)) {
                    $fileName = File::name($filePath);
                    $fileExtension = File::extension($filePath);

                    foreach(['large', 'medium', 'tiny', 'small'] as $size) {
                        $url = str_replace(
                            $fileName . '.' . $fileExtension,
                            $fileName . '-' . $size . '.' . $fileExtension,
                            $filePath
                        );
                        if (Storage::exists($url)) {
                            $newPath = RvMedia::getThumbnailPath($this->moduleName, $size, $filePath);
                            Storage::move($url, $newPath);
                        }
                        $webpPath = $url. '.webp';
                        if (Storage::exists($webpPath)) {
                            $newWebpPath = $newPath . '.webp';
                            Storage::move($webpPath, $newWebpPath);
                        }
                    }
                    ++$converted;
                } else {
                    Log::error(sprintf('Not found file item in storage %s', $filePath));
                }
            }
            Log::info(sprintf('Converted %d %s', $converted, Str::plural('file', $converted)));
            $this->setProgressNow(100);
            $this->setOutput(['success' => 1, 'message' => sprintf('Converted %d %s', $converted, Str::plural('file', $converted)) ]);
        } catch(Exception $e) {
            $this->fail($e);
            Log::error('Faild Convert media thumbnail V2: ' .  $e->getMessage());
            throw $e;
        }

    }

    public function displayName() {
        return 'convert_media_thumbnailv2';
    }
}
