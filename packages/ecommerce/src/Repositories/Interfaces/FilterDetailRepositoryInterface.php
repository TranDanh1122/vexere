<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface FilterDetailRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilterDetailBySlug($slugs);
}
