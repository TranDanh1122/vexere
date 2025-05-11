<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface FilterRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilterByIds($filterIds);
}
