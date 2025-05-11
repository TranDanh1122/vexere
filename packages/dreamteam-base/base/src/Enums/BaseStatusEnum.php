<?php

namespace DreamTeam\Base\Enums;

use DreamTeam\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static BaseStatusEnum DRAFT()
 * @method static BaseStatusEnum ACTIVE()
 * @method static BaseStatusEnum DEACTIVE()
 * @method static BaseStatusEnum DELETE()
 * @method static BaseStatusEnum DELETE_FOREVER()
 */
class BaseStatusEnum extends Enum
{
    public const DELETE = -1;
    public const DEACTIVE = 0;
    public const ACTIVE = 1;
    public const DRAFT = 2;
    public const DELETE_FOREVER = 'delete_forever';

    public static $langPath = 'Core::enums.statuses';

    public function toHtml(): string|HtmlString
    {
        return match ($this->value) {
            self::DRAFT => '<span>'. self::DRAFT()->label() . '</span>',
            self::DEACTIVE => '<span>'. self::DEACTIVE()->label() . '</span>',
            self::ACTIVE => '<span>'. self::ACTIVE()->label() . '</span>',
            self::DELETE => '<span>'. self::DELETE()->label() . '</span>',
            default => parent::toHtml(),
        };
    }

    public static function tableLabels(): array
    {
        $labels = self::labels();
        unset($labels[self::DELETE]);
        unset($labels[self::DELETE_FOREVER]);
        return $labels;
    }
}
