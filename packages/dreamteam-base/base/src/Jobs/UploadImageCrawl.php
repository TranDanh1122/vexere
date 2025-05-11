<?php

namespace DreamTeam\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use Illuminate\Support\Facades\Log;
use DreamTeam\JobStatus\Trackable;

class UploadImageCrawl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Trackable;

    public $tries = 2;
    // public $timeout = 10;
    public $failOnTimeout = true;

    protected $imageItem;
    protected $replaceLinks;
    protected $hasResize;
    protected $saveDB;
    protected $typeJob;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($imageItem = null, $replaceLinks = [], $hasResize = true, $saveDB = false, $typeJob = null)
    {
        $this->imageItem = $imageItem;
        $this->replaceLinks = $replaceLinks;
        $this->hasResize = $hasResize;
        $this->saveDB = $saveDB;
        $this->typeJob = $typeJob;
        $this->prepareStatus([]);
        $this->setInput(['url' => $this->imageItem]); // Optional
    }

    public function handle()
    {
        Log::info('Starting updload image crawl for url ' . $this->imageItem);
        try {
            $this->setProgressNow(1);
            $this->setProgressMax(100);
            uploadImageCrawlFromLink($this->imageItem, $this->replaceLinks, $this->hasResize, $this->saveDB);
            Log::info('Done upload image crawl for ' . $this->imageItem);
            $this->setProgressNow(100);
            $this->setOutput(['success' => 1, 'message' => __('JobStatus::progress.import_image_success', ['link' => $this->imageItem])]);
        } catch (Exception $e) {
            $this->fail($e);
            Log::error('Fail upload image crawl for ' . $this->imageItem . ' ' . $e->getMessage());
        }
    }

    public function displayName()
    {
        if (!$this->typeJob) $this->typeJob = \DreamTeam\JobStatus\Models\JobStatus::IMPORT_IMAGE_CRAWL;

        return $this->typeJob;
    }
}
