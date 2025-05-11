<?php

namespace DreamTeam\Ecommerce\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;

interface ProductServiceInterface extends CrudServiceInterface
{
    public function deleteForever(int|string $productId, array $productLocation);

    public function rollbackFromLog(array $dataOld): void;

    public function saveProductSchedule(int $productId, array $data): void;

    public function getProductByConditions(array $data): LengthAwarePaginator|Collection;

    public function getProductsIdByFilterDetailIds($ilterDetailIds) :array;
}
