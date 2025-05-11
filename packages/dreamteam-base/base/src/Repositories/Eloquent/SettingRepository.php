<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\Setting;
use DreamTeam\Base\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    protected string|null|Model $model = Setting::class;

}
