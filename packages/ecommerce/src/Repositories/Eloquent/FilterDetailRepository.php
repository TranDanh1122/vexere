<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\FilterDetail;
use DreamTeam\Ecommerce\Repositories\Interfaces\FilterDetailRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class FilterDetailRepository extends BaseRepository implements FilterDetailRepositoryInterface
{
    protected string|null|Model $model = FilterDetail::class;

    public function getFilterDetailBySlug($slugs)
    {
        return $this->getModel()
            ->whereIn('slug', $slugs)
            ->get();
    }
}
