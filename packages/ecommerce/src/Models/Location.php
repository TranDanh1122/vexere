<?php

namespace DreamTeam\Ecommerce\Models;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Models\Pin;
use DreamTeam\Ecommerce\Enums\LocationEnum;

class Location extends BaseModel
{
    protected $fillable = [
        'name',
        'parent_id',
        'from',
        'address',
        'google_map',
        'status'
    ];

    protected $casts = [
        'from' => LocationEnum::class,
        'parent_id' => 'integer',
    ];
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id', 'id');
    }
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id', 'id');
    }
}
