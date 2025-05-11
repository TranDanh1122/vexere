<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;

class OrderDetail extends BaseModel
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function getTotalPrice()
    {
        $quantity = $this->quantity;
        $price = $this->price;
        return $quantity*$price;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productLocation()
    {
        return $this->belongsTo(ProductLocation::class, 'location_product_id', 'id');
    }

    public function productLocationReturn()
    {
        return $this->belongsTo(ProductLocation::class, 'return_location_product_id', 'id');
    }
}
