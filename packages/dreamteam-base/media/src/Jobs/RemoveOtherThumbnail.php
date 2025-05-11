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
use Illuminate\Support\Str;
use DreamTeam\Media\Facades\RvMedia;

class RemoveOtherThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Trackable;

    public $tries = 2;
    public $failOnTimeout = true;

    protected $fileList;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileList)
    {
        $this->fileList = $fileList;
        $this->prepareStatus([]);
        $this->setInput(['fileList' => $this->fileList]); // Optional
    }

    public function handle()
    {
        try{
            $this->setProgressNow(1);
            $this->setProgressMax(100);
            $removed = 0;
            foreach ($this->fileList as $media) {
                $filePath = $media->url;
                if (!RvMedia::canGenerateThumbnails($media->mime_type)) continue;
                Log::info(sprintf('Remove thumbnail of %s', $filePath));
    
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
                            Storage::delete($url);
                        }
                        $webpPath = $url. '.webp';
                        if (Storage::exists($webpPath)) {
                            Storage::delete($webpPath);
                        }
                    }
                    ++$removed;
                } else {
                    Log::error(sprintf('Not found file item in storage %s', $filePath));
                }
            }
            Log::info(sprintf('Removed %d %s', $removed, Str::plural('file', $removed)));
            $this->setProgressNow(100);
            $this->setOutput(['success' => 1, 'message' => sprintf('Removed thumbnail %d %s', $removed, Str::plural('file', $removed)) ]);
        } catch(Exception $e) {
            $this->fail($e);
            Log::error('Faild Remove media thumbnail V2: ' .  $e->getMessage());
            throw $e;
        }

    }

    public function displayName() {
        return 'remove_media_thumbnailv2';
    }
}
