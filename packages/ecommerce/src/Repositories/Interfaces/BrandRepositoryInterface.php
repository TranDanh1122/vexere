<?php

namespace DreamTeam\Ecommerce\Repositories\Interfaces;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface BrandRepositoryInterface extends BaseRepositoryInterface
{
    public function getBrandByCategoryIds($categoryIds, $productIdByCategoryMap);

    public function getAllActive($lang = null);

    public function getBrandWithCategory($data);
}
