<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\Location;
use DreamTeam\Ecommerce\Repositories\Interfaces\LocationRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Base\Enums\BaseStatusEnum;

class LocationRepository extends BaseRepository implements LocationRepositoryInterface
{
    protected string|null|Model $model = Location::class;

    public function getLocationByCategoryIds($categoryIds, $productIdByCategoryMap)
    {
        return $this->getModel()
            ->with(['product' => function($query) use ($categoryIds, $productIdByCategoryMap) {
                return $query->where('status', BaseStatusEnum::ACTIVE)
                    ->where(function ($qr) use ($categoryIds, $productIdByCategoryMap) {
                        $qr->whereIn('category_id', $categoryIds)->orWhereIn('id', $productIdByCategoryMap);
                    });
            }])
            ->join('products', 'products.location_id', 'locations.id')
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.status', BaseStatusEnum::ACTIVE)
            ->where('locations.status', BaseStatusEnum::ACTIVE)
            ->select('locations.*')
            ->groupBy('locations.id')
            ->get();
    }

    public function getAllActive($lang = null)
    {
        $records = $this->getModel()->with(['product' => function($query) {
            return $query->where('status', BaseStatusEnum::ACTIVE);
        }]);
        if ($lang) {
            $records = $records->whereHas('language_metas', function($query) use ($lang) {
                return $query->where('lang_locale', $lang);
            });
        }
        $records = $records->join('products', 'products.location_id', 'locations.id')
            ->where('products.status', BaseStatusEnum::ACTIVE)
            ->where('locations.status', BaseStatusEnum::ACTIVE)
            ->select('locations.*')
            ->groupBy('locations.id')
            ->get();
        return $records;
    }

    public function getLocationWithCategory($data)
    {
        $categoryIds = $data['categoryIds'] ?? null;
        $cateSkip = $data['cateSkip'] ?? null;
        if (! $categoryIds) return null;

        if ($cateSkip) $categoryIds = array_diff($categoryIds, $cateSkip);

        return $this->getModel()
            ->join('products', 'products.location_id', 'locations.id')
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.status', BaseStatusEnum::ACTIVE)
            ->where('locations.status', BaseStatusEnum::ACTIVE)
            ->select('locations.*')
            ->distinct()
            ->get();
    }
}
