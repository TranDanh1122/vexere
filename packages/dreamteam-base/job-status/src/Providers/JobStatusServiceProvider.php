<?php

namespace DreamTeam\JobStatus\Providers;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use DreamTeam\JobStatus\EventManagers\EventManager;

class JobStatusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $modulePath = __DIR__.'/../../';
        $moduleName = 'JobStatus';
        // boot migration
        if (File::exists($modulePath . "migrations")) {
            $this->loadMigrationsFrom($modulePath . "migrations");
        }

        // boot languages
        if (File::exists($modulePath . "resources/lang")) {
            $this->loadTranslationsFrom($modulePath . "resources/lang", $moduleName);
            $this->loadJSONTranslationsFrom($modulePath . 'resources/lang');
        }

        // boot views
        if (File::exists($modulePath . "resources/views")) {
            $this->loadViewsFrom($modulePath . "resources/views", $moduleName);
        }

        // boot route
        if (File::exists($modulePath."routes/route.php")) {
            $this->loadRoutesFrom($modulePath."/routes/route.php");
        }

	    // boot all helpers
        if (File::exists($modulePath . "helpers")) {
            // get all file in Helpers Folder
            $helper_dir = File::allFiles($modulePath . "helpers");
            // foreach to require file
            foreach ($helper_dir as $key => $value) {
                $file = $value->getPathName();
                require_once $file;
            }
        }
        $this->bootListeners();
    }

    /*
    * publish dự án ra ngoài
    * publish config File
    * publish assets File
    */
    public function publish()
    {
        if ($this->app->runningInConsole()) {
            $assets = [];
            $config = [];
            $lang = [
                __DIR__ . '/../../resources/lang' => lang_path('vendor/JobStatus'),
            ];
            $all = array_merge($assets, $config, $lang);
            // Chạy riêng
            $this->publishes($all, 'dreamteam/sync_links');
            // Khởi chạy chung theo core
            $this->publishes($all, 'dreamteam/core');
            $this->publishes($assets, 'dreamteam/core/assets');
            $this->publishes($lang, 'dreamteam/core/lang');
        }
    }

    private function bootListeners()
    {
        /** @var EventManager $eventManager */
        $eventManager = app(\DreamTeam\JobStatus\EventManagers\DefaultEventManager::class);

        // Add Event listeners
        app(QueueManager::class)->before(function (JobProcessing $event) use ($eventManager) {
            $eventManager->before($event);
        });
        app(QueueManager::class)->after(function (JobProcessed $event) use ($eventManager) {
            $eventManager->after($event);
        });
        app(QueueManager::class)->failing(function (JobFailed $event) use ($eventManager) {
            $eventManager->failing($event);
        });
        app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) use ($eventManager) {
            $eventManager->exceptionOccurred($event);
        });
    }
}
