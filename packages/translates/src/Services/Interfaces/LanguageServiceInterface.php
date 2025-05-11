<?php

namespace DreamTeam\Translate\Services\Interfaces;

use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;
use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Translate\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface LanguageServiceInterface extends CrudServiceInterface
{

	public function getActiveLanguage(array $select = ['*']): Collection;

	public function getDefaultLanguage(array $select = ['*']): BaseModel|Model|Language|null;
}
