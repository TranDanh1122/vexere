<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\TableOption;
use DreamTeam\Base\Repositories\Interfaces\TableOptionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TableOptionRepository extends BaseRepository implements TableOptionRepositoryInterface
{
    protected string|null|Model $model = TableOption::class;

}
