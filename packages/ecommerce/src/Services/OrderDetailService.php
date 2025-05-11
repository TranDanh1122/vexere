<?php

namespace DreamTeam\Ecommerce\Services;

use DreamTeam\Ecommerce\Repositories\Interfaces\OrderDetailRepositoryInterface;
use DreamTeam\Ecommerce\Services\Interfaces\OrderDetailServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use Illuminate\Database\Eloquent\Model;

class OrderDetailService extends CrudService implements OrderDetailServiceInterface
{
	public function __construct (
        OrderDetailRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

}