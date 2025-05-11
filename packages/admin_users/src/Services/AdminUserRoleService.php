<?php

namespace DreamTeam\AdminUser\Services;

use DreamTeam\AdminUser\Repositories\Interfaces\AdminUserRoleRepositoryInterface;
use DreamTeam\AdminUser\Services\Interfaces\AdminUserRoleServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use Mail;

class AdminUserRoleService extends CrudService implements AdminUserRoleServiceInterface
{


    public function __construct(
        AdminUserRoleRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

}
