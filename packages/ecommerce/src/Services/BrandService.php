<?php

namespace DreamTeam\Ecommerce\Services;

use DreamTeam\Base\Services\CrudService;
use DreamTeam\Ecommerce\Repositories\Interfaces\BrandRepositoryInterface;
use DreamTeam\Ecommerce\Services\Interfaces\BrandServiceInterface;

class BrandService extends CrudService implements BrandServiceInterface
{

    public function __construct(
        BrandRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
}
