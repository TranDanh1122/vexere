<?php

namespace DreamTeam\Ecommerce\Services\Interfaces;

use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Database\Eloquent\Collection;

interface FilterServiceInterface extends CrudServiceInterface
{
	public function storeFilterDetail(array $filterDetails, int|string $filterId): void;

    public function getFilterDetailByConditions(array $conditions): ?Collection;

    public function deleteFilterDetailByConditions(array $conditions): void;
    
    public function insertMultipleFilterDetail(array $data): void;

    public function setProductFilter(array $filters, int|string $productId): void;

    public function getProductFilterByConditions(array $conditions): ?Collection;

    public function deleteProductFilterByConditions(array $conditions): void;
    
    public function insertMultipleProductFilter(array $data): void;

    public function getProductFilterInDetail(array $filterDetailIds): ?Collection;

    public function deleteProductFilterInDetail(array $filterDetailIds): void;

    public function getProductCategoryFilterMapCruds(int|string $productCategoryId, int|string $productId): array;
}
