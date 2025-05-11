<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Enums\BaseStatusEnum;

class Filter extends BaseModel {
	
	protected $fillable = ['name', 'name_web', 'order', 'status'];

	public function filterDetail() {
    	return $this->hasMany('DreamTeam\Ecommerce\Models\FilterDetail', 'filter_id', 'id')->where('status', BaseStatusEnum::ACTIVE);
    }
}