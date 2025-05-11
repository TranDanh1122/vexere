<?php

namespace DreamTeam\AdminUser\Services;

use DreamTeam\AdminUser\Repositories\Interfaces\AdminUserRepositoryInterface;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use Mail;

class AdminUserService extends CrudService implements AdminUserServiceInterface
{


    public function __construct(
        AdminUserRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

}
