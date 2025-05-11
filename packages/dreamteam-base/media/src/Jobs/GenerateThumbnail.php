<?php

namespace DreamTeam\Media\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use Illuminate\Support\Facades\Log;
use DreamTeam\JobStatus\Trackable;
use DreamTeam\Media\Facades\RvMedia;
use Illuminate\Support\Str;

class GenerateThumbnail implements ShouldQueue
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
        $this->setInput(['fileList' => $this->fileList, 'moduleName' => $this->moduleName]); // Optional
    }

    public function handle()
    {
        try{
            $this->setProgressNow(1);
            $this->setProgressMax(100);
            $errors = [];
            $converted = count($this->fileList);
            if ($converted) {
                foreach ($this->fileList as $file) {
                    if (empty($file)) continue;
                    try {
                        RvMedia::reGenerateThumbnails($file, $this->moduleName);
                    } catch (Exception) {
                        $errors[] = $file;
                    }
                }

                $errors = array_unique($errors);

                $errors = array_map(function ($item) {
                    return [$item];
                }, $errors);
            }

            if ($errors) {
                Log::error(trans('media::media.setting.generate_thumbnails_error', ['count' => count($errors)]));
                $this->setOutput(['success' => 0, 'message' => trans('media::media.setting.generate_thumbnails_error', ['count' => count($errors)])]);
                $this->fail(new Exception(trans('media::media.setting.generate_thumbnails_error', ['count' => count($errors)])));
            } else {
                Log::debug(sprintf('Generate thumbnail %d %s', $converted, Str::plural('file', $converted)));
                $this->setProgressNow(100);
                $this->setOutput(['success' => 1, 'message' => sprintf('Generate thumbnail %d %s', $converted, Str::plural('file', $converted)) ]);
            }
        } catch(Exception $e) {
            $this->fail($e);
            Log::error('Faild Generate thumbnail media: ' .  $e->getMessage());
            throw $e;
        }

    }

    public function displayName() {
        return 'generate_thumbnail';
    }
}
