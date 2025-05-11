<?php

namespace DreamTeam\AdminUser\Repositories\Eloquent;

use DreamTeam\AdminUser\Repositories\Interfaces\AdminUserRepositoryInterface;
use DreamTeam\AdminUser\Models\AdminUser;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AdminUserRepository extends BaseRepository implements AdminUserRepositoryInterface
{
    protected string|null|Model $model = AdminUser::class;
}
