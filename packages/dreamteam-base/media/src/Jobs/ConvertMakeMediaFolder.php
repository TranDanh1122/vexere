<?php

namespace DreamTeam\Media\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DreamTeam\JobStatus\Trackable;
use DreamTeam\Media\Facades\RvMedia;
use Illuminate\Support\Str;

class ConvertMakeMediaFolder implements ShouldQueue
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
            $converted = 0;
            foreach ($this->fileList as $file) {
                $filePath = getImageMedia($file->getPath().$file->name, '', $file->url);
                Log::info(sprintf('Converting %s', $filePath));
    
                if (Storage::exists($filePath)) {
                    if (str_contains($filePath, '/')) {
                        $paths = array_filter(explode('/', $filePath));
                        array_pop($paths);
                        $folderId = 0;
                        if (count($paths)) {
                            $mimeType = Storage::mimeType($filePath);
                            foreach ($paths as $folder) {
                                $folderId = RvMedia::createFolder($folder, $folderId, true);
                            }
                            $file->folder_id = $folderId;
                            $file->mime_type = $mimeType;
                            $file->url = $filePath;
                            $file->save();
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
            Log::error('Faild Convert media: ' .  $e->getMessage());
        }

    }

    public function displayName() {
        return 'convert_media';
    }
}
