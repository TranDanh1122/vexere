<?php

namespace DreamTeam\Base\Repositories\Interfaces;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface SeoRepositoryInterface extends BaseRepositoryInterface
{
	public function createMetaSeo(string $type, int|string $typeId, array $data) :Model;
}
