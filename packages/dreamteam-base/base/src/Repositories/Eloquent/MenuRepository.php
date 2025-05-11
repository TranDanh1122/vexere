<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\Menu;
use DreamTeam\Base\Repositories\Interfaces\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    protected string|null|Model $model = Menu::class;

}
