<?php

namespace DreamTeam\Base\Supports;

use DreamTeam\Base\Models\BaseModel;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;

class Helper
{
    public static function autoload(string $directory): void
    {
        $helpers = File::glob($directory . '/*.php');
        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }


    public static function removeModuleFiles(string $module, string $type = 'packages'): bool
    {
        $folders = [
            public_path('vendor/core/' . $module),
            resource_path('assets/' . $module),
            resource_path('views/vendor/'. $module),
            lang_path('vendor/' . $module),
            config_path($type . '/' . $module),
        ];

        foreach ($folders as $folder) {
            if (File::isDirectory($folder)) {
                File::deleteDirectory($folder);
            }
        }

        return true;
    }


    public static function removeThemeFiles(string $module, string $type = 'packages'): bool
    {
        $folders = [
            public_path('assets/' . $module),
            resource_path('assets/' . $module),
            resource_path('views/themes/' . $module),
            lang_path('themes/' . $module),
            config_path($type . '/' . $module),
        ];

        foreach ($folders as $folder) {
            if (File::isDirectory($folder)) {
                File::deleteDirectory($folder);
            }
        }

        return true;
    }


    public static function removeLangPublishFiles(): bool
    {
        $folders = [
            lang_path('vendor'),
            lang_path('themes'),
        ];

        foreach ($folders as $folder) {
            if (File::isDirectory($folder)) {
                File::deleteDirectory($folder);
            }
        }

        return true;
    }

    public static function isConnectedDatabase(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (Exception) {
            return false;
        }
    }

    public static function clearCache(): bool
    {
        Event::dispatch('cache:clearing');

        try {
            Cache::flush();
            if (! File::exists($storagePath = storage_path('framework/cache'))) {
                return true;
            }

            foreach (File::files($storagePath) as $file) {
                if (preg_match('/facade-.*\.php$/', $file)) {
                    File::delete($file);
                }
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }

        Event::dispatch('cache:cleared');

        return true;
    }

    public static function isActivatedLicense(): bool
    {
        if (! File::exists(storage_path('.license'))) {
            return false;
        }

        $coreApi = new Core();

        $result = $coreApi->verifyLicense(true);

        if (! $result['status']) {
            return false;
        }

        return true;
    }


    public static function getIpFromThirdParty(): bool|string|null
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://ipecho.net/plain');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($curl);

        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpStatus == 200) {
            return $response ?: Request::ip();
        }

        return Request::ip();
    }

    public static function isIniValueChangeable(string $setting): bool
    {
        static $iniAll;

        if (! isset($iniAll)) {
            $iniAll = false;
            // Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
            if (function_exists('ini_get_all')) {
                $iniAll = ini_get_all();
            }
        }

        // Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
        if (isset($iniAll[$setting]['access']) && (INI_ALL === ($iniAll[$setting]['access'] & 7) || INI_USER === ($iniAll[$setting]['access'] & 7))) {
            return true;
        }

        // If we were unable to retrieve the details, fail gracefully to assume it's changeable.
        if (! is_array($iniAll)) {
            return true;
        }

        return false;
    }

    public static function convertHrToBytes(string|float|int|null $value): float|int
    {
        $value = strtolower(trim($value));
        $bytes = (int)$value;

        if (str_contains($value, 'g')) {
            $bytes *= 1024 * 1024 * 1024;
        } elseif (str_contains($value, 'm')) {
            $bytes *= 1024 * 1024;
        } elseif (str_contains($value, 'k')) {
            $bytes *= 1024;
        }

        // Deal with large (float) values which run into the maximum integer size.
        return min($bytes, PHP_INT_MAX);
    }
}
