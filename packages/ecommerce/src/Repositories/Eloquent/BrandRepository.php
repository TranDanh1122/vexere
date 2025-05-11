<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\Brand;
use DreamTeam\Ecommerce\Repositories\Interfaces\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use DreamTeam\Base\Enums\BaseStatusEnum;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    protected string|null|Model $model = Brand::class;

    public function getBrandByCategoryIds($categoryIds, $productIdByCategoryMap)
    {
        return $this->getModel()
            ->with(['product' => function($query) use ($categoryIds, $productIdByCategoryMap) {
                return $query->where('status', BaseStatusEnum::ACTIVE)
                    ->where(function ($qr) use ($categoryIds, $productIdByCategoryMap) {
                        $qr->whereIn('category_id', $categoryIds)->orWhereIn('id', $productIdByCategoryMap);
                    });
            }])
            ->join('products', 'products.brand_id', 'brands.id')
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.status', BaseStatusEnum::ACTIVE)
            ->where('brands.status', BaseStatusEnum::ACTIVE)
            ->select('brands.*')
            ->groupBy('brands.id')
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
        $records = $records->join('products', 'products.brand_id', 'brands.id')
            ->where('products.status', BaseStatusEnum::ACTIVE)
            ->where('brands.status', BaseStatusEnum::ACTIVE)
            ->select('brands.*')
            ->groupBy('brands.id')
            ->get();
        return $records;
    }

    public function getBrandWithCategory($data)
    {
        $categoryIds = $data['categoryIds'] ?? null;
        $cateSkip = $data['cateSkip'] ?? null;
        if (! $categoryIds) return null;

        if ($cateSkip) $categoryIds = array_diff($categoryIds, $cateSkip);

        return $this->getModel()
            ->join('products', 'products.brand_id', 'brands.id')
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.status', BaseStatusEnum::ACTIVE)
            ->where('brands.status', BaseStatusEnum::ACTIVE)
            ->select('brands.*')
            ->distinct()
            ->get();
    }
}
