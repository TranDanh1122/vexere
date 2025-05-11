<?php

namespace DreamTeam\Base\Models;

use DreamTeam\Base\Models\BaseModel;

class TableOption extends BaseModel {
	
	protected $guarded = ['id'];

	public function table()
	{
	    return $this->morphTo();
	}

}