<?php

namespace DreamTeam\SyncLink\Models;

use DreamTeam\Base\Models\BaseModel;

class SyncLink extends BaseModel
{
	public $timestamps = false;

	protected $fillable = [
        'old', 'new', 'code', 'status',
    ];
}
