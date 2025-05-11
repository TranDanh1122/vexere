<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\MenuRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\MenuServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;

class MenuService extends CrudService implements MenuServiceInterface
{

    public function __construct(
        MenuRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

}
