<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductLocationRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Ecommerce\Models\ProductLocation;

class ProductLocationRepository extends BaseRepository implements ProductLocationRepositoryInterface
{
    protected string|null|Model $model = ProductLocation::class;
}
