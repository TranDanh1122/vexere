<?php

namespace DreamTeam\Base\Repositories\Interfaces;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface LanguageMetaRepositoryInterface extends BaseRepositoryInterface
{
	public function getMapRecordsWithId(int $tableId, string $tableName): ?Collection;
}
