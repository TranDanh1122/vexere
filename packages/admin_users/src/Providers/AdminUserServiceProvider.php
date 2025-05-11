<?php

namespace DreamTeam\AdminUser\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Events\RouteMatched;
use DreamTeam\AdminUser\Repositories\Eloquent\AdminUserRoleRepository;
use DreamTeam\AdminUser\Repositories\Eloquent\AdminUserRepository;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserServiceInterface;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserRoleServiceInterface;
use DreamTeam\AdminUser\Services\AdminUserService;
use DreamTeam\AdminUser\Services\AdminUserRoleService;
use Illuminate\Auth\Events\Login;
use DreamTeam\AdminUser\Http\Middleware\CorsMiddleware;
use DreamTeam\AdminUser\Http\Middleware\ForceJsonResponseMiddleware;
use DreamTeam\AdminUser\Listeners\LoginViaRemember;

class AdminUserServiceProvider extends ServiceProvider
{
    /**
     * Register config file here (Chỉ áp dụng cho configs không sắp sếp theo thứ tự)
     * alias => path
     */
    private $configFile = [
        'app'       => 'app.php',
        'auth'      => 'auth.php',
    ];

    /**
     * Register commands file here
     * alias => path
     */
    protected $commands = [
        'DreamTeam\AdminUser\Commands\AdminUserSeedCommand',
        'DreamTeam\AdminUser\Commands\LicenseSeedCommand',
    ];

    /**
     * Register middleare file here
     * name => middleware
     */
    protected $middleare = [
        'auth-admin' => '\DreamTeam\AdminUser\Http\Middleware\AdminAuthenticate',
        'only-dev' => '\DreamTeam\AdminUser\Http\Middleware\OnlyDev',
        'check-license' => '\DreamTeam\AdminUser\Http\Middleware\CheckLicense',
        '2fa' => '\DreamTeam\AdminUser\Http\Middleware\LoginSecurityMiddleware',
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

        $this->app->bind(AdminUserServiceInterface::class, function($app) {
            return new AdminUserService(
                new AdminUserRepository()
            );
        });
        $this->app->bind(AdminUserRoleServiceInterface::class, function($app) {
            return new AdminUserRoleService(
                new AdminUserRoleRepository()
            );
        });
    }

	public function boot()
    {
        Schema::defaultStringLength(191);

		$this->registerModule();

        $this->publishCore();

        $this->registerMiddleware();

        $this->app['events']->listen(RouteMatched::class, function () {
            admin_menu()
                 ->registerItem([
                    'id'             => 'package_admin_users',
                    'priority'       => 9,
                    'parent_id'      => '',
                    'type'           => 'multiple',
                    'route'          => '',
                    'name'           => 'AdminUser::admin.account',
                    'icon'           => 'bx bx-user-circle',
                ])
                ->registerItem([
                    'id'          => 'package_admin_users_index',
                    'priority'    => 0,
                    'parent_id'   => 'package_admin_users',
                    'name'        => 'AdminUser::admin.list_admin',
                    'route'       => 'admin.admin_users.index',
                    'role'        => 'admin_users_index',
                    'permissions' => ['admin_users_index'],
                    'active'      => ['admin.admin_users.edit' ]
                ])
                ->registerItem([
                    'id'          => 'package_admin_user_roles_create',
                    'priority'    => 1,
                    'parent_id'   => 'package_admin_users',
                    'name'        => 'AdminUser::admin.role_name',
                    'route'       => 'admin.admin_user_roles.index',
                    'role'        => 'admin_user_roles_index',
                    'permissions' => ['admin_user_roles_index'],
                    'active'      => ['admin.admin_user_roles.index', 'admin.admin_user_roles.edit', 'admin.admin_user_roles.create']
                ])
                ->registerItem([
                    'id'          => 'settings_googleAuthenticate',
                    'priority'    => 10,
                    'parent_id'   => 'group_setting',
                    'name'        => 'AdminUser::admin.2fa',
                    'route'       => 'admin.settings.googleAuthenticate',
                    'role'        => 'settings_googleAuthenticate',
                    'permissions' => ['settings_googleAuthenticate']
                ]);
        });

        $this->app->booted(function() {
            if (defined('ROLLBACK_DATA_FROM_LOG')) {
                add_filter(ROLLBACK_DATA_FROM_LOG, function($response, string $type, array $dataOld) {
                    if ($type == 'admin_users') {
                        $this->app->make(AdminUserServiceInterface::class)->insert($dataOld);
                        return ['success' => true];
                    } else if ($type == 'admin_user_roles') {
                        $this->app->make(AdminUserRoleServiceInterface::class)->insert($dataOld);
                        return ['success' => true];
                    }
                    return $response;
                }, 130, 3);
            }
        });

        $this->app['events']->listen(Login::class, LoginViaRemember::class);

        $this->app['events']->listen(RouteMatched::class, function () {
            $this->app['router']->pushMiddlewareToGroup('api', ForceJsonResponseMiddleware::class);
            // $this->app['router']->pushMiddlewareToGroup('api', CorsMiddleware::class);
        });
	}

	/*
    * Đăng ký tự động các modules
    */
    private function registerModule()
    {
    	$modulePath = __DIR__.'/../../';
    	$moduleName = 'AdminUser';
		// boot route
        if (File::exists($modulePath."routes/routes.php")) {
            $this->loadRoutesFrom($modulePath."/routes/routes.php");
        }
		// boot route
        if (File::exists($modulePath."routes/api.php")) {
            $this->loadRoutesFrom($modulePath."/routes/api.php");
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
                // __DIR__.'/../../resources/assets' => public_path(),
            ];
            $config = [
                __DIR__.'/../../config/google2fa.php' => config_path('google2fa.php'),
            ];
            $lang = [
                __DIR__ . '/../../resources/lang' => lang_path('vendor/AdminUser'),
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
    public function mergeConfig() {
        foreach ($this->configFile as $alias => $path) {
            $config = $this->app['config']->get($alias, []);
            $this->app['config']->set($alias, $this->mergeArrayConfigs(require __DIR__ . "/../../config/" . $path, $config));
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
            if (! is_array($value)) { continue; }
            if (! \Arr::exists($merging, $key)) { continue; }
            if (is_numeric($key)) { continue; }
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
