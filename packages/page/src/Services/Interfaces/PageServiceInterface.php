<?php

namespace DreamTeam\Page\Services\Interfaces;

use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Http\Request;
use DreamTeam\Page\Models\Page;

interface PageServiceInterface extends CrudServiceInterface
{
	public function getPage(Request $request, object $itemSlug, string $slug, string $fullSlug, string $device);
	public function getPageRecordByLangAndOriginId(string $lang, int $originId): Page|null;
}
