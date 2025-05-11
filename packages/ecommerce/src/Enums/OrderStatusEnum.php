<?php

namespace DreamTeam\Ecommerce\Enums;

use Illuminate\Support\HtmlString;
use DreamTeam\Base\Supports\Enum;

/**
 * @method static PaymentStatusEnum STATUS_NEW()
 * @method static PaymentStatusEnum STATUS_CANCEL()
 * @method static PaymentStatusEnum STATUS_SUCCESS()
 */
class OrderStatusEnum extends Enum
{
    public const STATUS_NEW = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_CANCEL = 3;

    public static $langPath = 'Ecommerce::order.statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::STATUS_NEW => '<span class="badge badge-info status-label">'. self::STATUS_NEW()->label() .'</span>',
            self::STATUS_CANCEL => '<span class="badge badge-danger status-label">'. self::STATUS_CANCEL()->label() .'</span>',
            self::STATUS_SUCCESS => '<span class="badge badge-success status-label">'. self::STATUS_SUCCESS()->label() .'</span>',
            default => parent::toHtml(),
        };
    }
}
