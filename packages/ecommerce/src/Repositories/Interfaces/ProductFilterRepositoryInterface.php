<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface ProductFilterRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductIdByFilterIds($filterIds);

    public function deleteByFilterDetailId(array $filterDetailIds);
}
