<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;

class ProductFilter extends BaseModel
{

	protected $fillable = ['product_id', 'filter_detail_id', 'filter_id', 'category_id'];
		
	public function product()
	{
    	return $this->hasOne('DreamTeam\Ecommerce\Models\Product', 'id', 'product_id');
    }
    public function filterDetail()
    {
    	return $this->hasOne('DreamTeam\Ecommerce\Models\FilterDetail', 'id', 'filter_detail_id');
    }
}