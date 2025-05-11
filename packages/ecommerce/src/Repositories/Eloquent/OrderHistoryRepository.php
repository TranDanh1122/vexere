<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\OrderHistory;
use DreamTeam\Ecommerce\Repositories\Interfaces\OrderHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OrderHistoryRepository extends BaseRepository implements OrderHistoryRepositoryInterface
{
    protected string|null|Model $model = OrderHistory::class;

}
