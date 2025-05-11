<?php

namespace DreamTeam\Ecommerce\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductRepositoryInterface;
use DreamTeam\Ecommerce\Services\Interfaces\ProductServiceInterface;
use DreamTeam\Base\Services\CrudService;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Ecommerce\Repositories\Interfaces\BrandRepositoryInterface;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductFilterRepositoryInterface;
use DreamTeam\Base\Enums\SystemLogStatusEnum;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductScheduleRepositoryInterface;
use DreamTeam\Ecommerce\Services\Interfaces\LocationServiceInterface;
use DreamTeam\Ecommerce\Enums\DirectionTypeEnum;

class ProductService extends CrudService implements ProductServiceInterface
{
    protected BrandRepositoryInterface $brandRepository;
    protected ProductFilterRepositoryInterface $productFilterRepository;
    protected ProductScheduleRepositoryInterface $productScheduleRepository;

    public function __construct(
        ProductRepositoryInterface $repository,
        BrandRepositoryInterface $brandRepository,
        ProductFilterRepositoryInterface $productFilterRepository,
        ProductScheduleRepositoryInterface $productScheduleRepository
    ) {
        $this->repository = $repository;
        $this->brandRepository = $brandRepository;
        $this->productFilterRepository = $productFilterRepository;
        $this->productScheduleRepository = $productScheduleRepository;
    }

    public function deleteForever(int|string $productId, array $productLocation)
    {
        $checkTrashRecord = $this->findOne(['id' => $productId, 'status' => BaseStatusEnum::DELETE], true, true);

        $conditions = ['product_id' => $productId];

        $filters = $this->productFilterRepository->getMultipleFromConditions([], $conditions, 'product_id');

        $data = $checkTrashRecord->toArray();
        $data['filters'] = $filters->toArray();
        $data['productLocation'] = $productLocation;
        // ghi log
        systemLogs(SystemLogStatusEnum::DELETE_FOREVER, $data, 'products', $productId);

        // xóa
        $this->productFilterRepository->deleteFromWhereCondition($conditions);

        $this->repository->deleteFromWhereCondition(['id' => $productId, 'status' => BaseStatusEnum::DELETE]);

        return true;
    }

    public function rollbackFromLog(array $dataOld): void
    {
        $filters = $dataOld['filters'] ?? [];
        if (isset($dataOld['filters'])) unset($dataOld['filters']);
        $productLocation = $dataOld['productLocation'] ?? [];
        if (isset($dataOld['productLocation'])) unset($dataOld['productLocation']);
        $this->repository->insertMultipleFromArray($dataOld);

        if (count($filters)) {
            $this->productFilterRepository->insertMultipleFromArray($filters);
        }
        if (count($productLocation)) {
            app(LocationServiceInterface::class)->insertMultipleLocationFromArray($productLocation);
        }
    }

    public function getThumbnailImages(): array
    {
        $productMedias =  $this->repository->findMultipleFromArray([], false, 'image, slide');
        $productVrMedias =  $this->productVariantRepository->findMultipleFromArray([], false, 'image')->pluck('image')->toArray();
        $productImages = [];
        foreach ($productMedias as $productMd) {
            if (!empty($productMd->slide)) {
                $slides = array_filter(explode(',', $productMd->slide));
                $productImages = array_merge($productImages, $slides);
            }
            if (!empty($productMd->image)) {
                $productImages[] = $productMd->image;
            }
        }
        $productImages = array_merge($productImages, $productVrMedias);
        return array_filter($productImages);
    }

    public function saveProductSchedule(int $productId, array $data): void
    {
        $directionsgvt = $data['directionsgvt'] ?? [];
        $directionvtsg = $data['directionvtsg'] ?? [];
        $this->productScheduleRepository->deleteFromWhereCondition(['product_id' => $productId]);
        if (count($directionsgvt)) {
            $dataInsert = [
                'product_id' => $productId,
                'direction' => DirectionTypeEnum::SGVT,
                'monday' => in_array('T2', $directionsgvt) ? 1 : 0,
                'tuesday' => in_array('T3', $directionsgvt) ? 1 : 0,
                'wednesday' => in_array('T4', $directionsgvt) ? 1 : 0,
                'thursday' => in_array('T5', $directionsgvt) ? 1 : 0,
                'friday' => in_array('T6', $directionsgvt) ? 1 : 0,
                'saturday' => in_array('T7', $directionsgvt) ? 1 : 0,
                'sunday' => in_array('CN', $directionsgvt) ? 1 : 0,
                'time' => !empty($data['directionsgvt_time'] ?? null) ? date('H:i:s', strtotime(($data['directionsgvt_time'] ?? null) . ':00')) : null,
                'time_run' => $data['time_run'] ?? null,
            ];
            $this->productScheduleRepository->createFromArray($dataInsert);
        }
        if (count($directionvtsg)) {
            $dataInsert = [
                'product_id' => $productId,
                'direction' => DirectionTypeEnum::VTSG,
                'monday' => in_array('T2', $directionvtsg) ? 1 : 0,
                'tuesday' => in_array('T3', $directionvtsg) ? 1 : 0,
                'wednesday' => in_array('T4', $directionvtsg) ? 1 : 0,
                'thursday' => in_array('T5', $directionvtsg) ? 1 : 0,
                'friday' => in_array('T6', $directionvtsg) ? 1 : 0,
                'saturday' => in_array('T7', $directionvtsg) ? 1 : 0,
                'sunday' => in_array('CN', $directionvtsg) ? 1 : 0,
                'time' => !empty($data['directionvtsg_time'] ?? null) ? date('H:i:s', strtotime(($data['directionvtsg_time'] ?? null) . ':00')) : null,
                'time_run' => $data['time_run'] ?? null,
            ];
            $this->productScheduleRepository->createFromArray($dataInsert);
        }
    }

    public function getProductByConditions(array $data): LengthAwarePaginator|Collection
    {
        return $this->productScheduleRepository->getProductByConditions($data);
    }

    public function getProductsIdByFilterDetailIds($ilterDetailIds) :array
    {
        if (!$ilterDetailIds) return [];

        return $this->productFilterRepository
            ->getProductIdByFilterIds($ilterDetailIds)
            ->filter(function ($item) use ($ilterDetailIds) {
                return $item->total == count($ilterDetailIds);
            })
            ->pluck('product_id')
            ->toArray();
    }
}
