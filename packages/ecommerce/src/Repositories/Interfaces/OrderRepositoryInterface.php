<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
	public function getOrderByconditionsToExports(bool $hasPayment, array $inputData, string $select = 'orders.*'): Collection;
}
