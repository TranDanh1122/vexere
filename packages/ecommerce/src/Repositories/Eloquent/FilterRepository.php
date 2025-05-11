<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\Filter;
use DreamTeam\Ecommerce\Repositories\Interfaces\FilterRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Base\Enums\BaseStatusEnum;

class FilterRepository extends BaseRepository implements FilterRepositoryInterface
{
    protected string|null|Model $model = Filter::class;

    public function getFilterByIds($filterIds)
    {
        return $this->getModel()
            ->with(['filterDetail' => function ($query) {
                $query->where('filter_details.status', BaseStatusEnum::ACTIVE);
            }])
            ->active()
            ->whereIn('id', $filterIds)
            ->orderBy('order', 'asc')
            ->get();
    }
}
