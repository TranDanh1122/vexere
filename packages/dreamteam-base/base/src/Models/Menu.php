<?php

namespace DreamTeam\Base\Models;
use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Events\ClearCacheEvent;

class Menu extends BaseModel
{
    const PRIMARY = 1;
    const SECONDARY = 2;
    
    protected $fillable = [
        'name', 'location', 'value', 'status'
    ];
}
