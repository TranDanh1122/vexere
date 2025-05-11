<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\PasswordReset;
use DreamTeam\Ecommerce\Repositories\Interfaces\PasswordResetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRepository extends BaseRepository implements PasswordResetRepositoryInterface
{
    protected string|null|Model $model = PasswordReset::class;

}
