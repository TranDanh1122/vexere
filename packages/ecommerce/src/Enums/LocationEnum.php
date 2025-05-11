<?php

namespace DreamTeam\Ecommerce\Enums;

use DreamTeam\Base\Facades\Html;
use DreamTeam\Base\Supports\Enum;

/**
 * @method static ProductTypeEnum SG()
 * @method static ProductTypeEnum VT()
 */
class LocationEnum extends Enum
{
    public const SG = 'sg';
    public const VT = 'vt';

    public static $langPath = 'Ecommerce::product.location';

    public function toHtml()
    {
        return Html::tag('span', $this->label(), ['style' => 'font-size:12px;color:#333']);
    }
}
