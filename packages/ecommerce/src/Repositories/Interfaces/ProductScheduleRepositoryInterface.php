<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface ProductScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductByConditions(array $data): LengthAwarePaginator|Collection;
}
