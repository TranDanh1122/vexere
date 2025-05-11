<?php

namespace DreamTeam\Ecommerce\Services;

use DreamTeam\Base\Services\CrudService;
use DreamTeam\Ecommerce\Repositories\Interfaces\FilterRepositoryInterface;
use DreamTeam\Ecommerce\Repositories\Interfaces\FilterDetailRepositoryInterface;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductFilterRepositoryInterface;
use DreamTeam\Ecommerce\Services\Interfaces\FilterServiceInterface;
use DreamTeam\Base\Enums\BaseStatusEnum;
use Illuminate\Database\Eloquent\Collection;

class FilterService extends CrudService implements FilterServiceInterface
{
    protected FilterDetailRepositoryInterface $filterDetailRepository;
    protected ProductFilterRepositoryInterface $productFilterRepository;   

    public function __construct (
        FilterRepositoryInterface $repository,
        FilterDetailRepositoryInterface $filterDetailRepository,
        ProductFilterRepositoryInterface $productFilterRepository
    )
    {
        $this->repository = $repository;
        $this->filterDetailRepository = $filterDetailRepository;
        $this->productFilterRepository = $productFilterRepository;
    }

    /**
     * @param array $filterDetails
     * @param int|string $filterId
     */
    public function storeFilterDetail(array $filterDetails, int|string $filterId): void
    {
        if (count($filterDetails)) {
            $detailDatas = [];
            $filterDetailSlugs = $this->filterDetailRepository->getAll(['slug'], 'id', 'asc')
                ->pluck('slug', 'slug')->toArray();
            foreach ($filterDetails as $value) {
                $slug = str_slug(trim($value));
                if (in_array($slug, $filterDetailSlugs)) {
                    $slug = $slug . '-ft'. $filterId. '-'. rand(2, 8);
                }
                $detailDatas[] = [
                    'filter_id' => $filterId,
                    'name' => trim($value),
                    'slug' => $slug,
                    'status' => BaseStatusEnum::ACTIVE,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            if (count($detailDatas)) {
                $this->filterDetailRepository->insertMultipleFromArray($detailDatas);
            }
        }
    }

    public function getFilterDetailByConditions(array $conditions): ?Collection
    {
        return $this->filterDetailRepository->getWithMultiFromConditions([], $conditions, 'order', 'asc');
    }

    public function deleteFilterDetailByConditions(array $conditions): void
    {
        $this->filterDetailRepository->deleteFromWhereCondition($conditions);
    }
    
    public function insertMultipleFilterDetail(array $data): void
    {
        $this->filterDetailRepository->insertMultipleFromArray($data);
    }

    public function setProductFilter(array $filters, int|string $productId): void
    {
        $this->productFilterRepository->deleteFromWhereCondition(['product_id' => $productId]);
        if (count($filters)) {
            $maps = [];
            foreach ($filters as $filterDetailId) {
                $maps[] = [
                    'product_id' => $productId,
                    'filter_detail_id' => $filterDetailId,
                ];
            }
            
            if (count($maps) > 0) {
                $this->productFilterRepository->insertMultipleFromArray($maps);
            }
        }
    }

    public function getProductFilterByConditions(array $conditions): ?Collection
    {
        return $this->productFilterRepository->findMultipleFromArray($conditions);
    }

    public function deleteProductFilterByConditions(array $conditions): void
    {
        $this->productFilterRepository->deleteFromWhereCondition($conditions);
    }
    
    public function insertMultipleProductFilter(array $data): void
    {
        $this->productFilterRepository->insertMultipleFromArray($data);
    }

    public function getProductFilterInDetail(array $filterDetailIds): ?Collection
    {
        return $this->productFilterRepository->getWithMultiFromConditions([], ['filter_detail_id' => ['IN' => $filterDetailIds]], 'filter_detail_id', 'asc');
    }

    public function deleteProductFilterInDetail(array $filterDetailIds): void
    {
        $this->productFilterRepository->deleteByFilterDetailId($filterDetailIds);
    }

    public function getProductCategoryFilterMapCruds(int|string $productCategoryId, int|string $productId): array
    {

        $filters = $this->repository->getWithMultiFromConditions([], [
                'status' => ['=' => BaseStatusEnum::ACTIVE]
            ], 'order', 'asc');
        $filterDetails = $this->filterDetailRepository->getWithMultiFromConditions([], [
                'status' => ['=' => BaseStatusEnum::ACTIVE]
            ], 'order', 'asc');
        $productFilters = $this->productFilterRepository->findMultipleFromArray(['product_id' => $productId ?? 0])
            ->pluck('product_id', 'filter_detail_id')->toArray();
        return compact('filters', 'filterDetails', 'productFilters');
    }

}