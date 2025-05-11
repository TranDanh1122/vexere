<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;

class ProductSchedule extends BaseModel
{
    protected $fillable = [
        'product_id',
        'direction',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'time',
        'time_run',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
