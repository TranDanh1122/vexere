<?php

namespace DreamTeam\Page\Providers;

use Illuminate\Support\ServiceProvider;
use File;
use Illuminate\View\Factory;
use Illuminate\Routing\Events\RouteMatched;
use DreamTeam\Page\Repositories\Interfaces\PageRepositoryInterface;
use DreamTeam\Page\Repositories\Eloquent\PageRepository;
use DreamTeam\Page\Services\Interfaces\PageServiceInterface;
use DreamTeam\Page\Services\PageService;
use Illuminate\Http\Request;
use DreamTeam\Page\Models\Page;

class PageServiceProvider extends ServiceProvider
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
     * Register bindings in the container.
     */
    public function register()
    {
        // Đăng ký config cho từng Module
        $this->mergeConfig();
        // boot commands
        $this->commands($this->commands);

        $this->app->bind(PageRepositoryInterface::class, function($app){
            return new PageRepository();
        });

        $this->app->bind(PageServiceInterface::class, function($app){
            return new PageService(
                new PageRepository()
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
                    'id'             => 'package_page',
                    'priority'       => 3,
                    'parent_id'      => '',
                    'type'           => 'multiple',
                    'route'          => '',
                    'name'           => 'Page::page.name',
                    'icon'           => 'bx bx-file',
                ])
                ->registerItem([
                    'id'          => 'package_page_index',
                    'priority'    => 0,
                    'parent_id'   => 'package_page',
                    'name'        => 'Page::page.list_page',
                    'route'       => 'admin.pages.index',
                    'role'        => 'pages_index',
                    'permissions' => ['pages_index'],
                    'active'      => ['admin.pages.edit' ]
                ])
                ->registerItem([
                    'id'          => 'package_page_create',
                    'priority'    => 1,
                    'parent_id'   => 'package_page',
                    'name'        => 'Page::page.create_page',
                    'route'       => 'admin.pages.create',
                    'role'        => 'pages_create',
                    'permissions' => ['pages_create']
                ]);
                // ->registerItem([
                //     'id'             => 'package_page',
                //     'priority'       => 3,
                //     'parent_id'      => '',
                //     'type'           => 'single',
                //     'name'           => 'Page::page.name',
                //     'icon'           => 'bx bx-file',
                //     'route'          => 'admin.pages.index',
                //     'role'           => 'pages_index',
                //     'permision'      => ['pages_index'],
                //     'active'         => ['admin.pages.create', 'admin.pages.edit']
                // ]);
        });

        $this->app->booted(function(){
            menu_store()
                ->registerMenu([
                    'id'             => 'pages',
                    'priority'       => 1,
                    'name'           => 'Page::page.name',
                    'models'         => 'DreamTeam\Page\Models\Page',
                    'has_locale'     => true,
                    'select'         => 'id, name, slug',
                ]);

            if(defined('FILTER_LIST_DATA_TABLE_TOP_VIEW')) {
                add_filter(FILTER_LIST_DATA_TABLE_TOP_VIEW, function(string|\DreamTeam\Shortcode\View\View $response, string $tableName, Request $request) {
                    if($tableName == 'pages') {
                        $datas = $this->app->make(PageRepositoryInterface::class)->countRecordWithStatus();
                        return \View('Table::components.count-status', compact('datas', 'tableName'))->render();
                    }
                    return $response;
                }, 133, 3);
            }

            if (defined('ROLLBACK_DATA_FROM_LOG')) {
                add_filter(ROLLBACK_DATA_FROM_LOG, function($response, string $type, array $dataOld) {
                    if($type == 'pages') {
                        $this->app->make(PageServiceInterface::class)->insert($dataOld);
                        return ['success' => true];
                    }
                    return $response;
                }, 130, 3);
            }
        });
	}

	private function registerModule() {
		$modulePath = __DIR__.'/../../';
        $moduleName = 'Page';

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
            // foreach to require_once file
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
                //
            ];
            $config = [
                //
            ];
            $all = array_merge($assets, $config);
            // Chạy riêng
            $this->publishes($all, 'dreamteam/page');
            $this->publishes($assets, 'dreamteam/page/assets');
            $this->publishes($config, 'dreamteam/page/config');
            // Khởi chạy chung theo core
            $this->publishes($all, 'dreamteam/core');
            $this->publishes($assets, 'dreamteam/core/assets');
            $this->publishes($config, 'dreamteam/core/config');
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
