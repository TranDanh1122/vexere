<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;

class FilterDetail extends BaseModel
{
    protected $fillable = ['filter_id', 'name', 'slug', 'order', 'status'];

    public function productFilter() {
    	return $this->hasMany('DreamTeam\Ecommerce\Models\ProductFilter', 'filter_id', 'id');
    }	
}