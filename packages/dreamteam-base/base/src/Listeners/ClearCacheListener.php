<?php

namespace DreamTeam\Base\Listeners;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ClearCacheListener
{
    public function __construct()
    {
    }

    public function handle(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Log::info('Listener Call Clear Cache Success');
        } catch (Exception $exception) {
            Log::error('Listener Call Clear Cache Error '.$exception->getMessage());
            info($exception->getMessage());
        }
    }
}
