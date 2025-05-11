<?php

namespace DreamTeam\Base\Facades;

use DreamTeam\Base\Supports\MenuStore as MenuStoreSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DreamTeam\Base\Supports\MenuStore registerMenu(array $options)
 * @method static \Illuminate\Support\Collection getAll()
 * @method static \DreamTeam\Base\Supports\MenuStore registerLocation(array $options)
 * @method static array getLocations()
 *
 * @see \DreamTeam\Base\Supports\MenuStore
 */
class MenuStore extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MenuStoreSupport::class;
    }
}
