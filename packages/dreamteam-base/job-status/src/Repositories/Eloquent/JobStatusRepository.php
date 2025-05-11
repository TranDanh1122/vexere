<?php

namespace DreamTeam\JobStatus\Repositories\Eloquent;

use DreamTeam\JobStatus\Repositories\Interfaces\JobStatusRepositoryInterface;
use DreamTeam\JobStatus\Models\JobStatus;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class JobStatusRepository extends BaseRepository implements JobStatusRepositoryInterface
{

    protected string|null|Model $model = JobStatus::class;

}
