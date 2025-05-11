<?php

namespace DreamTeam\Base\Services\Interfaces;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Database\Eloquent\Model;

interface LanguageMetaServiceInterface  extends CrudServiceInterface
{
    public function createLangMeta($requests, string $langTable, int $langTableId) :Model;
}
