<?php

namespace DreamTeam\Page\Repositories\Interfaces;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PageRepositoryInterface extends BaseRepositoryInterface
{

    public function getAllPages(string|null $locale): Collection;
}
