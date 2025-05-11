<?php

namespace DreamTeam\Translate\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use DreamTeam\Translate\Repositories\Eloquent\LanguageRepository;
use DreamTeam\Translate\Services\Interfaces\LanguageServiceInterface;
use DreamTeam\Translate\Services\LanguageService;

class TranslateServiceProvider extends ServiceProvider
{
    /**
     * Register config file here
     * alias => path
     */
    private $configFile = [
        //
    ];

    /**
     * Register commands file here
     * alias => path
     */
    protected $commands = [
        //
    ];

    /**
     * Register middleare file here
     * name => middleware
     */
    protected $middleare = [
        //
    ];

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        // Đăng ký config cho từng Module
        $this->mergeConfig();
        // boot commands
        $this->commands($this->commands);

        $this->app->bind(LanguageServiceInterface::class, function ($app) {
            return new LanguageService(
                new LanguageRepository()
            );
        });

        $this->app->register(LanguageServiceProvider::class);
        $this->app->register(CustomTranslationServiceProvider::class);
    }

    public function boot()
    {
        $this->registerModule();

        $this->publish();

        $this->registerMiddleware();
    }

    private function registerModule()
    {
        $modulePath = __DIR__ . '/../../';
        $moduleName = 'Translate';

        // boot route
        if (File::exists($modulePath . "routes/routes.php")) {
            $this->loadRoutesFrom($modulePath . "/routes/routes.php");
        }

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
    }

    /*
    * publish dự án ra ngoài
    * publish config File
    * publish assets File
    */
    public function publish()
    {
        if ($this->app->runningInConsole()) {
            $assets = [
                __DIR__ . '/../../resources/assets' => public_path('vendor/core/core/translates'),
            ];
            $config = [
                //
            ];
            $view = [
                //
            ];
            $lang = [
                __DIR__ . '/../../resources/lang' => lang_path('vendor/Translate'),
            ];
            $all = array_merge($assets, $config, $view, $lang);
            // Chạy riêng
            $this->publishes($all, 'dreamteam/translates');
            $this->publishes($assets, 'dreamteam/translates/assets');
            $this->publishes($config, 'dreamteam/translates/config');
            $this->publishes($view, 'dreamteam/etranslates/view');
            $this->publishes($lang, 'dreamteam/etranslates/lang');
            // Khởi chạy chung theo core
            $this->publishes($all, 'dreamteam/core');
            $this->publishes($assets, 'dreamteam/core/assets');
            $this->publishes($config, 'dreamteam/core/config');
            $this->publishes($view, 'dreamteam/core/view');
            $this->publishes($lang, 'dreamteam/core/lang');
        }
    }

    /*
    * Đăng ký config cho từng Module
    * $this->configFile
    */
    public function mergeConfig()
    {
        foreach ($this->configFile as $alias => $path) {
            $this->mergeConfigFrom(__DIR__ . "/../../config/" . $path, $alias);
        }
    }

    /**
     * Đăng ký Middleare
     */
    public function registerMiddleware()
    {
        foreach ($this->middleare as $key => $value) {
            $this->app['router']->pushMiddlewareToGroup($key, $value);
        }
    }
}
