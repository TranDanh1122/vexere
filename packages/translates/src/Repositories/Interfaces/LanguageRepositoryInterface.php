<?php

namespace DreamTeam\Translate\Repositories\Interfaces;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Translate\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use DreamTeam\Base\Repositories\Interfaces\BaseRepositoryInterface;

interface LanguageRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveLanguage(array $select = ['*']): Collection;

    public function getDefaultLanguage(array $select = ['*']): BaseModel|Model|Language|null;
}
