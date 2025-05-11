<?php
 
namespace DreamTeam\SyncLink\Providers;
 
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\File;
use DreamTeam\SyncLink\Services\Interfaces\SyncLinkServiceInterface;
use DreamTeam\SyncLink\Services\SyncLinkService;
use DreamTeam\SyncLink\Repositories\Eloquent\SyncLinkRepository;

class SyncLinkServiceProvider extends ServiceProvider
{
    /**
     * Register config file here
     * alias => path
     */
    private $configFile = [
        
    ];

    /**
     * Register commands file here
     * alias => path
     */
    protected $commands = [
        
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

        $this->app->bind(SyncLinkServiceInterface::class, function($app) {
            return new SyncLinkService(
                new SyncLinkRepository()
            );
        });
    }

	public function boot()
	{
		$this->registerModule();

        $this->publish();

        $this->app['events']->listen(RouteMatched::class, function () {
            admin_menu()
                 ->registerItem([
                    'id'             => 'packge_utilities',
                    'priority'       => 9,
                    'parent_id'      => '',
                    'type'           => 'multiple',
                    'route'          => '',
                    'name'           => 'Core::admin.admin_menu.utilities',
                    'icon'           => 'bx bx-sun',
                ])
                ->registerItem([
                    'id'          => 'packge_utilities_links',
                    'priority'    => 2,
                    'parent_id'   => 'packge_utilities',
                    'name'        => 'SyncLink::admin.link',
                    'route'       => 'admin.sync_links.index',
                    'role'        => 'sync_links_index',
                    'permissions' => ['sync_links_index'],
                    'active'      => ['admin.sync_links.edit']
                ]);
        });

        $this->app->booted(function() {
            if (defined('ROLLBACK_DATA_FROM_LOG')) {
                add_filter(ROLLBACK_DATA_FROM_LOG, function($response, string $type, array $dataOld) {
                    if($type == 'sync_links') {
                        $this->app->make(SyncLinkServiceInterface::class)->insert($dataOld);
                        return ['success' => true];
                    }
                    return $response;
                }, 130, 3);
            }
        });
	}

	private function registerModule() {
		$modulePath = __DIR__.'/../../';
        $moduleName = 'SyncLink';

        // boot route
        if (File::exists($modulePath."routes/routes.php")) {
            $this->loadRoutesFrom($modulePath."/routes/routes.php");
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
                __DIR__.'/../../resources/assets' => public_path('vendor/core/core/sync-link'),
            ];
            $config = [];
            $lang = [
                __DIR__ . '/../../resources/lang' => lang_path('vendor/SyncLink'),
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

    /*
    * Đăng ký config cho từng Module
    * $this->configFile
    */
    public function mergeConfig() {
        foreach ($this->configFile as $alias => $path) {
            $this->mergeConfigFrom(__DIR__ . "/../../config/" . $path, $alias);
        }
    }
}