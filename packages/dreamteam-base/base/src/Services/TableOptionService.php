<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\TableOptionRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\TableOptionServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use Mail;

class TableOptionService extends CrudService implements TableOptionServiceInterface
{

    public function __construct(
        TableOptionRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

}
