<?php

namespace DreamTeam\Base\Facades;

use DreamTeam\Base\Supports\CacheHelper as CacheHelperSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string|array|null getMediaConfig(string $key, int|string|bool|null $default = null)
 * @method static getOption(string $settingName, string $locale = null, bool $hasLocale = true)
 * @method static string|null getSettingByKey(string $key, $default)
 * @method static getStorageHost()
 *
 * @see \DreamTeam\Base\Supports\Action
 */
class CacheHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CacheHelperSupport::class;
    }
}
