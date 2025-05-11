<?php

namespace DreamTeam\Base\Services\Interfaces;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Database\Eloquent\Model;

interface SlugServiceInterface  extends CrudServiceInterface
{
    public function createOrUpdateSlug(string $tableName, int $tableId, string $slug) :Model;

    public function getSlugUniqueAuto(string $slugRoot, int $tableId): string;
}
