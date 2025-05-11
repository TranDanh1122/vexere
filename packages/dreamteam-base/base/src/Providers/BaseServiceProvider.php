<?php

namespace DreamTeam\Base\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Base\Supports\Action;
use DreamTeam\Base\Supports\Filter;
use DreamTeam\Base\Supports\GoogleFonts;
use DreamTeam\Base\Supports\SettingsManager;
use DreamTeam\Base\Supports\SettingStore;
use Illuminate\Foundation\AliasLoader;
use DreamTeam\Base\Models\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use DreamTeam\Base\Facades\BaseHelper as BaseHelperFacade;
use DreamTeam\Base\Repositories\Eloquent\TableOptionRepository;
use DreamTeam\Base\Services\Interfaces\TableOptionServiceInterface;
use DreamTeam\Base\Services\TableOptionService;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserServiceInterface;
use DreamTeam\Base\Events\ClearCacheEvent;
use DreamTeam\Base\Events\SendMailTemplateEvent;
use DreamTeam\Base\Events\RefreshCountAdminMenuEvent;
use DreamTeam\Base\Repositories\Eloquent\LanguageMetaRepository;
use DreamTeam\Base\Repositories\Interfaces\LanguageMetaRepositoryInterface;
use DreamTeam\Base\Events\UpdateAttributeImageInContentEvent;
use DreamTeam\Base\Facades\CacheHelper;
use DreamTeam\Base\Listeners\UpdateAttributeImageInContentListener;
use DreamTeam\Base\Facades\MenuStore as MenuStoreFacade;
use DreamTeam\Base\Repositories\Eloquent\SeoRepository;
use DreamTeam\Base\Services\Interfaces\SeoServiceInterface;
use DreamTeam\Base\Services\SeoService;
use DreamTeam\Base\Repositories\Eloquent\SlugRepository;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use DreamTeam\Base\Services\SlugService;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\LanguageMetaService;
use DreamTeam\Base\Repositories\Eloquent\SystemLogRepository;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Base\Services\SystemLogService;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\BaseService;
use DreamTeam\Base\Repositories\Eloquent\MenuRepository;
use DreamTeam\Base\Services\Interfaces\MenuServiceInterface;
use DreamTeam\Base\Services\MenuService;
use DreamTeam\Base\Repositories\Eloquent\SettingRepository;
use DreamTeam\Base\Services\Interfaces\SettingServiceInterface;
use DreamTeam\Base\Services\SettingService;
use DreamTeam\Base\Repositories\Eloquent\CurrencyRepository;
use DreamTeam\Base\Services\Interfaces\CurrencyServiceInterface;
use DreamTeam\Base\Services\CurrencyService;
use DreamTeam\Base\Facades\Currency as CurrencyFacade;
use DreamTeam\Base\Http\Middleware\CheckAuthMarketPlace;
use DreamTeam\Base\Listeners\ClearCacheListener;
use DreamTeam\Base\Listeners\SendMailTemplateListener;
use DreamTeam\Base\Listeners\RefreshCountAdminMenuListener;
use DreamTeam\Base\Models\SystemLog;
use DreamTeam\Base\Services\DashboardService;
use DreamTeam\Base\Services\Interfaces\DashboardServiceInterface;
use DreamTeam\Base\Supports\HtmlBuilder;
use DreamTeam\Customer\Services\Interfaces\CustomerLabelServiceInterface;
use DreamTeam\Customer\Services\Interfaces\CustomerServiceInterface;
use DreamTeam\ProductSource\Services\Interfaces\ProductSourceLabelServiceInterface;
use DreamTeam\ProductSource\Services\Interfaces\ProductSourceServiceInterface;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register config file here (Chỉ áp dụng cho configs không sắp sếp theo thứ tự)
     * alias => path
     */
    private $configFile = [
        'app'           => 'app.php',
        'DreamTeamWidget'    => 'DreamTeamWidget.php'
    ];

    /**
     * Register commands file here
     * alias => path
     */
    protected $commands = [
        'DreamTeam\Base\Commands\DreamTeamClearCommand',
        'DreamTeam\Base\Commands\UpdateAttributeImageInContentCommand',
        'DreamTeam\Base\Commands\MigrateSettingMailContentCommand',
    ];

    /**
     * Register middleare file here
     * name => middleware
     */
    protected $middleare = [
        'check-auth-marketplace' => CheckAuthMarketPlace::class
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
        $this->app->singleton('Core:action', function () {
            return new Action();
        });

        $this->app->singleton('Core:filter', function () {
            return new Filter();
        });

        $this->app->singleton('Core:google-fonts', function (Application $app) {
            return new GoogleFonts(
                filesystem: $app->make(FilesystemManager::class)->disk('public'),
                path: 'fonts',
                inline: true,
                userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
            );
        });
        $this->app->singleton(SettingsManager::class, function (Application $app) {
            return new SettingsManager($app);
        });

        $this->app->singleton(SettingStore::class, function (Application $app) {
            return $app->make(SettingsManager::class)->driver();
        });

        AliasLoader::getInstance()->alias('Setting', Setting::class);
        AliasLoader::getInstance()->alias('BaseHelper', BaseHelperFacade::class);
        AliasLoader::getInstance()->alias('MenuStoreHelper', MenuStoreFacade::class);
        AliasLoader::getInstance()->alias('CurrencyHelper', CurrencyFacade::class);
        AliasLoader::getInstance()->alias('CacheHelper', CacheHelper::class);

        $this->app->bind(TableOptionServiceInterface::class, function ($app) {
            return new TableOptionService(
                new TableOptionRepository()
            );
        });
        $this->app->bind(LanguageMetaRepositoryInterface::class, function ($app) {
            return new LanguageMetaRepository();
        });
        $this->app->bind(SeoServiceInterface::class, function ($app) {
            return new SeoService(
                new SeoRepository()
            );
        });
        $this->app->bind(SlugServiceInterface::class, function ($app) {
            return new SlugService(
                new SlugRepository()
            );
        });
        $this->app->bind(LanguageMetaServiceInterface::class, function ($app) {
            return new LanguageMetaService(
                new LanguageMetaRepository()
            );
        });
        $this->app->bind(SystemLogServiceInterface::class, function ($app) {
            return new SystemLogService(
                new SystemLogRepository(),
                new SlugRepository(),
                new SeoRepository(),
                new LanguageMetaRepository(),
            );
        });
        $this->app->bind(BaseServiceInterface::class, function ($app) {
            return new BaseService(
                $app->make(SlugServiceInterface::class),
                $app->make(SeoServiceInterface::class),
                $app->make(LanguageMetaServiceInterface::class),
                $app->make(SystemLogServiceInterface::class)
            );
        });
        $this->app->bind(MenuServiceInterface::class, function ($app) {
            return new MenuService(
                new MenuRepository()
            );
        });
        $this->app->bind(SettingServiceInterface::class, function ($app) {
            return new SettingService(
                new SettingRepository()
            );
        });

        $this->app->bind(CurrencyServiceInterface::class, function ($app) {
            return new CurrencyService(
                new CurrencyRepository(),
            );
        });

        $this->app->bind(DashboardServiceInterface::class, function ($app) {
            return new DashboardService(
                $app->make(CustomerServiceInterface::class),
                $app->make(ProductSourceServiceInterface::class),
                $app->make(AdminUserServiceInterface::class),
                $app->make(CustomerLabelServiceInterface::class),
                $app->make(ProductSourceLabelServiceInterface::class),
            );
        });

        $this->app->alias('Html', HtmlBuilder::class);
        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder($app['url'], $app['view']);
        });
    }

    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->registerModule();

        $this->publishCore();

        $this->registerMiddleware();

        $this->app->booted(function () {

            menu_store()
                ->registerLocation([
                    'id' => \DreamTeam\Base\Models\Menu::PRIMARY,
                    'priority' => 0,
                    'name' => 'Core::admin.menu.location.primary'
                ])
                ->registerLocation([
                    'id' => \DreamTeam\Base\Models\Menu::SECONDARY,
                    'priority' => 1,
                    'name' => 'Menu Footer'
                ]);
            if (defined('FILTER_LIST_DATA_TABLE_QUERY')) {
                add_filter(FILTER_LIST_DATA_TABLE_QUERY, function ($datas, string $tableName, array $conditions, array $customConditions, Request $request) {
                    if ($tableName == (new SystemLog())->getTable() && count($customConditions)) {
                        $joinName = $conditions['system_logs.type']['='] ?? '';
                        if (isset($customConditions['type_name']) && !empty($customConditions['type_name']) && !empty($joinName)) {
                            $datas = $datas->join($joinName, $joinName . '.id', 'system_logs.type_id')
                                ->where($joinName . '.name', 'LIKE', '%' . str_replace(' ', '%', $customConditions['type_name']) . '%');
                        }
                        return $datas;
                    }
                    return $datas;
                }, 135, 5);
            }

            if (Schema::hasTable('settings')) {
                $readingConfig = getOption('reading', null, false);
                $config = $this->app->make('config');

                $config->set([
                    'dreamteam_asset.version' => $readingConfig['asset_version'] ?? $config->get('dreamteam_asset.version'),
                ]);
            }
        });

        $this->app['events']->listen(RouteMatched::class, function () {
            admin_menu()
                ->registerItem([
                    'id'          => 'dashboard',
                    'priority'    => 1,
                    'parent_id'   => '',
                    'type'        => 'single',
                    'name'        => 'Core::admin.admin_menu.overview',
                    'icon'        => 'bx bx-calendar',
                    'route'       => 'admin.home',
                    'role'        => 'home',
                    'permissions' => [],
                ])

                // Giao diện
                ->registerItem([
                    'id'          => 'group_interface',
                    'priority'    => 12,
                    'parent_id'   => '',
                    'route'       => 'admin.settings.groupInterface',
                    'type'        => 'multiple',
                    'name'        => 'Core::admin.admin_menu.interface_2',
                    'icon'        => 'bx bx-layout',
                    'childs'      => []
                ])
                ->registerItem([
                    'id'             => 'group_interface_menu',
                    'priority'       => 2,
                    'parent_id'      => 'group_interface',
                    'name'           => 'Core::admin.menu.name',
                    'route'          => 'admin.menus.index',
                    'role'           => 'menus_index',
                    'active'         => ['admin.menus.index', 'admin.menus.edit', 'admin.menus.create'],
                    'permissions'    => ['menus_index', 'menus_edit', 'menus_create'],
                    'description'    => 'Core::admin.menu.description',
                ])
                ->registerItem([
                    'id'          => 'settings_custom_css',
                    'priority'    => 4,
                    'parent_id'   => 'group_interface',
                    'name'        => 'CSS',
                    'route'       => 'admin.settings.custom_css',
                    'role'        => 'settings_custom_css',
                    'permissions' => ['settings_custom_css'],
                    'description' => 'Core::admin.custom_css_desc',
                ])
                ->registerItem([
                    'id'          => 'settings_home',
                    'priority'    => 4,
                    'parent_id'   => 'group_interface',
                    'name'        => 'Trang chủ',
                    'route'       => 'admin.settings.home',
                    'role'        => 'settings_home',
                    'permissions' => ['settings_home'],
                    'description' => 'Cài đặt trang chủ',
                ])
                // Cài đặt
                ->registerItem([
                    'id'             => 'group_setting',
                    'priority'       => 13,
                    'parent_id'      => '',
                    'type'           => 'multiple',
                    'route'          => 'admin.settings.groupConfig',
                    'name'           => 'Core::admin.admin_menu.config',
                    'icon'           => 'bx bx-cog',
                ])
                ->registerItem([
                    'id'             => 'group_setting_email',
                    'priority'       => 5,
                    'parent_id'      => 'group_setting',
                    'type'           => 'multiple',
                    'route'          => '',
                    'name'           => 'Core::admin.admin_menu.email',
                ])
                ->registerItem([
                    'id'          => 'settings_email_content',
                    'priority'    => 0,
                    'parent_id'   => 'group_setting_email',
                    'name'      => 'Core::admin.admin_menu.email_content',
                    'route'     => 'admin.settings.email_contents',
                    'role'      => 'settings_email_content',
                    'permissions' => ['settings_email_content'],
                    'description' => 'Core::admin.desc_menu.email_content',
                ])
                ->registerItem([
                    'id'             => 'group_setting_email',
                    'priority'       => 5,
                    'parent_id'      => 'group_setting',
                    'type'           => 'multiple',
                    'route'          => '',
                    'name'           => 'Core::admin.admin_menu.email',
                ])
                ->registerItem([
                    'id'          => 'settings_email_content',
                    'priority'    => 0,
                    'parent_id'   => 'group_setting_email',
                    'name'      => 'Core::admin.admin_menu.email_content',
                    'route'     => 'admin.settings.email_contents',
                    'role'      => 'settings_email_content',
                    'permissions' => ['settings_email_content'],
                ])
                ->registerItem([
                    'id'          => 'settings_email',
                    'priority'    => 1,
                    'parent_id'   => 'group_setting_email',
                    'name'      => 'Core::admin.admin_menu.email_configuration',
                    'route'     => 'admin.settings.email',
                    'role'      => 'settings_email',
                    'permissions' => ['settings_email'],
                    'description'      => 'Core::admin.desc_menu.email_configuration',
                ])
                ->registerItem([
                    'id'             => 'group_settings_siteLanguage',
                    'priority'       => 2,
                    'parent_id'      => 'group_setting',
                    'type'           => 'multiple',
                    'route'          => '',
                    'name'           => 'Core::admin.admin_menu.language_3',
                    'icon'           => 'bx bx-cog',
                ])
                ->registerItem([
                    'id'          => 'job_statuses',
                    'priority'    => 999,
                    'parent_id'   => 'group_setting',
                    'type'        => 'single',
                    'name'        => 'JobStatus::admin.name',
                    'icon'        => 'fas fa-tasks',
                    'route'       => 'admin.job_statuses.index',
                    'role'        => 'job_statuses_index',
                    'permissions' => ['job_statuses_index'],
                    'description' => 'JobStatus::admin.description',
                ])

                // Media
                ->registerItem([
                    'id'          => 'media_index',
                    'priority'    => 10,
                    'parent_id'   => '',
                    'type'        => 'single',
                    'name'        => 'Media',
                    'icon'        => 'fas fa-photo-video',
                    'route'       => 'media.index',
                    'role'        => 'media_index',
                    'permissions' => ['media_index']
                ])
                ->registerItem([
                    'id'          => 'settings_media',
                    'priority'    => 3,
                    'parent_id'   => 'group_setting',
                    'name'        => 'Media',
                    'route'       => 'admin.settings.media',
                    'role'        => 'settings_media',
                    'permissions' => ['settings_media'],
                    'description' => 'Core::admin.desc_menu.settings_media',
                ])
                ->registerItem([
                    'id'          => 'package_system_logs',
                    'priority'    => 93,
                    'parent_id'   => '',
                    'type'        => 'single',
                    'name'        => 'Core::admin.admin_menu.system_logs',
                    'icon'        => 'bx bx-log-in-circle',
                    'route'       => 'admin.system_logs.index',
                    'role'        => 'system_logs_index',
                    'permissions' => ['system_logs'],
                    'active'      => ['admin.system_logs.show']
                ])
                
                ->registerItem([
                    'id'          => 'settings_code',
                    'priority'    => 12,
                    'parent_id'   => 'group_setting',
                    'name'        => 'Core::admin.admin_menu.embed',
                    'route'       => 'admin.settings.code',
                    'role'        => 'settings_code',
                    'permissions' => ['settings_code'],
                    'description' => 'Core::admin.desc_menu.embed',
                ])
                ->registerItem([
                    'id'          => 'settings_theme_config',
                    'priority'    => 1,
                    'parent_id'   => 'group_interface',
                    'name'        => 'Nhân diện thương hiệu',
                    'route'       => 'admin.settings.theme_config',
                    'role'        => 'settings_theme_config',
                    'permissions' => ['settings_theme_config'],
                    'description' => 'Cài đặt logo, Icon ... cho website',
                ])
                ->registerItem([
                    'id'             => 'group_interface_settings_general',
                    'priority'       => 5,
                    'parent_id'      => 'group_interface',
                    'name'           => 'Core::admin.admin_menu.interface_setting',
                    'route'          => 'admin.settings.general',
                    'role'           => 'settings_general',
                    'permissions'    => ['settings_general']
                ])
                ->registerItem([
                    'id'          => 'settings_reading',
                    'priority'    => 1,
                    'parent_id'   => 'group_setting',
                    'name'        => 'Core::admin.setting.reading.title',
                    'route'       => 'admin.settings.reading',
                    'role'        => 'settings_reading',
                    'permissions' => ['settings_reading']
                ]);
        });

        $this->app['events']->listen(UpdateAttributeImageInContentEvent::class, UpdateAttributeImageInContentListener::class);
        $this->app['events']->listen(ClearCacheEvent::class, ClearCacheListener::class);
        $this->app['events']->listen(SendMailTemplateEvent::class, SendMailTemplateListener::class);
        $this->app['events']->listen(RefreshCountAdminMenuEvent::class, RefreshCountAdminMenuListener::class);

    }

    /*
    * Đăng ký tự động các modules
    */
    private function registerModule()
    {
        $modulePath = __DIR__ . '/../../';
        $moduleName = 'Core';
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
    public function publishCore()
    {
        if ($this->app->runningInConsole()) {
            $assets = [
                __DIR__ . '/../../resources/assets' => public_path('vendor/core/core/base'),
            ];
            $config = [
                __DIR__ . '/../../config/permark_links.php' => config_path('permark_links.php'),
                __DIR__ . '/../../config/DreamTeamWidget.php' => config_path('DreamTeamWidget.php'),
                __DIR__ . '/../../config/DreamTeamModule.php' => config_path('DreamTeamModule.php'),
            ];
            $lang = [
                __DIR__ . '/../../resources/lang' => lang_path('vendor/Core'),
            ];
            $all = array_merge($assets, $config, $lang);
            // Khởi chạy chung theo core
            $this->publishes($all, 'dreamteam/core');
            $this->publishes($assets, 'dreamteam/core/assets');
            $this->publishes($config, 'dreamteam/core/config');
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
            $config = $this->app['config']->get($alias, []);
            $packageConfig = $this->mergeArrayConfigs(require __DIR__ . "/../../config/" . $path, $config);
            $this->app['config']->set($alias, $packageConfig);
        }
    }

    /**
     * Merge config để lấy ra mảng chung
     * Ưu tiên lấy config ở app
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeArrayConfigs(array $original, array $merging)
    {
        $array = array_merge($original, $merging);
        foreach ($original as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if (!Arr::exists($merging, $key)) {
                continue;
            }
            if (is_numeric($key)) {
                continue;
            }
            $array[$key] = $this->mergeArrayConfigs($value, $merging[$key]);
        }
        return $array;
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
