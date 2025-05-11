<?php

namespace DreamTeam\Ecommerce\Services;

use Illuminate\Database\Eloquent\Collection;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\CrudService;
use DreamTeam\Ecommerce\Enums\DirectionTypeEnum;
use DreamTeam\Ecommerce\Enums\LocationTypeEnum;
use DreamTeam\Ecommerce\Repositories\Interfaces\LocationRepositoryInterface;
use DreamTeam\Ecommerce\Repositories\Interfaces\ProductLocationRepositoryInterface;
use DreamTeam\Ecommerce\Services\Interfaces\LocationServiceInterface;

class LocationService extends CrudService implements LocationServiceInterface
{
    protected ProductLocationRepositoryInterface $productLocationRepository;

    public function __construct(
        LocationRepositoryInterface $repository,
        ProductLocationRepositoryInterface $productLocationRepository
    ) {
        $this->repository = $repository;
        $this->productLocationRepository = $productLocationRepository;
    }


    /**
     * Save product locations with time data
     *
     * @param int $productId
     * @param array $data
     * @return void
     */
    public function saveProductLocations(int $productId, array $data): array
    {
        $times = [
            'start_time_sg_vt' => null,
            'start_time_vt_sg' => null,
        ];
        // Delete existing product locations for this product
        $this->productLocationRepository->deleteFromWhereCondition(['product_id' => $productId]);
        $directionsgvt = $data['directionsgvt'] ?? [];
        $directionvtsg = $data['directionvtsg'] ?? [];

        // Process and save pickup/dropoff locations
        $locationData = [];
        // neu cô lich chay mới insert
        if (count($directionsgvt) > 0) {
            // Process pickup locations from Saigon to Vung Tau
            if (!empty($data['pickupsgvt'])) {
                foreach ($data['pickupsgvt'] as $index => $pickup) {
                    if (empty($times['start_time_sg_vt'])) {
                        $times['start_time_sg_vt'] = date('H:i:s', strtotime($pickup['time'] . ':00'));
                    }
                    $locationData[] = [
                        'product_id' => $productId,
                        'location_id' => $pickup['location_id'],
                        'time' => isset($pickup['time']) ? date('H:i:s', strtotime($pickup['time'] . ':00')) : null,
                        'type' => LocationTypeEnum::PICKUP,
                        'direction' => DirectionTypeEnum::SGVT,
                        'order' => $index,
                        'status' => BaseStatusEnum::ACTIVE,
                        'transit' => ($pickup['transit'] ?? '') === 'on' ? 1 : 0,
                    ];
                }
            }

            // Process dropoff locations from Saigon to Vung Tau
            if (!empty($data['droffsgvt'])) {
                foreach ($data['droffsgvt'] as $index => $dropoff) {
                    $locationData[] = [
                        'product_id' => $productId,
                        'location_id' => $dropoff['location_id'],
                        'time' => isset($dropoff['time']) ? date('H:i:s', strtotime($dropoff['time'] . ':00')) : null,
                        'type' => LocationTypeEnum::DROPOFF,
                        'direction' => DirectionTypeEnum::SGVT,
                        'order' => $index,
                        'status' => BaseStatusEnum::ACTIVE,
                        'transit' => ($dropoff['transit'] ?? '') === 'on' ? 1 : 0,
                    ];
                }
            }
        }
        // neu cô lich chay mới insert
        if (count($directionvtsg) > 0) {
            // Process pickup locations from Vung Tau to Saigon
            if (!empty($data['pickupvtsg'])) {
                foreach ($data['pickupvtsg'] as $index => $pickup) {
                    if (empty($times['start_time_vt_sg'])) {
                        $times['start_time_vt_sg'] = date('H:i:s', strtotime($pickup['time'] . ':00'));
                    }
                    $locationData[] = [
                        'product_id' => $productId,
                        'location_id' => $pickup['location_id'],
                        'time' => isset($pickup['time']) ? date('H:i:s', strtotime($pickup['time'] . ':00')) : null,
                        'type' => LocationTypeEnum::PICKUP,
                        'direction' => DirectionTypeEnum::VTSG,
                        'order' => $index,
                        'status' => BaseStatusEnum::ACTIVE,
                        'transit' => ($pickup['transit'] ?? '') === 'on' ? 1 : 0,
                    ];
                }
            }

            // Process dropoff locations from Vung Tau to Saigon
            if (!empty($data['droffvtsg'])) {
                foreach ($data['droffvtsg'] as $index => $dropoff) {
                    $locationData[] = [
                        'product_id' => $productId,
                        'location_id' => $dropoff['location_id'],
                        'time' => isset($dropoff['time']) ? date('H:i:s', strtotime($dropoff['time'] . ':00')) : null,
                        'type' => LocationTypeEnum::DROPOFF,
                        'direction' => DirectionTypeEnum::VTSG,
                        'order' => $index,
                        'status' => BaseStatusEnum::ACTIVE,
                        'transit' => ($dropoff['transit'] ?? '') === 'on' ? 1 : 0,
                    ];
                }
            }
        }

        // Create all product locations in a single batch
        if (!empty($locationData)) {
            $this->productLocationRepository->insertMultipleFromArray($locationData);
        }
        return $times;
    }
    /**
     * Get product locations by product ID
     *
     * @param int $productId
     * @return Collection
     */
    public function getProductLocationsByProduct(int $productId): Collection
    {
        return $this->productLocationRepository->getWithMultiFromConditions(
            [],
            ['product_id' => ['=' => $productId]],
            'order',
            'asc'
        );
    }
    /**
     * Delete product locations by product ID and type
     *
     * @param int $productId
     * @return void
     */
    public function deleteProductLocationByProduct(int $productId): void
    {
        $this->productLocationRepository->deleteFromWhereCondition(['product_id' => $productId]);
    }
    /**
     * Insert product locations by data
     *
     * @param array $data
     * @return void
     */
    public function insertMultipleLocationFromArray(array $data): void
    {
        $this->productLocationRepository->insertMultipleFromArray($data);
    }
}
