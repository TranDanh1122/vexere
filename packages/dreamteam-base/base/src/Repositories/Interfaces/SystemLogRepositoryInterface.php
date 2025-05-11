<?php

namespace DreamTeam\Base\Repositories\Interfaces;

use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface SystemLogRepositoryInterface extends BaseRepositoryInterface
{
	public function deleteWithRequest(Request $request);
}

