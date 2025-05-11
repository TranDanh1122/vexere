<?php

namespace DreamTeam\Base\Models;
use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Events\ClearCacheEvent;

class Slug extends BaseModel
{
    protected $fillable = [
        'table', 'table_id', 'slug'
    ];   
}
