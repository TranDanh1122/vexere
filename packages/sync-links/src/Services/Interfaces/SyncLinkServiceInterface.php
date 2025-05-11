<?php

namespace DreamTeam\SyncLink\Services\Interfaces;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Http\Request;

interface SyncLinkServiceInterface extends CrudServiceInterface
{
	public function addLinkToSync(Request $requests, string $oldLink, string $newLink): void;
}
