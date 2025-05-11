<?php

namespace DreamTeam\JobStatus\Services;

use DreamTeam\JobStatus\Repositories\Interfaces\JobStatusRepositoryInterface;
use DreamTeam\JobStatus\Services\Interfaces\JobStatusServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use Mail;

class JobStatusService extends CrudService implements JobStatusServiceInterface
{


    public function __construct(
        JobStatusRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

}
