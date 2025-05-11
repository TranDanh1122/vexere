<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface LocationRepositoryInterface extends BaseRepositoryInterface
{
    public function getLocationByCategoryIds($categoryIds, $productIdByCategoryMap);

    public function getAllActive($lang = null);

    public function getLocationWithCategory($data);
}
