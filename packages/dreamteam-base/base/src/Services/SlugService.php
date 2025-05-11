<?php

namespace DreamTeam\Base\Services;

use DreamTeam\Base\Repositories\Interfaces\SlugRepositoryInterface;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;

class SlugService extends CrudService implements SlugServiceInterface
{

    public function __construct(
        SlugRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function createOrUpdateSlug(string $tableName, int $tableId, string $slug) :Model
    {
        $checkExits = $this->repository->findOneFromArray([
                'table'    => $tableName,
                'table_id' => $tableId,
            ], false);
        $created_at = $updated_at = date('Y-m-d H:i:s');
        if($checkExits) {
            return $this->repository->updateByPrimary($checkExits->id,
                    [
                        'slug' => $slug,
                        'updated_at' => $updated_at
                    ],
                    false
                );
        } else {
            return $this->repository->createFromArray([
                'table'      => $tableName,
                'table_id'   => $tableId,
                'slug'       => $slug,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            ]);
        }
    }

    public function getSlugUniqueAuto(string $slug, int $tableId): string
    {
        $conditions = [
            'slug' => ['=' => $slug],
            'table_id' => ['DFF' => $tableId],
        ];
        $i = 1;
        $slugResult = $slug;
        while ($this->findOneWhereFromConditions([], $conditions, 'id', 'desc', false, '*', false)) {
            $slugResult = $slug.'-'.$i;
            $conditions = ['slug' => ['=' => $slugResult]];
            $i++;
        }
        return $slugResult;
    }
}
