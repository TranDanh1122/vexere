<?php

namespace DreamTeam\SyncLink\Repositories\Eloquent;

use DreamTeam\SyncLink\Repositories\Interfaces\SyncLinkRepositoryInterface;
use DreamTeam\SyncLink\Models\SyncLink;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SyncLinkRepository extends BaseRepository implements SyncLinkRepositoryInterface
{

    protected string|null|Model $model = SyncLink::class;

}
