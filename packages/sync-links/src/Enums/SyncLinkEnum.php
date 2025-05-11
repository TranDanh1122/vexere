<?php

namespace DreamTeam\SyncLink\Enums;

use DreamTeam\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static SystemLogStatusEnum TEMPORARY()
 * @method static SystemLogStatusEnum FOREVER()
 */
class SyncLinkEnum extends Enum
{
    public const TEMPORARY = '301';
    public const FOREVER = '302';

    public static $langPath = 'SyncLink::admin';

    public function toHtml(): string|HtmlString
    {
        return match ($this->value) {
            self::TEMPORARY => '<span>'. self::TEMPORARY()->label() . '</span>',
            self::FOREVER => '<span>'. self::FOREVER()->label() . '</span>',
            default => parent::toHtml(),
        };
    }
    
}
