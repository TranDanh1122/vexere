<?php

namespace DreamTeam\Ecommerce\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Ecommerce\Models\ProductSchedule;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use DreamTeam\Ecommerce\Services\Interfaces\ProductServiceInterface;

class ProductScheduleRepository extends BaseRepository implements ProductScheduleRepositoryInterface
{
    protected string|null|Model $model = ProductSchedule::class;

    public function getProductByConditions(array $data): LengthAwarePaginator|Collection
    {
        $direction = $data['direction'];
        $directionTime = $data['directionTime'];
        $day = $data['day'];
        $date = $data['date'];
        $isCurrentDay = $data['isCurrentDay'] ?? false;

        $records = $this->getModel()
            ->with('product.productLocations', 'product.brand')
            ->where('direction', $direction)
            ->where($day, 1)
            ->whereHas('product', function ($query) use ($data) {
                $query->where('products.status', 1);
            });
            
        if (count($data['times'] ?? [])) {
            $records = $records->whereBetween('time', $data['times']);
        }

        if (count($data['prices'] ?? [])) {
            $records = $records->whereHas('product', function ($query) use ($data) {
                $query->whereBetween('price', $data['prices']);
            });;
        }

        if ($data['brands'] ?? null) {
            $records = $records->whereHas('product', function ($query) use ($data) {
                $query->whereIn('products.brand_id', $data['brands']);
            });
        }

        if (count($data['fillters'] ?? [])) {
            $productIds = app(ProductServiceInterface::class)->getProductsIdByFilterDetailIds($data['fillters']);
            $records = $records->whereHas('product', function ($query) use ($productIds) {
                $query->whereIn('products.id', $productIds);
            });
        }

        if ($isCurrentDay) {
            $records = $records->whereHas('product', function ($query) use ($directionTime) {
                $query->where($directionTime, '>', date('H:i:s'));
            });
        }

        if (count($data['locationCheckedStarts'] ?? [])) {
            $records = $records->whereHas('product.productLocations', function ($query) use ($data) {
                $query->where('direction', $data['direction'])
                ->whereIn('product_locations.location_id', $data['locationCheckedStarts'])
                ->where('product_locations.type', 'pickup');
            });
        }
        if (count($data['locationCheckedReturns'] ?? [])) {
            $records = $records->whereHas('product.productLocations', function ($query) use ($data) {
                $query->where('direction', $data['direction'])
                    ->whereIn('product_locations.location_id', $data['locationCheckedReturns'])
                    ->where('product_locations.type', 'dropoff');
            });
        }

        // if ($request->onsale) {
        //     $records = $records->where('products.price_old', '>', 0);
        // }

        // if ($price = $request->price) {
        //     $price = explode('_', $price);
        //     $records = $records->whereBetween('products.price', $price);
        // }

        // if ($request->filter) {
        //     $records = $records->whereIn('products.id', $request->productsIdByFilterSlugs ?? []);
        // }

        // if (!empty($search)) {
        //     $records = $records->orderByRaw($search);
        // } else {
        //     $records = $records->orderBy('updated_at', 'desc');
        // }

        return $records->paginate(20)->withQueryString();
    }

}