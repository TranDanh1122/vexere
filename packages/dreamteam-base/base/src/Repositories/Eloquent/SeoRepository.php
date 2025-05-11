<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\Seo;
use DreamTeam\Base\Repositories\Interfaces\SeoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SeoRepository extends BaseRepository implements SeoRepositoryInterface
{
    protected string|null|Model $model = Seo::class;

    public function createMetaSeo(string $type, int|string $typeId, array $data) :Model
    {
        $checkExits = $this->getModel()
            ->where('type', $type)
            ->where('type_id', $typeId)->first();

        if($checkExits) {
            $this->getModel()
                ->where('type', $type)
                ->where('type_id', $typeId)->update($data);
            return $checkExits;
        } else {
            return $this->getModel()->create($data);
        }
    }
}
