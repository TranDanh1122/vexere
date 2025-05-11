<?php

namespace DreamTeam\Ecommerce\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;

interface LocationServiceInterface extends CrudServiceInterface
{
    public function saveProductLocations(int $productId, array $data): array;
    public function getProductLocationsByProduct(int $productId): Collection;
    public function deleteProductLocationByProduct(int $productId): void;
    public function insertMultipleLocationFromArray(array $data): void;

}
