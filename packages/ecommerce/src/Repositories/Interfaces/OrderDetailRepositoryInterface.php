<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface OrderDetailRepositoryInterface extends BaseRepositoryInterface
{
    public function getOrderDetailByConditionToExports(bool $hasPayment, array $inputData): Collection;
}
