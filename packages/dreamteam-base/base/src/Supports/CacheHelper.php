<?php

namespace DreamTeam\Base\Supports;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use DreamTeam\Base\Services\Interfaces\SettingServiceInterface;
use DreamTeam\Media\Facades\RvMedia;

class CacheHelper
{
    private static $mediaSetting = null;
    private static $options = [];
    private static $storageHost = null;

    public function getMediaConfig(string $key, int|string|bool|null $default = null): string|array|null
    {
        if (self::$mediaSetting === null) {
            self::$mediaSetting = Cache::remember('media_setting', now()->addDays(30), function () {
                return DB::table('settings')->where('key', 'like', 'media_%')->get();
            });
        }
        return self::$mediaSetting->where('key', $key)->first()->value ?? $default;
    }

    public function getOption(string $settingName, string $locale = null, bool $hasLocale = true)
    {
        $locale = $locale ?? App::getLocale();
        $cacheKey = 'setting_' . $settingName . '_' . $locale;

        if (!isset(self::$options[$cacheKey])) {
            self::$options[$cacheKey] = Cache::remember($cacheKey, now()->addDays(30), function () use ($settingName, $locale, $hasLocale) {
                return app(SettingServiceInterface::class)->getData($settingName, $hasLocale, $locale);
            });
        }

        return self::$options[$cacheKey];
    }


    public function getSettingByKey(string $key, $default): string|null
    {
        $cacheKey = 'setting_by_key_' . $key;

        if (!isset(self::$options[$cacheKey])) {
            self::$options[$cacheKey] = Cache::remember($cacheKey, now()->addDays(30), function () use ($key, $default) {
                return app(SettingServiceInterface::class)->findOne(['key' => $key])->value ?? $default;
            });
        }

        return self::$options[$cacheKey];
    }

    public function getStorageHost()
    {
        if (self::$storageHost === null) {
            self::$storageHost = Cache::remember('storage_url', now()->addDays(30), function () {
                return parse_url(RvMedia::getStorageDomain())['host'] ?? '';
            });
        }
        return self::$storageHost;
    }
}
