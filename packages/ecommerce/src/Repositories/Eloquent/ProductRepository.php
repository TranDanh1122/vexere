<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\Product;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected string|null|Model $model = Product::class;
}