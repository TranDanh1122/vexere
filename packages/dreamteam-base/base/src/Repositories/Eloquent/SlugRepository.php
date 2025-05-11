<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\Slug;
use DreamTeam\Base\Repositories\Interfaces\SlugRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SlugRepository extends BaseRepository implements SlugRepositoryInterface
{
    protected string|null|Model $model = Slug::class;

}
