<?php

namespace DreamTeam\Base\Services\Interfaces;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    public function handleRelatedRecord($requests, string $table, int $tableId, bool $hasSeo = false, bool $hasLocale = false, bool $hasLog = true, string $logAction = null, array $logData = [], bool $hasSlug = false);
}
