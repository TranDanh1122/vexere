<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;

class ProductLocation extends BaseModel
{
    protected $fillable = [
        'product_id',
        'location_id',
        'time',
        'type',
        'order',
        'status',
        'transit'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'location_id' => 'integer',
        'order' => 'integer',
        'status' => 'integer',
        'type' => 'string',
    ];
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
