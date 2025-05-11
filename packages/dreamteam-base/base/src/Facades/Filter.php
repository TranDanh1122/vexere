<?php

namespace DreamTeam\Base\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed fire(string $action, array $args)
 * @method static void addListener(array|string|null $hook, \Closure|array|string $callback, int $priority = 20, int $arguments = 1)
 * @method static \DreamTeam\Base\Supports\ActionHookEvent removeListener(string $hook)
 * @method static array getListeners()
 *
 * @see \DreamTeam\Base\Supports\Filter
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Core:filter';
    }
}
