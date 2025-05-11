<?php

namespace DreamTeam\Media\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\File;
use DreamTeam\Media\Chunks\Storage\ChunkStorage;
use DreamTeam\Media\Commands\ClearChunksCommand;
use DreamTeam\Media\Commands\CropImageCommand;
use DreamTeam\Media\Commands\DeleteThumbnailCommand;
use DreamTeam\Media\Commands\GenerateThumbnailCommand;
use DreamTeam\Media\Commands\InsertWatermarkCommand;
use DreamTeam\Media\Facades\RvMedia;
use DreamTeam\Media\Repositories\Eloquent\MediaFolderRepository;
use DreamTeam\Media\Repositories\Eloquent\MediaRepository;
use DreamTeam\Media\Repositories\Interfaces\MediaFolderInterface;
use DreamTeam\Media\Repositories\Interfaces\MediaRepositoryInterface;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use DreamTeam\Media\Commands\ConvertMediaCommand;
use Illuminate\Filesystem\AwsS3V3Adapter as IlluminateAwsS3V3Adapter;
use DreamTeam\Media\Storage\BunnyCDN\BunnyCDNAdapter;
use DreamTeam\Media\Storage\BunnyCDN\BunnyCDNClient;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Schema;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use DreamTeam\Base\Supports\SettingStore;
use DreamTeam\Media\Commands\ConvertThumbnailMediaCommand;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Register config file here (Chỉ áp dụng cho configs không sắp sếp theo thứ tự và không phải ghi đè lên file mặc định)
     * alias => path
     */
    private $configFile = [
        'dreamteam_media' => 'media.php',
    ];

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        // Đăng ký config cho từng Module
        $this->mergeConfig();

        $this->app->bind(MediaRepositoryInterface::class, function () {
            return new MediaRepository();
        });

        $this->app->bind(MediaFolderInterface::class, function () {
            return new MediaFolderRepository();
        });

        $this->app->singleton(ChunkStorage::class);

        if (!class_exists('RvMedia')) {
            AliasLoader::getInstance()->alias('RvMedia', RvMedia::class);
        }
    }

    public function boot()
    {
        $this->registerModule();

        $this->publishMedia();
        $this->mapFilesysterm();
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateThumbnailCommand::class,
                CropImageCommand::class,
                DeleteThumbnailCommand::class,
                InsertWatermarkCommand::class,
                ClearChunksCommand::class,
                ConvertMediaCommand::class,
                ConvertThumbnailMediaCommand::class
            ]);

            $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
                if (Schema::hasTable('settings') && RvMedia::getConfig('chunk.clear.schedule.enabled')) {
                    $schedule
                        ->command(ClearChunksCommand::class)
                        ->cron(RvMedia::getConfig('chunk.clear.schedule.cron'));
                }
            });
        }
    }

    private function registerModule()
    {
        $modulePath = __DIR__ . '/../../';
        $moduleName = 'media';

        // boot route
        if (File::exists($modulePath . "routes/routes.php")) {
            $this->loadRoutesFrom($modulePath . "/routes/routes.php");
        }
        if (File::exists($modulePath . "routes/api.php")) {
            $this->loadRoutesFrom($modulePath . "/routes/api.php");
        }

        // boot migration
        if (File::exists($modulePath . "database/migrations")) {
            $this->loadMigrationsFrom($modulePath . "database/migrations");
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
    public function publishMedia()
    {
        if ($this->app->runningInConsole()) {
            $assets = [
                __DIR__ . '/../../resources/assets' => public_path('vendor/core/core/media'),
            ];
            $config = [
                __DIR__ . '/../../config/media.php' => config_path('dreamteam_media.php'),
            ];
            $lang = [
                __DIR__ . '/../../resources/lang' => lang_path('vendor/media'),
            ];
            $all = array_merge($assets, $config, $lang);
            // Chạy riêng
            $this->publishes($all, 'dreamteam/media');
            $this->publishes($assets, 'dreamteam/media/assets');
            $this->publishes($config, 'dreamteam/media/config');
            $this->publishes($lang, 'dreamteam/media/lang');
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
            $this->mergeConfigFrom(__DIR__ . "/../../config/" . $path, $alias);
        }
    }

    private function mapFileSysterm()
    {
        if (Schema::hasTable('settings')) {
            Storage::extend('wasabi', function ($app, $config) {
                $config['url'] = 'https://' . $config['bucket'] . '.s3.' . $config['region'] . '.wasabisys.com/';

                $client = new S3Client([
                    'endpoint' => $config['url'],
                    'bucket_endpoint' => true,
                    'credentials' => [
                        'key' => $config['key'],
                        'secret' => $config['secret'],
                    ],
                    'region' => $config['region'],
                    'version' => 'latest',
                ]);

                $adapter = new AwsS3V3Adapter($client, $config['bucket'], trim($config['root'], '/'));

                return new IlluminateAwsS3V3Adapter(
                    new Filesystem($adapter, $config),
                    $adapter,
                    $config,
                    $client,
                );
            });

            Storage::extend('bunnycdn', function ($app, $config) {
                $adapter = new BunnyCDNAdapter(
                    new BunnyCDNClient(
                        $config['storage_zone'],
                        $config['api_key'],
                        $config['region']
                    ),
                    'https://' . $config['hostname']
                );

                return new FilesystemAdapter(
                    new Filesystem($adapter, $config),
                    $adapter,
                    $config
                );
            });

            $config = $this->app->make('config');
            $setting = $this->app->make(SettingStore::class);

            $mediaDriver = RvMedia::getMediaDriver();

            $config->set([
                'filesystems.default' => $mediaDriver,
                'filesystems.disks.public.throw' => true,
                'dreamteam_media.chunk.enabled' => (bool)$setting->get(
                    'media_chunk_enabled',
                    $config->get('dreamteam_media.chunk.enabled')
                ),
                'dreamteam_media.chunk.chunk_size' => (int)$setting->get(
                    'media_chunk_size',
                    $config->get('dreamteam_media.chunk.chunk_size')
                ),
                'dreamteam_media.chunk.max_file_size' => (int)$setting->get(
                    'media_max_file_size',
                    $config->get('dreamteam_media.chunk.max_file_size')
                ),
            ]);

            switch ($mediaDriver) {
                case 's3':
                    RvMedia::setS3Disk([
                        'key' => $setting->get('media_aws_access_key_id', $config->get('filesystems.disks.s3.key')),
                        'secret' => $setting->get('media_aws_secret_key', $config->get('filesystems.disks.s3.secret')),
                        'region' => $setting->get('media_aws_default_region', $config->get('filesystems.disks.s3.region')),
                        'bucket' => $setting->get('media_aws_bucket', $config->get('filesystems.disks.s3.bucket')),
                        'url' => $setting->get('media_aws_url', $config->get('filesystems.disks.s3.url')),
                        'endpoint' => $setting->get('media_aws_endpoint', $config->get('filesystems.disks.s3.endpoint')) ?: null,
                        'use_path_style_endpoint' => $config->get('filesystems.disks.s3.use_path_style_endpoint'),
                    ]);

                    break;
                case 'r2':
                    RvMedia::setR2Disk([
                        'key' => $setting->get('media_r2_access_key_id', $config->get('filesystems.disks.r2.key')),
                        'secret' => $setting->get('media_r2_secret_key', $config->get('filesystems.disks.r2.secret')),
                        'bucket' => $setting->get('media_r2_bucket', $config->get('filesystems.disks.r2.bucket')),
                        'url' => $setting->get('media_r2_url', $config->get('filesystems.disks.r2.url')),
                        'endpoint' => $setting->get('media_r2_endpoint', $config->get('filesystems.disks.r2.endpoint')) ?: null,
                        'use_path_style_endpoint' => $config->get('filesystems.disks.s3.use_path_style_endpoint'),
                    ]);

                    break;
                case 'wasabi':
                    RvMedia::setWasabiDisk([
                        'key' => $setting->get('media_wasabi_access_key_id'),
                        'secret' => $setting->get('media_wasabi_secret_key'),
                        'region' => $setting->get('media_wasabi_default_region'),
                        'bucket' => $setting->get('media_wasabi_bucket'),
                        'root' => $setting->get('media_wasabi_root', '/'),
                    ]);

                    break;

                case 'bunnycdn':
                    RvMedia::setBunnyCdnDisk([
                        'hostname' => $setting->get('media_bunnycdn_hostname'),
                        'storage_zone' => $setting->get('media_bunnycdn_zone'),
                        'api_key' => $setting->get('media_bunnycdn_key'),
                        'region' => $setting->get('media_bunnycdn_region'),
                    ]);

                    break;

                case 'do_spaces':
                    RvMedia::setDoSpacesDisk([
                        'key' => $setting->get('media_do_spaces_access_key_id'),
                        'secret' => $setting->get('media_do_spaces_secret_key'),
                        'region' => $setting->get('media_do_spaces_default_region'),
                        'bucket' => $setting->get('media_do_spaces_bucket'),
                        'endpoint' => $setting->get('media_do_spaces_endpoint'),
                    ]);

                    break;
            }
        }
    }
}
