<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\Currency;
use DreamTeam\Base\Repositories\Interfaces\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
{
    protected string|null|Model $model = Currency::class;

}
