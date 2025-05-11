<?php

namespace DreamTeam\Base\Services\Interfaces;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Database\Eloquent\Model;

interface SeoServiceInterface  extends CrudServiceInterface
{
    public function createMetaSeo($requests, string $type, int|string $typeId) :Model;
}
