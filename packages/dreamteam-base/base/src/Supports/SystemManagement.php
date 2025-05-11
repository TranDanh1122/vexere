<?php

namespace DreamTeam\Base\Supports;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Carbon\Carbon;
use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Media\Facades\RvMedia;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use DreamTeam\Media\Storage\BunnyCDN\BunnyCDNClient;

class SystemManagement
{
    public static function getComposerArray(): array
    {
        return BaseHelper::getFileData(base_path('composer.json'));
    }

    public static function getPackagesAndDependencies(array $packagesArray): array
    {
        $packages = [];
        foreach ($packagesArray as $key => $value) {
            $packageFile = base_path('vendor/' . $key . '/composer.json');

            if ($key === 'php' || !File::exists($packageFile)) {
                continue;
            }

            $composer = BaseHelper::getFileData($packageFile);

            $packages[] = [
                'name' => $key,
                'version' => $value,
                'dependencies' => Arr::get($composer, 'require', 'No dependencies'),
                'dev-dependencies' => Arr::get($composer, 'require-dev', 'No dependencies'),
            ];
        }

        return $packages;
    }

    public static function getSystemEnv(): array
    {
        $app = app();

        return [
            'version' => $app->version(),
            'timezone' => $app['config']->get('app.timezone'),
            'debug_mode' => $app->hasDebugModeEnabled(),
            'storage_dir_writable' => File::isWritable($app->storagePath()),
            'cache_dir_writable' => File::isReadable($app->bootstrapPath('cache')),
            'app_size' => 'N/A',
        ];
    }

    protected static function calculateAppSize(string $directory): int
    {
        $size = 0;

        foreach (File::glob(rtrim($directory, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += File::isFile($each) ? File::size($each) : self::calculateAppSize($each);
        }

        return $size;
    }

    public static function getServerEnv(): array
    {
        return [
            'version' => phpversion(),
            'memory_limit' => @ini_get('memory_limit'),
            'max_execution_time' => @ini_get('max_execution_time'),
            'server_software' => Request::server('SERVER_SOFTWARE'),
            'server_os' => function_exists('php_uname') ? php_uname() : 'N/A',
            'database_connection_name' => DB::getDefaultConnection(),
            'ssl_installed' => request()->isSecure(),
            'cache_driver' => Cache::getDefaultDriver(),
            'session_driver' => Session::getDefaultDriver(),
            'queue_connection' => Queue::getDefaultDriver(),
            'allow_url_fopen_enabled' => @ini_get('allow_url_fopen'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'curl' => extension_loaded('curl'),
            'exif' => extension_loaded('exif'),
            'pdo' => extension_loaded('pdo'),
            'fileinfo' => extension_loaded('fileinfo'),
            'tokenizer' => extension_loaded('tokenizer'),
            'imagick_or_gd' => (extension_loaded('imagick') || extension_loaded('gd')) && extension_loaded(RvMedia::getImageProcessingLibrary()),
            'zip' => extension_loaded('zip'),
            'iconv' => extension_loaded('iconv'),
        ];
    }

    public static function getMemoryLimitAsMegabyte(): int
    {
        $memoryLimit = @ini_get('memory_limit') ?: 0;

        if (!$memoryLimit) {
            return 0;
        }

        if (preg_match('/^(\d+)(.)$/', $memoryLimit, $matches)) {
            if ($matches[2] === 'M') {
                return (int) $matches[1];
            }

            if ($matches[2] === 'K') {
                return (int) ((int) $matches[1] / 1024);
            }

            if ($matches[2] === 'G') {
                return (int) ((int) $matches[1] * 1024);
            }
        }

        return (int)$memoryLimit;
    }

    public static function getMaximumExecutionTime(): int
    {
        return (int) (@ini_get('max_execution_time') ?: -1);
    }

    public static function getAppSize(): int
    {
        return self::calculateAppSize(app()->basePath());
    }

    public static function calculateStorageSize(int $size)
    {
        $data = getOption('theme_validate', 'all', false);
        $storage = $data['storage_capacity'] ?? 0;
        $storageAdditional = $data['storage_additional'] ?? [];
        if (!$storage) {
            $storage = match ($data['package']) {
                'vip'  => 4294967296,
                'base' => 2147483648,
                'pro'  => 4294967296
            };
        }
        $fullStorage = false;
        $warningStore = false;
        $warningStoreAddition = false;
        $statusTimeStoreAddition = false;
        $additionInformation = [];
        if (($storageAdditional['storage_capacity'] ?? 0) && !empty($storageAdditional['addition_end_time'])) {
            $additionStartTime = date('Y-m-d', strtotime($storageAdditional['addition_start_time'] ?? date('Y-m-d')));
            $additionEndTime = date('Y-m-d', strtotime($storageAdditional['addition_end_time']));
            if ($additionEndTime < date('Y-m-d')) {
                $statusTimeStoreAddition = true;
            } else {
                // hiện cảnh báo hết hạn trước 15 ngày
                $diff = (strtotime($additionEndTime) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                $warningStoreAddition = $diff <= 15;
                $storage += $storageAdditional['storage_capacity'] ?? 0;
            }
            $additionInformation = [
                'storage_size' => BaseHelper::humanFilesize($storageAdditional['storage_capacity']),
                'start_time' => $additionStartTime,
                'end_time' => $additionEndTime,
                'status_time' => $statusTimeStoreAddition,
                'alert_15_day' => $warningStoreAddition
            ];
        }
        if ($storage <= $size) $fullStorage = true;
        if (!$fullStorage && $size > $storage * 0.9) {
            $warningStore = true;
        }
        return compact('storage', 'fullStorage', 'warningStore', 'additionInformation', 'statusTimeStoreAddition', 'warningStoreAddition');
    }
}
