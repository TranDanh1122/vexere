<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\ProductFilter;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductFilterRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductFilterRepository extends BaseRepository implements ProductFilterRepositoryInterface
{
    protected string|null|Model $model = ProductFilter::class;

    public function getProductIdByFilterIds($filterIds)
    {
        return $this->getModel()
            ->whereIn('filter_detail_id', $filterIds)
            ->select('product_id', DB::raw('count(*) as total'))
            ->groupBy('product_id')
            ->get();
    }

    public function deleteByFilterDetailId(array $filterDetailIds)
    {
        $this->getModel()
            ->whereIn('filter_detail_id', $filterDetailIds)
            ->delete();
    }
}
