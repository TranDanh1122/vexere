<?php

namespace DreamTeam\Base\Facades;

use DreamTeam\Base\Supports\SettingStore;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(array|string $key, mixed $default = null)
 * @method static mixed getByKey(string $key, mixed $default = null)
 * @method static bool has(string $key)
 * @method static \DreamTeam\Base\Supports\SettingStore set(string|array $key, mixed $value = null)
 * @method static \DreamTeam\Base\Supports\SettingStore forget(string $key)
 * @method static \DreamTeam\Base\Supports\SettingStore forgetAll()
 * @method static array all()
 * @method static bool save()
 * @method static void load(bool $force = false)
 *
 * @see \DreamTeam\Base\Supports\SettingStore
 */
class Setting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingStore::class;
    }
}
