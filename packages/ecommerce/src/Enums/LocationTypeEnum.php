<?php

namespace DreamTeam\Ecommerce\Enums;

use DreamTeam\Base\Facades\Html;
use DreamTeam\Base\Supports\Enum;

/**
 * @method static ProductTypeEnum PICKUP()
 * @method static ProductTypeEnum DROPOFF()
 */
class LocationTypeEnum extends Enum
{
    public const PICKUP = 'pickup';
    public const DROPOFF = 'dropoff';

    public static $langPath = 'Ecommerce::product.location_type';

    public function toHtml()
    {
        return Html::tag('span', $this->label(), ['style' => 'font-size:12px;color:#333']);
    }
}
