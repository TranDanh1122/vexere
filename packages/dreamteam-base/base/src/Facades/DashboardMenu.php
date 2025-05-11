<?php

namespace DreamTeam\Base\Facades;

use DreamTeam\Base\Supports\DashboardMenu as DashboardMenuSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DreamTeam\Base\Supports\DashboardMenu registerItem(array $options)
 * @method static \DreamTeam\Base\Supports\DashboardMenu removeItem(array|string $id, $parentId = null)
 * @method static bool hasItem(string $id, string|null $parentId = null)
 * @method static \Illuminate\Support\Collection getAll()
 * @method static array getMenuSetting(string $key)
 * @method static array getAllRouteSetting(string $key)
 *
 * @see \DreamTeam\Base\Supports\DashboardMenu
 */
class DashboardMenu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DashboardMenuSupport::class;
    }
}
