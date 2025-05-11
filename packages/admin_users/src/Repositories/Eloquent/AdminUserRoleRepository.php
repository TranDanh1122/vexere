<?php

namespace DreamTeam\AdminUser\Repositories\Eloquent;

use DreamTeam\AdminUser\Repositories\Interfaces\AdminUserRoleRepositoryInterface;
use DreamTeam\AdminUser\Models\AdminUserRole;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AdminUserRoleRepository extends BaseRepository implements AdminUserRoleRepositoryInterface
{
    protected string|null|Model $model = AdminUserRole::class;
}
