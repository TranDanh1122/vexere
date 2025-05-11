<?php

namespace DreamTeam\Base\Services\Interfaces;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Http\Request;

interface SystemLogServiceInterface  extends CrudServiceInterface
{
    public function saveLog(string $action, array $compact = [], string $type = '', string|int $type_id = '', string $idName = 'id');
    public function deleteWithRequest(Request $request);
}
