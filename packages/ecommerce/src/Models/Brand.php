<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;

class Brand extends BaseModel
{
    protected $fillable = [
        'name',
        'ower_name',
        'ower_phone',
        'address',
        'detail',
        'status'
    ];
    public function product()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }
}
