<?php

namespace DreamTeam\Ecommerce\Enums;

use DreamTeam\Base\Facades\Html;
use DreamTeam\Base\Supports\Enum;

/**
 * @method static ProductTypeEnum SGVT()
 * @method static ProductTypeEnum VTSG()
 */
class DirectionTypeEnum extends Enum
{
    public const SGVT = 'sg_vt';
    public const VTSG = 'vt_sg';

    public static $langPath = 'Ecommerce::product.direction_type';

    public function toHtml()
    {
        return Html::tag('span', ' - ' . $this->label(), ['style' => 'font-size:12px;color:#333']);
    }
}
