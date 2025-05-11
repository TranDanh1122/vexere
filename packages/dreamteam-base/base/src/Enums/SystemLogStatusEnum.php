<?php

namespace DreamTeam\Base\Enums;

use DreamTeam\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static SystemLogStatusEnum CREATE()
 * @method static SystemLogStatusEnum UPDATE()
 * @method static SystemLogStatusEnum LOGIN()
 * @method static SystemLogStatusEnum QUICK_DELETE()
 * @method static SystemLogStatusEnum QUICK_UPDATE()
 * @method static SystemLogStatusEnum QUICK_RESTORE()
 * @method static SystemLogStatusEnum DELETE_FOREVER()
 */
class SystemLogStatusEnum extends Enum
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const LOGIN = 'login';
    public const QUICK_DELETE = 'quick_delete';
    public const QUICK_UPDATE = 'quick_update';
    public const QUICK_RESTORE = 'quick_restore';
    public const DELETE_FOREVER = 'delete_forever';

    public static $langPath = 'Core::admin';

    public function toHtml(): string|HtmlString
    {
        return match ($this->value) {
            self::CREATE => '<span>'. self::CREATE()->label() . '</span>',
            self::UPDATE => '<span>'. self::UPDATE()->label() . '</span>',
            self::LOGIN => '<span>'. self::LOGIN()->label() . '</span>',
            self::QUICK_DELETE => '<span>'. self::QUICK_DELETE()->label() . '</span>',
            self::QUICK_UPDATE => '<span>'. self::QUICK_UPDATE()->label() . '</span>',
            self::QUICK_RESTORE => '<span>'. self::QUICK_RESTORE()->label() . '</span>',
            self::DELETE_FOREVER => '<span>'. self::DELETE_FOREVER()->label() . '</span>',
            default => parent::toHtml(),
        };
    }
    
}
