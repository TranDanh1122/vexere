<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Ecommerce\Enums\ProductTypeEnum;

class Product extends BaseModel
{
	protected $guarded = ['id'];


    public function brand()
    {
        return $this->belongsTo('\DreamTeam\Ecommerce\Models\Brand', 'brand_id', 'id');
    }

    public function productLocations()
    {
        return $this->hasMany(ProductLocation::class, 'product_id', 'id');
    }

    public function productFillters()
    {
        return $this->hasMany(ProductFilter::class, 'product_id', 'id');
    }

    public function productSchedules()
    {
        return $this->hasMany(ProductSchedule::class, 'product_id', 'id');
    }
    
    public function getPrice()
    {
        return $this->price;
    }

    public function getPriceOld()
    {
        return $this->price_old;
    }

    public function getDiscount()
    {
        $initPrice = $this->getPriceOld();
        if(!$initPrice) return 0;
        $sub = $initPrice - $this->getPrice();
        $discount = round(($sub / $initPrice), 2) * 100;
        return $discount;
    }

    public function getImage($size = '', $name = '')
    {
        return getImage($this->image, 'products', $size, $name);
    }

    public function getSlides() {
        $slides = explode(',', $this->slide);
        return array_merge($slides, [$this->image]);
    }
}
